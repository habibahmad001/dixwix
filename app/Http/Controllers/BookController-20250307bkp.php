<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MailService;
use App\Models\Book;
use App\Models\Entries;
use App\Models\Group;
use App\Models\Groupmember;
use App\Models\Grouptype;
use App\Models\Importedfile;
use App\Models\ItemRejectedRequest;
use App\Models\LoanHistory;
use App\Models\LoanRule;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TrustScore;
use App\Models\Type;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use LaravelQRCode\Facades\QRCode;

class BookController extends Controller
{
    public function AddBook(Request $request, $id = null)
    {
        $retdata         = [];
        $retdata["mode"] = "add";
        if (isset($id)) {
            $book               = Book::find($id);
            $retdata["book"]    = $book;
            $retdata["mode"]    = "edit";
            $retdata["book_id"] = $id;
        } else {
            $user = auth()->user();
            if (count($user->membership) > 0) {
                if (count($user->createditems) >= $user->membership[0]->plan->allowed_items) {
                    $retdata["item_limit_reached"] = true;
                }
            }
            $retdata['rand_item_id'] = generateUniqueId('book', 'item_id', 12);
        }

        return $this->ReturnToAddPage($retdata);
    }

    public function StoreBook(Request $request)
    {
        $retdata                      = [];
        $data                         = $request->input('book');
        $mode                         = $request->input('mode');
        $book_id                      = $request->input('book_id');
        $retdata['mode']              = $mode;
        $retdata['book_id']           = $book_id;
        $book_rent_percent            = Setting::where('name', 'book_rent_price_percentage')->first()->value;
        $retdata["book_rent_percent"] = (int) $book_rent_percent;
        if (! $request->hasFile('cover_page')) {
            if ($mode == "add") {
                $message                               = "Cover Page Must be uploaded!";
                $retdata['book']                       = $data;
                $retdata['book']['cover_page']         = "";
                $retdata['errs']['book']['cover_page'] = $message;
                $retdata['err_message']                = $message;
                if (! isset($request->input('book')['status'])) {
                    $retdata['book']['status'] = 0;
                }
                return $this->ReturnToAddPage($retdata);
            }
        } else {
            $path               = $request->file('cover_page')->store('cover_pages');
            $data["cover_page"] = $path;
        }
        $book  = null;
        $model = new Book();

        if ($mode == "add") {
            $data["added_date"] = date("Y-m-d");
            $data["created_at"] = date("Y-m-d H:i:s");
            $data["barcode"]    = $data["name"] . ' - ' . $data['writers'];
            if (! isset($request->input('book')['status'])) {
                $data['status'] = 0;
            }
            // $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            // $image = $generator->getBarcode($data["barcode"], $generator::TYPE_CODE_128_B,3,150);

            $file_path = 'barcodes/' . date("YmdHis") . '.png';
            $save_path = 'storage/' . $file_path;

            $uploader = User::find(Auth::user()->id);
            \QRCode::text($data['name'] . " - " . $uploader->name . " - " . $uploader->address)->setOutfile($save_path)->png();
            // Storage::put($file_path, $image);
            $data["barcode_url"] = Storage::disk('local')->url($file_path);
            $data["created_by"]  = Auth::user()->id;
            $book                = $model->add($data);
        } else if ($mode == "edit") {
            if (! isset($request->input('book')['status'])) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }
            $data["id"] = $book_id;
            $book       = $model->change($data, $book_id);
        }

        if (! is_object($book)) {
            if ($mode == "edit") {
                $errors = \App\Message\Error::get('book.change');
            } else {
                $errors = \App\Message\Error::get('book.add');
            }
            if (count($errors) == 0) {
                if ($mode == "edit") {
                    $errors = \App\Message\Error::get('book.change');
                } else {
                    $errors = \App\Message\Error::get('book.add');
                }
            }
        }
        if (isset($errors) && count($errors) > 0) {
            $message                       = returnErrorMsg($errors);
            $retdata['errs']               = $errors;
            $retdata['book']               = $data;
            $retdata['book']['cover_page'] = "";
            $retdata['err_message']        = $message;
            // $this->flashError($retdata['err_message']);
            return $this->ReturnToAddPage($retdata);
        }
        if ($mode == "add") {
            for ($i = 0; $i < $data['copies']; $i++) {
                $entry_data = [
                    // 'group_type_id' => $data['group_type_id'],
                    'name'       => $data["name"] . " (Copy " . $i + 1 . ")",
                    'created_by' => Auth::user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                $book->entries()->create($entry_data);
            }
        }
        $retdata["success"] = "Item " . ucfirst($mode) . "ed successfully";
        return $this->ReturnToAddPage($retdata);
    }

    public function ReturnToAddPage($retdata = [])
    {
        $data['title']       = ucfirst($retdata["mode"]) . ' Item';
        $data['template']    = 'book.add';
        $data['script_file'] = 'add_item';

        $userId = $retdata['mode'] === 'edit' && isset($retdata['book']['created_by'])
        ? $retdata['book']['created_by']
        : auth()->user()->id;

        $data['groups'] = Group::where('created_by', $userId)
            ->orWhereHas('groupmembers', function ($q) use ($userId) {
                $q->where('member_id', $userId)
                    ->where('status', 'added')
                    ->where('activated', '1');
            })
            ->get();

        $data['types']       = Type::get();
        $data['group_types'] = Grouptype::get();
        return view('with_login_common', compact('data', 'retdata'));
    }

    private function parseGoogleSheetUrl($url)
    {
        $pattern = '/spreadsheets\/d\/([a-zA-Z0-9-_]+).*?(?:[?&#]gid=([0-9]+))?/';

        preg_match($pattern, $url, $matches);

        return [
            'sheet_id' => $matches[1] ?? null,
            'gid'      => isset($matches[2]) ? (int) $matches[2] : 0,
        ];
    }

    /*public function ImportFromCSV(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            "group_id"      => "required|exists:group,id",
            "type_id"       => "required|exists:item_type,id",
            "sale_or_rent"  => "required|in:sale,rent",
            "locations"     => "required|array",
            "import_method" => "required|in:file,url",
            "csv_file"      => "nullable|required_if:import_method,file|mimes:csv,txt",
            "csv_url"       => "nullable|required_if:import_method,url|url",
        ]);

        $category   = Type::findOrFail($request->type_id);
        $percentage = $category->percentage;

        $filePath = null;

        if ($request->import_method === 'file') {
            $filePath = $request->file('csv_file')->store('imported_csvs');
        } elseif ($request->import_method === 'url') {
            $csvUrl = $request->input('csv_url');

            if (str_contains($csvUrl, 'docs.google.com/spreadsheets')) {

                $parsedUrl = $this->parseGoogleSheetUrl($csvUrl);

                if (! $parsedUrl['sheet_id'] || $parsedUrl['gid'] === null) {
                    return back()->withErrors(['csv_url' => 'Invalid Google Sheets URL format.']);
                }

                $exportUrl = "https://docs.google.com/spreadsheets/d/{$parsedUrl['sheet_id']}/export?format=csv&gid={$parsedUrl['gid']}";

                $response = Http::get($exportUrl);

                if ($response->status() != 200) {
                    return back()->withErrors(['csv_url' => 'Failed to fetch data from the Google Sheet. Ensure it is publicly accessible.']);
                }

                $filePath = 'imported_csvs/google_sheet_' . date('Y_m_d_h_i_s_A') . "_" . uniqid() . '.csv';
                Storage::put($filePath, $response->body());
            } elseif (filter_var($csvUrl, FILTER_VALIDATE_URL)) {
                $response = Http::get($csvUrl);

                $validMimeTypes = ['text/csv', 'application/csv', 'application/octet-stream'];
                $fileExtension  = pathinfo(parse_url($csvUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

                if ($response->status() !== 200 ||
                    (! in_array($response->header('Content-Type'), $validMimeTypes) && ! in_array(strtolower($fileExtension), ['csv', 'txt']))
                ) {
                    return back()->withErrors(['csv_url' => 'Invalid file URL or the file is not a CSV.']);
                }

                $filePath = 'imported_csvs/direct_url' . date('Y_m_d_h_i_s_A') . "_" . uniqid() . '.csv';
                Storage::put($filePath, $response->body());
            } else {
                return back()->withErrors(['csv_url' => 'Unsupported URL format. Only Google Sheets or direct CSV file URLs are allowed.']);
            }
        }

        if (! $filePath) {
            return back()->withErrors(['csv_url' => 'Failed to process the file.']);
        }

        $uploader = Auth::user();

        DB::beginTransaction();

        try {

            Importedfile::create(["path" => $filePath, "created_by" => $uploader->id]);

            $csvs_data = csvToArray(Storage::path($filePath));
            $csvGroup  = Group::findOrFail($request->group_id);
            $errors    = [];
            $rowCount  = 0;

            foreach ($csvs_data as $csv_data) {
                if (is_null($csv_data[""]) || empty($csv_data[""])) {
                    unset($csv_data[""]);
                }

                $rowCount++;
                $csv_data["group_id"]   = $request->group_id;
                $csv_data["type_id"]    = $request->type_id;
                $csv_data["ref_type"]   = "csv_import";
                $csv_data["added_date"] = now()->format('Y-m-d');
                $csv_data["created_by"] = $uploader->id;
                $csv_data["barcode"]    = date("YmdHis");
                $csv_data["pages"]      = isset($csv_data["pages"]) ? (int) $csv_data["pages"] : null;

                $file_path = 'barcodes/' . date("YmdHis") . "_CSV_" . $rowCount . '.png';
                $save_path = storage_path("app/public/{$file_path}");

                $qr_text = $csv_data['name'] . " - {$uploader->email} - {$uploader->address}";
                if (! empty($request->locations)) {
                    $locText = implode(", ", $request->locations);
                    $qr_text .= " - Locations: {$locText}";
                }

                QRCode::text($qr_text)->setOutfile($save_path)->png();

                $csv_data["barcode_url"] = "storage/{$file_path}";

                $validator = Validator::make($csv_data, [
                    'name'             => 'required|string',
                    'description'      => 'required',
                    'writers'          => 'required|string',
                    'year'             => 'nullable|integer',
                    'pages'            => 'nullable|numeric',
                    'journal_name'     => 'nullable|string',
                    'ean_isbn_no'      => 'nullable|string|min:10|max:13',
                    'upc_isbn_no'      => 'nullable|string',
                    'copies'           => 'required|numeric|min:1',
                    'cover_image_path' => 'required|string|url',
                    'price'            => 'required|numeric',
                ]);

                $validator->sometimes(['ean_isbn_no', 'writers', 'pages'], 'required', function ($input) use ($category) {
                    return strtolower($category->name) === 'book';
                });

                if ($validator->fails()) {

                    $fieldErrors = [];
                    foreach ($validator->errors()->messages() as $field => $messages) {
                        $fieldErrors[] = [
                            'field' => $field,
                            'value' => $csv_data[$field] ?? null,
                            'error' => implode(', ', $messages),
                        ];
                    }

                    $errors[] = [
                        'row'          => $rowCount,
                        'field_errors' => $fieldErrors,
                    ];
                    continue;
                }

                try {
                    $coverImagePath = $csv_data['cover_image_path'];

                    if (filter_var($coverImagePath, FILTER_VALIDATE_URL)) {

                        $imageContent = file_get_contents($coverImagePath);
                        if ($imageContent != false) {

                            $fileInfo  = pathinfo($coverImagePath);
                            $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : 'jpg';

                            $imageName = uniqid() . '_' . $fileInfo['filename'] . '.' . $extension;

                            $relativePath = "cover_pages/{$imageName}";
                            Storage::disk('public')->put($relativePath, $imageContent);
                            $csv_data['cover_image_path'] = $relativePath;
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = [
                        'row'          => $rowCount,
                        'field_errors' => [
                            [
                                'field' => 'cover_image_path',
                                'value' => $coverImagePath,
                                'error' => $e->getMessage(),
                            ],
                        ],
                    ];
                    continue;
                }



                $book = Book::create([
                    'item_id'        => generateUniqueId('book', 'item_id', 12),
                    'group_id'       => $csv_data['group_id'],
                    'type_id'        => $csv_data['type_id'],
                    'ref_type'       => "csv_import",
                    'added_date'     => $csv_data['added_date'],
                    'created_by'     => $csv_data['created_by'],
                    'barcode_url'    => $csv_data['barcode_url'],
                    'locations'      => json_encode($request->locations),
                    'name'           => $csv_data['name'],
                    'description'    => $csv_data['description'],
                    'writers'        => $csv_data['writers'],
                    'year'           => $csv_data['year'],
                    'pages'          => $csv_data['pages'],
                    'status_options' => "maintenance",
                    'sale_or_rent'   => $request->sale_or_rent,
                    'journal_name'   => $csv_data['journal_name'],
                    'ean_isbn_no'    => $csv_data['ean_isbn_no'],
                    'upc_isbn_no'    => $csv_data['upc_isbn_no'],
                    'copies'         => $csv_data['copies'],
                    'group_type_id'  => $csvGroup->group_type_id,
                    'cover_page'     => $csv_data['cover_image_path'],
                    'price'          => $csv_data['price'],
                    'rent_price'     => $csv_data['price'] * $percentage / 100,
                ]);

                for ($i = 0; $i < $csv_data['copies']; $i++) {
                    $book->entries()->create([
                        'name'       => $book->name . " (Copy " . $i + 1 . ")",
                        'created_by' => $uploader->id,
                    ]);
                }
            }

            if (isset($errors) && count($errors) > 0) {
                DB::rollBack();
                return back()->with('csv_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Items successfully imported.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', "An error occurred during the import process. Please try again.");
        }
    }*/

    public function ImportFromCSV(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        // Validate the incoming request
        $request->validate([
            "group_id"      => "required|exists:group,id",
            "type_id"       => "required|exists:item_type,id",
            "sale_or_rent"  => "required|in:sale,rent",
            "locations"     => "required|array",
            "import_method" => "required|in:file,url",
            "csv_file"      => "nullable|required_if:import_method,file|mimes:csv,txt",
            "csv_url"       => "nullable|required_if:import_method,url|url",
        ]);

        $category   = Type::findOrFail($request->type_id);
        $percentage = $category->percentage;

        $filePath = null;

        // Handle file upload or URL-based CSV import
        if ($request->import_method === 'file') {
            $filePath = $request->file('csv_file')->store('imported_csvs');
        } elseif ($request->import_method === 'url') {
            $csvUrl = $request->input('csv_url');

            if (str_contains($csvUrl, 'docs.google.com/spreadsheets')) {
                $parsedUrl = $this->parseGoogleSheetUrl($csvUrl);

                if (!$parsedUrl['sheet_id'] || $parsedUrl['gid'] === null) {
                    return back()->withErrors(['csv_url' => 'Invalid Google Sheets URL format.']);
                }

                $exportUrl = "https://docs.google.com/spreadsheets/d/{$parsedUrl['sheet_id']}/export?format=csv&gid={$parsedUrl['gid']}";
                $response = Http::get($exportUrl);

                if ($response->status() != 200) {
                    return back()->withErrors(['csv_url' => 'Failed to fetch data from the Google Sheet. Ensure it is publicly accessible.']);
                }

                $filePath = 'imported_csvs/google_sheet_' . date('Y_m_d_h_i_s_A') . "_" . uniqid() . '.csv';
                Storage::put($filePath, $response->body());
            } elseif (filter_var($csvUrl, FILTER_VALIDATE_URL)) {
                $response = Http::get($csvUrl);

                $validMimeTypes = ['text/csv', 'application/csv', 'application/octet-stream'];
                $fileExtension  = pathinfo(parse_url($csvUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

                if ($response->status() !== 200 ||
                    (!in_array($response->header('Content-Type'), $validMimeTypes) && !in_array(strtolower($fileExtension), ['csv', 'txt']))
                ) {
                    return back()->withErrors(['csv_url' => 'Invalid file URL or the file is not a CSV.']);
                }

                $filePath = 'imported_csvs/direct_url' . date('Y_m_d_h_i_s_A') . "_" . uniqid() . '.csv';
                Storage::put($filePath, $response->body());
            } else {
                return back()->withErrors(['csv_url' => 'Unsupported URL format. Only Google Sheets or direct CSV file URLs are allowed.']);
            }
        }

        if (!$filePath) {
            return back()->withErrors(['csv_url' => 'Failed to process the file.']);
        }


        $uploader = Auth::user();

        DB::beginTransaction();

        try {
            Importedfile::create([
                "path" => $filePath,
                "created_by" => $uploader->id
            ]);

            // Convert CSV to array
            $csvs_data = csvToArray(Storage::path($filePath));
            $csvGroup  = Group::findOrFail($request->group_id);
            $errors    = [];
            $rowCount  = 0;

            $updts=[];

            foreach ($csvs_data as $csv_data2) {
                if (isset($csv_data2['name']) && trim($csv_data2['name'])!="") {
                    unset($csv_data2[""]);
                    $d2=mb_convert_encoding($csv_data2['name'], 'UTF-8', 'UTF-8');
                    $d3=mb_convert_encoding($csv_data2['description'], 'UTF-8', 'UTF-8');
                    $d4=mb_convert_encoding($csv_data2['writers'], 'UTF-8', 'UTF-8');
                    $d5=mb_convert_encoding($csv_data2['journal_name'], 'UTF-8', 'UTF-8');

                    $csv_data2['name']=$d2;
                    $csv_data2['description']=$d3;
                    $csv_data2['writers']=$d4;
                    $csv_data2['journal_name']=$d5;
                    $updts[]=$csv_data2;
                }else{continue;}
            }
            $csvs_data=$updts;

            // echo "<pre>";
            // print_r($csvs_data);die;
            foreach ($csvs_data as $csv_data) {
                // Remove empty keys
                $csv_data = array_filter($csv_data, function($value) {
                    return !is_null($value) && $value !== "";
                });

                $rowCount++;
                $csv_data["group_id"]   = $request->group_id;
                $csv_data["type_id"]    = $request->type_id;
                $csv_data["ref_type"]   = "csv_import";
                $csv_data["added_date"] = now()->format('Y-m-d');
                $csv_data["created_by"] = $uploader->id;
                $csv_data["barcode"]    = date("YmdHis");
                $csv_data["pages"]      = isset($csv_data["pages"]) ? (int) $csv_data["pages"] : null;

                // QR Code Generation
                $file_path = 'barcodes/' . date("YmdHis") . "_CSV_" . $rowCount . '.png';
                $save_path = storage_path("app/public/{$file_path}");

                $qr_text = $csv_data['name'] . " - {$uploader->email} - {$uploader->address}";
                if (!empty($request->locations)) {
                    $locText = implode(", ", $request->locations);
                    $qr_text .= " - Locations: {$locText}";
                }

                QRCode::text($qr_text)->setOutfile($save_path)->png();

                $csv_data["barcode_url"] = "storage/{$file_path}";

                // Validate CSV data
                $validator = Validator::make($csv_data, [
                    'name'             => 'required|string',
                    'description'      => 'required',
                    'writers'          => 'required|string',
                    'year'             => 'nullable|integer',
                    'pages'            => 'nullable|numeric',
                    'journal_name'     => 'nullable|string',
                    'ean_isbn_no'      => 'nullable|string|min:10|max:13',
                    'upc_isbn_no'      => 'nullable|string',
                    'copies'           => 'required|numeric|min:1',
                    'cover_image_path' => 'required|string|url',
                    'price'            => 'required|numeric',
                ]);

                $validator->sometimes(['ean_isbn_no', 'writers', 'pages'], 'required', function ($input) use ($category) {
                    return strtolower($category->name) === 'book';
                });

                if ($validator->fails()) {
                    $fieldErrors = [];
                    foreach ($validator->errors()->messages() as $field => $messages) {
                        $fieldErrors[] = [
                            'field' => $field,
                            'value' => $csv_data[$field] ?? null,
                            'error' => implode(', ', $messages),
                        ];
                    }

                    $errors[] = [
                        'row'          => $rowCount,
                        'field_errors' => $fieldErrors,
                    ];
                    continue;
                }

                try {
                    // Handle cover image upload
                    $coverImagePath = $csv_data['cover_image_path'];

                    if (filter_var($coverImagePath, FILTER_VALIDATE_URL)) {
                        $imageContent = file_get_contents($coverImagePath);
                        if ($imageContent !== false) {
                            $fileInfo  = pathinfo($coverImagePath);
                            $extension = $fileInfo['extension'] ?? 'jpg';
                            $imageName = uniqid() . '_' . $fileInfo['filename'] . '.' . $extension;
                            $relativePath = "cover_pages/{$imageName}";
                            Storage::disk('public')->put($relativePath, $imageContent);
                            $csv_data['cover_image_path'] = $relativePath;
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = [
                        'row'          => $rowCount,
                        'field_errors' => [
                            [
                                'field' => 'cover_image_path',
                                'value' => $coverImagePath,
                                'error' => $e->getMessage(),
                            ],
                        ],
                    ];
                    continue;
                }

                // Create Book entry
                $book = Book::create([
                    'item_id'        => generateUniqueId('book', 'item_id', 12),
                    'group_id'       => $csv_data['group_id'],
                    'type_id'        => $csv_data['type_id'],
                    'ref_type'       => "csv_import",
                    'added_date'     => $csv_data['added_date'],
                    'created_by'     => $csv_data['created_by'],
                    'barcode_url'    => $csv_data['barcode_url'],
                    'locations'      => json_encode($request->locations),
                    'name'           => $csv_data['name'],
                    'description'    => $csv_data['description'],
                    'writers'        => $csv_data['writers'],
                    'year'           => $csv_data['year'],
                    'pages'          => $csv_data['pages'],
                    'status_options' => "maintenance",
                    'sale_or_rent'   => $request->sale_or_rent,
                    'journal_name'   => $csv_data['journal_name'],
                    'ean_isbn_no'    => $csv_data['ean_isbn_no'],
                    'upc_isbn_no'    => $csv_data['upc_isbn_no'],
                    'copies'         => $csv_data['copies'],
                    'group_type_id'  => $csvGroup->group_type_id,
                    'cover_page'     => $csv_data['cover_image_path'],
                    'price'          => $csv_data['price'],
                    'rent_price'     => $csv_data['price'] * $percentage / 100,
                ]);

                // Create book entries for each copy
                for ($i = 0; $i < $csv_data['copies']; $i++) {
                    $book->entries()->create([
                        'name'       => $book->name . " (Copy " . ($i + 1) . ")",
                        'created_by' => $uploader->id,
                    ]);
                }
            }

            if (isset($errors) && count($errors) > 0) {
                DB::rollBack();
                return back()->with('csv_errors', $errors);
            }

            DB::commit();
            return back()->with('success', "Items successfully imported.");

        } catch (Exception $e) {
            DB::rollBack();
            // Log::error("Import error: " . $e->getMessage());
            return back()->with('error', "An error occurred during the import process. Please try again.");
        }
    }


    public function ShowMyBooks(Request $request)
    {
        $category = null;
        $group    = null;

        if ($request->category) {
            $category = Type::where("name", $request->category)->first();
        }

        if ($request->group) {
            $group = auth()->user()->createdgroups()->where("title", $request->group)->first();
        }

        $data['title']       = 'My Items';
        $data['template']    = 'book.list';
        $data['script_file'] = 'listing';

        $ownGroupIds  = auth()->user()->createdgroups()->pluck('id')->toArray();
        $joinGroupIds = Groupmember::where('member_id', auth()->id())
            ->where('status', 'added')
            ->pluck('group_id')
            ->toArray();
        $groupIds = array_unique(array_merge($ownGroupIds, $joinGroupIds));

        $data['groups']     = Group::whereIn("id", $groupIds)->get(['id', 'title']);
        $data['categories'] = Type::get();

        $books = Book::with('availableentries')
            ->where("created_by", Auth::user()->id)
            ->when($category, function ($query) use ($category) {
                return $query->where('type_id', $category->id);
            })
            ->when($group, function ($query) use ($group) {
                return $query->where('group_id', $group->id);
            })
            ->get();

        $data['books'] = $books;

        return view('with_login_common', compact('data'));
    }

    public function BorrowedItems(Request $request)
    {
        $data['title']    = 'Borrowed Items';
        $data['template'] = 'group.borrowed_items';

        $borrowedItems = Entries::with(['book', 'book.user', 'reserver'])
            ->where('is_reserved', 1)
            ->where('reserved_by', auth()->id())
            ->get();

        return view('with_login_common', compact('data', 'borrowedItems'));
    }

    public function ShowAllItems(Request $request)
    {
        $category = null;
        $group    = null;

        if ($request->category) {
            $category = Type::where("name", $request->category)->first();
        }

        if ($request->group) {
            $group = Group::where("title", $request->group)->first();
        }

        $data['title']    = 'All Items';
        $data['template'] = 'book.list';
        $books            = Book::when($group, function ($query) use ($group) {
            return $query->where('group_id', $group->id);
        })->when($category, function ($query) use ($category) {
            return $query->where('type_id', $category->id);
        })->get();

        $data['books']       = $books;
        $data['groups']      = Group::get(['id', 'title']);
        $data['categories']  = Type::get();
        $data['script_file'] = 'listing';
        return view('with_login_common', compact('data'));
    }

    public function ShowSearchItems(Request $request)
    {
        $search_action        = $request->search_action;
        $data['title']        = 'Search Items';
        $data['search_group'] = 'Search Group';
        $data['template']     = 'book.search';
        $search_data          = $request->input('search-item');
        if (! empty($search_data)) {
            $books = Book::where('status_options', '!=', 'disable')->with(['availableentries', 'group', 'category'])
                ->where(function ($query) use ($search_data) {
                    $parts = explode(' ', $search_data);
                    foreach ($parts as $part) {
                        $query->orWhere('name', 'like', "%{$part}%")
                            ->orWhere('locations', 'like', "%{$part}%");
                    }
                })
                ->get();
            $groups = Group::where('status', "!=", "0")->with(['addedmembers', 'groupmembers'])->where("title", "like", "%$search_data%")->get();
        } else {
            $books = Book::where('status_options', '!=', 'disable')->get();
            if (! is_null(Auth::user()->group_type)) {
                $grp_type = Auth::user()->group_type;
                $books    = Book::where('status_options', '!=', 'disable')->whereHas("group", function ($q) use ($grp_type) {
                    $q->where("group_type_id", $grp_type);
                })->get();
            }
        }

        if ($search_action == 'my-items') {
            $books = Book::where('status_options', '!=', 'disable')->with(['availableentries', 'group', 'category'])
                ->where(function ($query) use ($search_data) {
                    $parts = explode(' ', $search_data);
                    foreach ($parts as $part) {
                        $query->orWhere('name', 'like', "%{$part}%")
                            ->orWhere('locations', 'like', "%{$part}%");
                    }
                })
                ->where('created_by', Auth::user()->id)->get();
            $data['title']         = 'Searched Items';
            $data['template']      = 'book.list';
            $data['script_file']   = 'listing';
            $data['books']         = $books;
            $data['search_action'] = 'my-items';
            $data['search-item']   = $search_data;
            return view('with_login_common', compact('data'));
        }
        if ($search_action == 'my-groups') {
            $groups                = Group::where('status', "!=", "0")->with(['addedmembers', 'groupmembers'])->where("title", "like", "%$search_data%")->where('created_by', Auth::user()->id)->get();
            $data['title']         = 'Searched Groups';
            $data['template']      = 'group.mylist';
            $data['script_file']   = 'group_listing';
            $data['my_groups']     = $groups;
            $data['search_action'] = 'my-groups';
            $data['search-item']   = $search_data;
            return view('with_login_common', compact('data'));
        }
        if ($search_action == 'join-group') {
            $retdata    = [];
            $endDate    = date("Y-m-d 23:59:59");
            $startDate  = date('Y-m-d 00:00:01', strtotime('-7 days'));
            $week_group = Group::where('status', "!=", "0")->select('group.*')
                ->join('users', 'group.created_by', '=', 'users.id')
                ->with(['addedmembers', 'groupmembers'])
                ->where("group.created_by", "!=", Auth::user()->id)
                ->where("title", "like", "%$search_data%")->get()->toArray();
            $retdata["week_group"] = $week_group;
            $data                  = [];
            $data['title']         = 'Searched Groups';
            $data['template']      = 'group.join';
            $data['script_file']   = 'join';
            $data['search_action'] = 'join-group';
            $data['search-item']   = $search_data;
            return view('with_login_common', compact('data', 'retdata'));
        }

        $data['books']       = $books;
        $data['groups']      = $groups;
        $data['search-item'] = $search_data;
        $data['script_file'] = 'listing';
        return view('with_login_common', compact('data'));
    }

    /*public function DeleteBook(Request $request)
    {
        $book_id = $request->input('item_id');
        $book    = Book::find($book_id);
        $book->entries()->delete();
        $book->delete();
        return json_encode(["success" => true, "message" => "Items Deleted Successfully"]);
    }*/
    public function DeleteBook(Request $request)
    {
        $book_id = $request->input('item_id');
        $book = Book::find($book_id);
        if ($book) {
            $book->entries()->delete();
            $book->delete();  
            return response()->json(["success" => true, "message" => "Items Deleted Successfully"]);
        }
        return response()->json(["success" => false, "message" => "Book not found"]);
    }


    public function ViewBarcode(Request $request)
    {
        $barcode_data = $request->input('barcode_data');
        $data         = [];
        $path         = 'barcodes/' . date("YmdHis") . '.png';
        $file_path    = "storage/{$path}";

        \QRCode::text($barcode_data)->setOutfile($file_path)->png();

        $data["barcode_url"] = Storage::disk('local')->url($path);

        $data['title']    = 'View Barcode';
        $data['template'] = 'book.view-barcode';
        return view('with_login_common', compact('data'));
    }

    public function MakeBarcode(Request $request)
    {
        $data             = [];
        $data['title']    = 'Make Barcode';
        $data['template'] = 'book.make-barcode';
        return view('with_login_common', compact('data'));
    }

    public function GetQRCodes(Request $request)
    {
        $book_id = $request->book_id ?: null;
        $books   = null;
        $retData = '';

        if (! empty($request->itemIds)) {
            $books = Book::whereIn('id', $request->itemIds)->get(['id', 'barcode_url']);
        } elseif (Auth::user()->hasRole('admin')) {
            $books = Book::all(['id', 'barcode_url']);
        } elseif (! empty(Auth::user()->id) && $book_id) {
            $books = Book::where('created_by', Auth::user()->id)
                ->where('id', $book_id)
                ->get(['id', 'barcode_url']);
        }

        if ($books && $books->isNotEmpty()) {
            $retData .= '<table>';
            foreach ($books->chunk(3) as $chunk) {
                $retData .= '<tr>';
                foreach ($chunk as $book) {
                    if (! empty($book->barcode_url)) {
                        $retData .= '<td><img src="' . url($book->barcode_url) . '" style="padding:0px 5px;"></td>';
                    }
                }
                $retData .= '</tr>';
            }
            $retData .= '</table>';
        } else {
            $retData = 'No QR Codes available!';
        }

        return json_encode([
            "success" => true,
            "data"    => $retData,
            "message" => "QR Codes retrieved successfully.",
        ]);
    }

    /*public function ShowBook(Request $request, $id)
    {
        $retdata          = [];
        $book             = Book::with(['category', 'group', 'availableentries', 'entries'])->where("id", $id)->get()->toArray()[0];
        $retdata["book"]  = $book;
        $data             = [];
        $data['title']    = 'Show Book';
        $data['template'] = 'book.show';
        return view('with_login_common', compact('data', 'retdata'));
    }*/

    public function ShowBook(Request $request, $id)
    {
        $retdata = [];
        $book = Book::with(['category', 'group', 'availableentries', 'entries'])->where("id", $id)->first();
        if (!$book) {
            abort(404, "Book not found");
        }
        $retdata["book"] = $book;

        $data = [];
        $data['title'] = 'Show Book';
        $data['template'] = 'book.show';

        return view('with_login_common', compact('data', 'retdata'));
    }


    public function SetBookStatus(Request $request)
    {
        $book = Book::with('entries')->where('id', $request['book_id'])->firstOrFail();
        DB::beginTransaction();

        try {

            if ($book->created_by == auth()->user()->id) {
                return json_encode(["success" => false, "message" => "You have created this book"]);
            }

            foreach ($book->toArray()['entries'] as $entry) {
                if ($entry['is_reserved'] == 1 && $entry['reserved_by'] == auth()->user()->id) {
                    return json_encode(["success" => false, "message" => "You have already reserved this book"]);
                }
            }

            $book_copies = $book->copies;

            if ($book_copies == 0 && $book) {
                return json_encode(["success" => false, "message" => "This book is completely reserved"]);
            }

            $reserved_copies = $entry = Entries::where('book_id', $request['book_id'])
                ->where('is_reserved', 2)->where('extra_request', null)->count();
            if ($book_copies === $reserved_copies) {
                $copy_entr = $entry = Entries::where('book_id', $request['book_id'])->first();
                Entries::create([
                    'name'          => $copy_entr->name,
                    'book_id'       => $copy_entr->book_id,
                    'group_type_id' => $copy_entr->group_type_id,
                    'group_id'      => $copy_entr->group_id,
                    'is_reserved'   => 0,
                    'reserved_by'   => null,
                    'created_by'    => $copy_entr->created_by,
                    'due_date'      => null,
                    'reserved_at'   => null,
                    'extra_request' => 1,
                ]);
            }

            $entry = Entries::where('book_id', $request['book_id'])->where('is_reserved', 0)->first();

            if ($entry == null) {
                return json_encode(["success" => false, "message" => "You can't reserve this book"]);
            }

            $group = (int) $request['group_id'];

            $user = User::findOrFail($book->created_by);

            $loanRule = LoanRule::find($request['duration']);
            if (! $loanRule) {
                return json_encode(["success" => false, "message" => "Invalid loan rule selected"]);
            }

            $dueDate = Carbon::now()->addDays($loanRule->duration)->toDateString();

            $duration = $loanRule->title;

            $link = "<a href=\"" . url("show-group/$group}") . "\">link</a>";

            $emailBody = ["message" => "Book Reserve Approval", "email" => "One of the group member has requested for the book <em><strong>{$book->name}</strong></em> with Book ID <em><strong>{$book->item_id}</strong></em> to reserve for {$duration}<br>Please click this {$link} to approve or reject."];

            if (env('APP_ENV') !== 'local') {
                Mail::to($user->email)->send(new MailService($emailBody));
            }

            $current_user_id = auth()->user()->id;

            $reservationNotification = [
                'only_database' => true,
                'title'         => 'Book Reservation Request',
                'type'          => 'book_reservation_request',
                'subject'       => 'Book Reservation Request',
                'message'       => "One of the group member has requested for the book <em><strong>{$book->name}</strong></em> with Book ID <em><strong>{$book->item_id}</strong></em> to reserve for $duration",
                'user_id'       => $current_user_id,
                'url'           => url("show-group/{$group}"),
                'action'        => 'View Reservation',
            ];

            $user->notify(new GeneralNotification($reservationNotification));

            if ($request['status'] == "reserved") {

                $entry->update([
                    "group_id"    => $group,
                    "is_reserved" => 2,
                    "reserved_by" => auth()->user()->id,
                    "reserved_at" => now()->toDateTimeString(),
                    "due_date"    => $dueDate,
                ]);

                $book = $book->update(['status' => 1]);
            }
            DB::commit();

            return json_encode(["success" => true, "message" => "Book status updated successfully"]);

        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('Error in SetBookStatus: ' . $e->getMessage());
            return json_encode(["success" => false, "message" => "An error occurred while processing your request."]);
        }

    }

    public function ApproveDisapproveReservation(Request $request)
    {
        $request->validate([
            'entry_id'             => 'required|integer',
            'status'               => 'required|string|in:approve,disapprove',
            'image_at_reservation' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            $entry = Entries::findOrFail($request->entry_id);
            $user  = User::findOrFail($entry->reserved_by);
            $book  = Book::findOrFail($entry->book_id);

            $isApproved = $request->status === 'approve';

            $entry->update([
                'is_reserved'    => $isApproved ? 1 : 0,
                'approved_by'    => $isApproved ? auth()->user()->id : null,
                'approved_at'    => $isApproved ? now()->toDateTimeString() : null,
                'disapproved_by' => $request->status === 'disapprove' ? auth()->user()->id : null,
                'disapproved_at' => $request->status === 'disapprove' ? now()->toDateTimeString() : null,
            ]);

            if ($request->hasFile('image_at_reservation') && $isApproved) {
                $imagePath = $request->file('image_at_reservation')->store('reservation_images', 'public');
                $entry->update(['image_at_reservering' => $imagePath]);
            }

            if ($isApproved) {
                $book->update(['copies' => $book->copies - 1]);
                LoanHistory::create([
                    'book_id'     => $book->id,
                    'user_id'     => $entry->reserved_by,
                    'group_id'    => $book->group_id,
                    'reserved_at' => now()->toDateTimeString(),
                    'due_date'    => $entry->due_date,
                    'amount'      => $book->rent_price,
                    'status'      => 'reserved',
                ]);
            } else {
                ItemRejectedRequest::create([
                    'entry_id'       => $entry->id,
                    'user_id'        => $user->id,
                    'book_id'        => $book->id,
                    'reason'         => $request->rejection_reason ?? 'Reservation disapproved',
                    'disapproved_by' => auth()->id(),
                    'disapproved_at' => now(),
                    'payload'        => $entry->toArray(),
                ]);
            }

            if ($book->copies < 1) {
                Entries::where('is_reserved', 2)
                    ->where('book_id', $entry->book_id)
                    ->update([
                        'cancel_reason' => 'No available copies',
                        'canceled_by'   => auth()->user()->id,
                        'canceled_at'   => now(),
                    ]);

                Entries::where('is_reserved', 2)
                    ->where('book_id', $entry->book_id)
                    ->delete();
            }

            $approvalStatus = $isApproved ? 'Approved' : 'Rejected';
            $link           = route('show-group', ['id' => $entry->group_id]);

            $emailBody = [
                "message" => "Book Reservation Status",
                "email"   => "Your request is {$approvalStatus} for the book <em><strong>{$book->name}</strong></em> with Book ID <em><strong>{$book->item_id}</strong></em>. <br>Please click this <a href=\"{$link}\">link</a> for more information.",
            ];

            if (env('APP_ENV') !== 'local') {
                Mail::to($user->email)->send(new MailService($emailBody));
            }

            $notification = [
                'only_database' => true,
                'title'         => "Book Reservation {$approvalStatus}",
                'type'          => 'book_reservation_' . strtolower($approvalStatus),
                'subject'       => "Book Reservation {$approvalStatus}",
                'message'       => "Your request is {$approvalStatus} for the book <em><strong>{$book->name}</strong></em> with Book ID <em><strong>{$book->item_id}</strong></em>.",
                'user_id'       => auth()->id(),
                'url'           => $link,
                'action'        => 'View Book',
            ];

            $user->notify(new GeneralNotification($notification));

        });

        return json_encode(["success" => true, "message" => "Entry status updated successfully"]);
    }

    public function RejectReturnRequest(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'entry_id' => 'required|integer',
            ]);

            $entry = Entries::with(['book', 'book.group'])->find($request->entry_id);

            if (! $entry || $entry->state !== 'return-request') {
                return json_encode(["success" => false, "message" => "Entry not found or invalid state."]);
            }

            $book_reserved_by = User::find($entry->reserved_by);
            if (! $book_reserved_by) {
                return json_encode(["success" => false, "message" => "Reserved user not found."]);
            }

            $group = $entry->book->group;
            if (! $group) {
                return json_encode(["success" => false, "message" => "Group not found."]);
            }

            $groupAdmins = $group->groupmembers()
                ->where('member_role', 'admin')
                ->where('activated', true)
                ->where('member_id', '!=', $entry->reserved_by)
                ->where('member_id', '!=', $entry->book->created_by)
                ->pluck('member_id')
                ->toArray();

            if (empty($groupAdmins) && $group->created_by) {
                $groupAdmins[] = $group->created_by;
            }

            $adminId = $groupAdmins[array_rand($groupAdmins)];

            $reservedByName = $book_reserved_by->name ?? 'Unknown User';
            $dueDate        = $entry->due_date ? Carbon::parse($entry->due_date)->format('d-m-Y') : 'N/A';

            $ticket = Ticket::create([
                'description' => "The return of the book <strong>{$entry->book->name}</strong> (Book ID: {$entry->book->id}) has been rejected.
                              Reserved by: <strong>{$reservedByName}</strong>.
                              Due date: <strong>{$dueDate}</strong>.",
                'group_id'    => $entry->group_id,
                'user_id'     => auth()->id(),
                'admin_id'    => $adminId,
            ]);

            $ticketAssignNotification = [
                'title'   => 'Ticket Assigned: Return Rejected',
                'type'    => 'ticket_assigned',
                'subject' => 'Ticket Assigned',
                'message' => "A ticket has been created for the rejected return of the book <strong>{$entry->book->name}</strong>.",
                'user_id' => auth()->id(),
                'url'     => url("show-group/{$entry->group_id}?tab=community&ticket={$ticket->id}"),
                'action'  => 'View Ticket',
            ];

            $ticket->admin->notify(new GeneralNotification($ticketAssignNotification));

            $book_reserved_by->notify(new GeneralNotification([
                'title'   => 'Return Request Rejected',
                'type'    => 'return_request_rejected',
                'subject' => 'Return Request Rejected',
                'message' => "Your return request for the book <strong>{$entry->book->name}</strong> has been rejected.",
                'url'     => route("show-group", $group->id),
                'action'  => 'View Details',
            ]));

            $entry->state = 'rejected';
            $entry->save();

            DB::commit();

            return json_encode(["success" => true, "message" => "Return request has been rejected, and a ticket has been created."]);
        } catch (Exception $e) {
            DB::rollBack();
            return json_encode([
                "success" => false,
                "message" => "An error occurred while processing the request.",
                "error"   => $e->getMessage(),
            ]);
        }
    }

    public function ShowEntries($id)
    {
        $entries = Entries::with(['reserved_by:id,name,email'])
            ->where('book_id', $id)
            ->get(['id', 'name', 'is_reserved', 'disapproved_at', 'reserved_by', 'due_date', 'is_sold', 'sold_date']);

        $entries->each(function ($entry) {
            if ($entry->reserved_by) {
                $userTrustScores = TrustScore::where('user_id', $entry->reserved_by);
                $averageRating   = $userTrustScores->avg('rating');
                $totalReviews    = $userTrustScores->count();

                $entry->average_rating = $totalReviews === 0 ? 'First time' : sprintf('%.1f/5 out of %d', $averageRating, $totalReviews);
            }
        });

        return response()->json($entries, 200);
    }

    public function ReturnBook(Request $request)
    {
        try {

            $request->validate([
                'entry_id'           => 'required|integer',
                'image_at_returning' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'original_condition' => 'required',
            ]);

            $entry = Entries::with('book')
                ->where('id', $request->entry_id)
                ->where('reserved_by', auth()->user()->id)
                ->first();

            if (! $entry) {
                return response()->json(["success" => false, "message" => "Entry not found or unauthorized."], 404);
            }

            $book_reserved_by = User::find($entry->reserved_by);
            if (! $book_reserved_by) {
                return response()->json(["success" => false, "message" => "Reserved user not found."], 404);
            }

            $group = Group::find($entry->group_id);
            if (! $group) {
                return response()->json(["success" => false, "message" => "Group not found."], 404);
            }

            $book_owner = User::find($group->created_by);
            if (! $book_owner) {
                return response()->json(["success" => false, "message" => "Book owner not found."], 404);
            }

            $book_owner_email = $book_owner->email;

            $link = "<a href='" . url("show-group/{$entry->group_id}?tab=return-requests") . "'>link</a>";

            $emailBody = [
                "message" => "Book returned",
                "email"   => "One of the group members, " . htmlspecialchars($book_reserved_by->name) .
                ", has returned the book <em><strong>" . htmlspecialchars($entry->book->name) .
                "</strong></em> with Book ID <em><strong>" . htmlspecialchars($entry->book_id) .
                "</strong></em><br>Please click this " . $link . " to approve the returning.",
            ];

            if (env('APP_ENV') !== 'local') {
                Mail::to($book_owner_email)->send(new MailService($emailBody));
            }

            $current_user_id = auth()->user()->id;

            $returnNotification = [
                'only_database' => true,
                'title'         => 'Book Return Request',
                'type'          => 'book_return_request',
                'subject'       => 'Book Return Request',
                'message'       => "One of the group members, " . htmlspecialchars($book_reserved_by->name) .
                ", has returned the book <em><strong>" . htmlspecialchars($entry->book->name) .
                "</strong></em> with Book ID <em><strong>" . htmlspecialchars($entry->book_id) .
                "</strong></em>",
                'user_id'       => $current_user_id,
                'url'           => url("show-group/{$entry->group_id}?tab=return-requests"),
                'action'        => 'View Return Request',
            ];

            $book_owner->notify(new GeneralNotification($returnNotification));

            $imagePath = null;

            if ($request->hasFile('image_at_returning')) {
                $imagePath = $request->file('image_at_returning')->store('item_return_images', 'public');
                $entry->book->update(["latest_image" => $imagePath]);
            }

            $entry->update([
                'state'              => 'return-request',
                'requested_by'       => $entry->reserved_by,
                'image_at_returning' => $imagePath,
                'original_condition' => $request->original_condition,
            ]);

            return response()->json(["success" => true, "message" => "Item return request has been sent to the owner!"]);

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "An error occurred while processing the request.",
                "error"   => $e->getMessage(),
            ], 500);
        }
    }

    public function AdminReturnBook(Request $request)
    {
        $request->validate([
            'entry_id' => 'required|integer',
            'book_id'  => 'required|integer',
            'rating'   => 'required|integer|between:1,5',
            'feedback' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $entry            = Entries::where('id', $request->entry_id)->first();
            $book_reserved_by = User::where('id', $entry->reserved_by)->first();

            $entry->update([
                'is_reserved' => 0,
                'reserved_by' => null,
                'due_date'    => null,
                'approved_by' => null,
                'reserved_at' => null,
                'approved_at' => null,
                'state'       => 'returned',
            ]);

            $book = Book::where('id', $request->book_id)->first();

            $book->update(['copies' => $book->copies + 1]);

            $book_reserved_by->trustscores()->create([
                'entry_id' => $entry->id,
                'book_id'  => $request->book_id,
                'rating'   => $request->rating,
                'feedback' => $request->feedback,
            ]);

            $loan = LoanHistory::where('book_id', $request->book_id)
                ->where('user_id', $book_reserved_by->id)
                ->where('status', 'reserved')->latest('reserved_at')->first();

            if ($loan) {

                $loan->update([
                    'returned_at' => now(),
                    'status'      => 'returned',
                ]);
            }

            $current_user_id = auth()->user()->id;

            $returnBookApprovedNotification = [
                'only_database' => true,
                'title'         => 'Book Return Request Approved',
                'type'          => 'book_return_request_approved',
                'subject'       => 'Book Return Request Approved',
                'message'       => "The book return request is approved successfully",
                'user_id'       => $current_user_id,
                'url'           => url("show-group/" . $book->group_id),
                'action'        => 'View Group',
            ];

            $book_reserved_by->notify(new GeneralNotification($returnBookApprovedNotification));

            DB::commit();

            return json_encode(["success" => true, "message" => "Book returned successfully"]);

        } catch (Exception $e) {

            DB::rollBack();

            return json_encode(["success" => false, "message" => "An error occurred while processing the book return. Please try again later."]);
        }
    }

    /*public function StoreBookAPI(Request $request)
    {
        $data = $request->book;

        $validator = Validator::make($request->all(), [
            "book.name"        => "required|string",
            "book.item_id"     => "required|unique:App\Models\Book,item_id",
            "book.group_id"    => "required|exists:App\Models\Group,id",
            "book.description" => "required",
            "book.copies"      => "required|numeric",
            "book.type_id"     => "required",
            "book.price"       => "required",
            "book.rent_price"  => "required",
            "book.locations"   => "required",
            'cover_page'       => 'required|file|mimes:jpeg,png,jpg',
        ], [
            "book.price"      => "The item.price field is required.",
            "book.rent_price" => "The item.rent price field is required.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'  => $validator->errors(),
                'status' => false,
            ], 400);
        }

        $data['name'] = cleanNameString($data['name']);
        if (! isset($data['name']) || $data['name'] == '') {
            return response()->json([
                'error'  => 'Please enter name in English',
                'status' => false,
            ], 400);
        }

        if ($request->hasFile('cover_page')) {
            $path               = $request->file('cover_page')->store('cover_pages');
            $data['cover_page'] = $path;
        }

        $data["added_date"] = now()->format("Y-m-d");
        $data["created_at"] = now()->format("Y-m-d H:i:s");
        $data["barcode"]    = $data["name"] . ' - ' . $data['writers'];

        if (! isset($request->input('book')['status'])) {
            $data['status'] = 0;
        }

        $file_path = 'barcodes/' . date("YmdHis") . '.png';
        $save_path = storage_path("app/public/{$file_path}");
        $uploader  = User::find(Auth::user()->id);

        $qr_text = $data['name'] . " - " . $uploader->email . " - " . $uploader->address;
        if (! empty($data['locations'])) {
            $locText = implode(", ", $data['locations']);
            $qr_text .= " - Locations: {$locText}";
        }

        QRCode::text($qr_text)->setOutfile($save_path)->png();

        $data["barcode_url"] = "storage/{$file_path}";
        $data["created_by"]  = Auth::user()->id;
        $data['locations']   = json_encode($data['locations']);

        try {
            $book = Book::create($data);

            for ($i = 0; $i < $data['copies']; $i++) {
                $entry_data = [
                    'name'       => $data["name"] . " (Copy " . $i + 1 . ")",
                    'created_by' => Auth::user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                $book->entries()->create($entry_data);
            }
        } catch (Exception $e) {
            return response()->json([
                'error'  => $e->getMessage(),
                'status' => false,
            ], 400);
        }

        return response()->json([
            "message" => "Book" . " " . $book->name . " " . "successfully created",
            "status"  => true,
        ], 200);

    }*/
    public function StoreBookAPI(Request $request)
    {
        $data = $request->book;

        // Validator rules
        $validator = Validator::make($request->all(), [
            "book.name"        => "required|string",
            "book.item_id"     => "required|unique:App\Models\Book,item_id",
            "book.group_id"    => "required|exists:App\Models\Group,id",
            "book.description" => "required",
            "book.copies"      => "required|numeric",
            "book.type_id"     => "required",
            "book.price"       => "required",
            "book.rent_price"  => "required",
            "book.locations"   => "nullable|array", // Ensure locations is an array
            'cover_page'       => 'nullable|file|mimes:jpeg,png,jpg', // Cover page is optional now
        ], [
            "book.price"      => "The item.price field is required.",
            "book.rent_price" => "The item.rent price field is required.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'  => $validator->errors(),
                'status' => false,
            ], 400);
        }

        $data['name'] = cleanNameString($data['name']);
        if (! isset($data['name']) || $data['name'] == '') {
            return response()->json([
                'error'  => 'Please enter name in English',
                'status' => false,
            ], 400);
        }

        // Default cover image if not provided
        if ($request->hasFile('cover_page')) {
            $path = $request->file('cover_page')->store('cover_pages');
            $data['cover_page'] = $path;
        } else {
            // Set a default image (for example, logo.png)
            $data['cover_page'] = 'media/logo.png'; // Assuming 'logo.png' is in the 'cover_pages' directory
        }

        // Default locations to "garage" if not provided
        if (empty($data['locations'])) {
            $data['locations'] = ['garage'];
        }

        $data["added_date"] = now()->format("Y-m-d");
        $data["created_at"] = now()->format("Y-m-d H:i:s");
        $data["barcode"]    = $data["name"] . ' - ' . $data['writers'];

        if (! isset($request->input('book')['status'])) {
            $data['status'] = 0;
        }

        $file_path = 'barcodes/' . date("YmdHis") . '.png';
        $save_path = storage_path("app/public/{$file_path}");
        $uploader  = User::find(Auth::user()->id);

        $qr_text = $data['name'] . " - " . $uploader->email . " - " . $uploader->address;
        if (! empty($data['locations'])) {
            $locText = implode(", ", $data['locations']);
            $qr_text .= " - Locations: {$locText}";
        }

        QRCode::text($qr_text)->setOutfile($save_path)->png();

        $data["barcode_url"] = "storage/{$file_path}";
        $data["created_by"]  = Auth::user()->id;
        $data['locations']   = json_encode($data['locations']);

        try {
            $book = Book::create($data);

            for ($i = 0; $i < $data['copies']; $i++) {
                $entry_data = [
                    'name'       => $data["name"] . " (Copy " . ($i + 1) . ")",
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),
                ];
                $book->entries()->create($entry_data);
            }
        } catch (Exception $e) {
            return response()->json([
                'error'  => $e->getMessage(),
                'status' => false,
            ], 400);
        }

        return response()->json([
            "message" => "Book " . $book->name . " successfully created",
            "status"  => true,
        ], 200);
    }


    public function UpdateBookAPI(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $data = $request->book;

        // applying validations on book data only
        $rules = [
            "book.name"        => "required|string",
            "book.group_id"    => "required|exists:App\Models\Group,id",
            "book.description" => "required",
            //"book.year" => "required|numeric",
            "book.copies"      => "required|numeric",
            //"book.type_id" => "required|exists:App\Models\Grouptype,id",
            "book.price"       => "required",
            "book.rent_price"  => "required",
            "book.locations"   => "required",
            'cover_page'       => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];

        // Add unique check for item_id if it is different from the current one
        if ($request->input('book.item_id') !== $book->item_id) {
            $rules["book.item_id"] = "required|unique:App\Models\Book,item_id";
        } else {
            $rules["book.item_id"] = "required";
        }

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error'  => $validator->errors(),
                'status' => false,
            ], 400);
        }

        $data['name'] = cleanNameString($data['name']);
        if (! isset($data['name']) || $data['name'] == '') {
            return response()->json([
                'error'  => 'Please enter name in English',
                'status' => false,
            ], 400);
        }

        // Handle file upload
        if ($request->hasFile('cover_page')) {
            // Delete the old image if exists
            if ($book->cover_page) {
                Storage::delete('cover_pages/' . $book->cover_page);
            }

            // Store the new image
            $path               = $request->file('cover_page')->store('cover_pages');
            $data['cover_page'] = $path;
        }

        $data["updated_by"] = Auth::user()->id;
        $data["updated_at"] = now()->format("Y-m-d H:i:s");
        $data['locations']  = json_encode($data['locations']);

        if ($book->copies > $data['copies']) {
            unset($data["copies"]);
            return response()->json([
                'error'  => ['book.copies' => [
                    "The book.copies can not be decreased.",
                ],
                ],
                'status' => false,
            ], 400);
        }

        try {
            if ($data['copies'] > $book->copies) {
                $copiesCount = $data['copies'] - $book->copies;
                // creating book copies
                for ($i = 0; $i < $copiesCount; $i++) {
                    $entry_data = [
                        'name'       => $data["name"] . " (Copy " . $book->copies + $i + 1 . ")",
                        'created_by' => Auth::user()->id,
                        'created_at' => date("Y-m-d H:i:s"),
                    ];
                    $book->entries()->create($entry_data);
                }
            }
            $book->update($data);

        } catch (Exception $e) {
            return response()->json([
                'error'  => $e->getMessage(),
                'status' => false,
            ], 400);
        }

        // Return a success response
        return response()->json([
            'message' => 'Book updated successfully',
            'status'  => true,
            'book'    => $book,
        ]);
    }

    public function bulkItemsEdit(Request $request)
    {
        $ids = explode(',', $request->query('ids'));

        $items = Book::whereIn('id', $ids)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'No items found for the provided IDs.');
        }

        $userId = $items[0]->group ? $items[0]->group->created_by : null;

        $groups = Group::where('created_by', $userId)
            ->orWhereHas('groupmembers', function ($q) use ($userId) {
                $q->where('member_id', $userId)
                    ->where('status', 'added')
                    ->where('activated', '1');
            })
            ->get();

        $data['title']       = 'Bulk Items Edit';
        $data['template']    = 'book.bulk-items-edit';
        $data['script_file'] = 'listing';
        $categories          = Type::all();

        return view('with_login_common', compact('data', 'items', 'groups', 'categories'));
    }

    public function bulkUpdateItems(Request $request)
    {
        $items = $request->input('items');

        $rules = [
            'items.*.id'             => 'required|numeric|exists:App\Models\Book,id',
            'items.*.name'           => 'required|string',
            'items.*.group_id'       => 'required|exists:App\Models\Group,id',
            'items.*.type_id'        => 'required|exists:App\Models\Type,id',
            'items.*.price'          => 'required|numeric',
            'items.*.rent_price'     => 'required|numeric',
            'items.*.status_options' => 'required|in:disable,maintenance',
            'items.*.condition'      => 'required',
        ];

        $request->validate($rules);

        try {
            foreach ($items as $itemData) {
                $book                   = Book::findOrFail($itemData['id']);
                $itemData["updated_by"] = Auth::user()->id;
                $book->update($itemData);
            }

            return back()->with('success', "Items updated successfully");

        } catch (Exception $e) {
            return back()->with('error', "Something went wrong");
        }
    }

    public function searchItems(Request $request)
    {
        $query  = $request->input('query');
        $typeId = $request->input('type_id');

        $dbItems = Book::when($typeId != 'all', function ($query) use ($typeId) {
            $query->where('type_id', $typeId);
        })
            ->where('created_by', "!=", auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('keyword', 'LIKE', "%$query%");
            })
            ->get();

        $view = view('book.amazon_searched', compact('dbItems'))->render();

        return response()->json([
            'status' => true,
            'view'   => $view,
        ]);
    }

    public function addToGroup(Request $request)
    {
        try {

            $originalItem = Book::findOrFail($request->item_id);

            $groupExists = Group::where('id', $request->group_id)->exists();
            if (! $groupExists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid group selected.',
                ]);
            }

            $alreadyExists = Book::where('name', $originalItem->name)
                ->where('group_id', $request->group_id)
                ->where('created_by', auth()->id())
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'This item already exists in the selected group.',
                ]);
            }

            $newImagePath = null;
            if ($originalItem->cover_page) {
                $originalImagePath = $originalItem->cover_page;

                $path = explode("/storage/", $originalImagePath);
                if (isset($path[1]) && ! empty($path[1])) {
                    $newImageName = uniqid() . '_' . basename($originalImagePath);
                    $newImagePath = "cover_pages/{$newImageName}";
                    Storage::copy($path[1], $newImagePath);
                }
            }

            $file_path = 'barcodes/' . date("YmdHis") . '.png';
            $save_path = "storage/{$file_path}";

            $uploader = User::find(Auth::user()->id);
            \QRCode::text("{$originalItem->name} - {$uploader->name} - {$uploader->address}")->setOutfile($save_path)->png();
            $barcode_url = Storage::disk('local')->url($file_path);

            $newItem              = $originalItem->replicate();
            $newItem->item_id     = generateUniqueId('book', 'item_id', 12);
            $newItem->cover_page  = $newImagePath;
            $newItem->barcode_url = $barcode_url;
            $newItem->created_by  = Auth::id();
            $newItem->group_id    = $request->group_id;
            $newItem->added_date  = date("Y-m-d");
            $newItem->created_at  = now();
            $newItem->status      = 1;
            $newItem->save();

            for ($i = 0; $i < $request->copies; $i++) {
                $entry_data = [
                    'name'       => $newItem->name . " (Copy " . ($i + 1) . ")",
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),
                ];
                $newItem->entries()->create($entry_data);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Item added to the group successfully.',
                'url'     => route('show-item', $newItem->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to duplicate the item. Error: ' . $e->getMessage(),
            ]);
        }
    }

}
