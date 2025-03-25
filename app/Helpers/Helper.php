
<?php
use App\Mail\MailService;
use App\Models\Groupmember;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Setting;
use App\Models\IntuitToken;

function cleanNameString($string)
{
    $symbols_data = "/[^\p{L}\p{N}\s\-_\[\]\.@\(\)%&]/u";
    $sqlwordlist = array('select', 'drop', 'delete', 'update', ' or ', 'mysql', 'sleep');
    $value = preg_replace($symbols_data, '', $string);
    foreach ($sqlwordlist as $v) {
        $value = preg_replace("/\S*$v\S*/i", '', $value);
    }

    return $value;
}

function respondWithSuccess($data, $module, $message = "", $success_code = 200)
{
    return response()->json([
        "code" => $success_code, "success" => true,
        "module" => $module, "message" => $message, "data" => $data,
    ]);
}

function respondWithError($errors, $error_code = 500)
{
    $err_msg = "";
    foreach ($errors as $err) {$err_msg .= (is_array($err) ? implode(",", $err) : $err) . ",";}
    $err_msg = rtrim($err_msg, ",");
    return response()->json([
        "code" => $error_code, "success" => false, "message" => $err_msg, "errors" => $errors,
    ]);
}

function returnErrorMsg($errors)
{
    $err_msg = "";
    foreach ($errors as $err) {$err_msg .= (is_array($err) ? implode(",", $err) : $err) . ",";}
    $err_msg = rtrim($err_msg, ",");
    return $err_msg;
}

function responseValidationError($message, $errors)
{

    return response([

        'status' => 'error',
        'code' => '400',
        'message' => $message,
        'data' => $errors,

    ]);

}

function getIp()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return request()->ip(); // it will return server ip when no client ip found
}

function send_notification_FCM($notification_id, $title, $message, $type, $source, $additional_data = null)
{
    if ($notification_id != null) {
        $reg_id = $notification_id;
        //Source 0 For Web

        if ($source == 0) {
            $dataArray = array(
                'reference_id' => 1,
                'key' => $type,
            );

            $message = [
                "to" => $reg_id,
                "data" => [
                    "message" => $title,
                    "body" => $message,
                ],

            ];

            if ($additional_data != null) {
                $message["data"] = $message["data"] + $additional_data;
            }
            $client = new GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=AAAAVYvjeE8:APA91bHlsLQOSvxlAEyRyZGIfLwmWHPy0adX_xhcdaHf0OXjOH652d1FRjptQd5ypuZu6ColciC7w4u1AzkzdfpiBBgU_AT3Dg5walwiMA4y8Z1XcagL_YlXKlHLPOTbgxf2b4ZQi8n8',
                ],
            ]);
        }
        //Source 1 For Mobile
        else if ($source == 1) {
            $message = [
                "registration_ids" => array($notification_id),
                "notification" => [
                    "title" => $title,
                    "body" => $message,
                ],
            ];
            $client = new GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=AAAAVYvjeE8:APA91bHlsLQOSvxlAEyRyZGIfLwmWHPy0adX_xhcdaHf0OXjOH652d1FRjptQd5ypuZu6ColciC7w4u1AzkzdfpiBBgU_AT3Dg5walwiMA4y8Z1XcagL_YlXKlHLPOTbgxf2b4ZQi8n8',
                ],
            ]);
        }
        $response = $client->post('https://fcm.googleapis.com/fcm/send',
            ['body' => json_encode($message)]
        );
    }
}

function filterMobileNumber($mobile_no)
{
    if (is_numeric($mobile_no)) {
        $number_length = strlen($mobile_no);
        if ($number_length == 9) {return "966" . $mobile_no;}
        if ($number_length == 10) {return "966" . ltrim($mobile_no, "0");}
        if ($number_length == 12) {return $mobile_no;}
    }
    return "";
}

function paginate($items, $perPage = null, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

function group_by($key, $data)
{
    $result = array();

    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[""][] = $val;
        }
    }
    return $result;
}

function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
{
    $i = $j = $c = 0;
    for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i++) {
        if ((($vertices_y[$i] > $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
            ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]))) {
            $c = !$c;
        }

    }
    return $c;
}

function callExternalAPI($method, $url, $body, $headers)
{
    $client = new GuzzleHttp\Client(['headers' => $headers]);
    $response = null;
    $return_data = "";
    switch ($method) {
        case "POST":$request = $client->post($url, ['body' => json_encode($body)]);
            $response = $request->send();
            break;
        default:$request = $client->get($url);
            $response = $request->getBody();
            break;
    }
    while (!$response->eof()) {$return_data .= $response->read(1024);}

    return $return_data;
}

function sanitizeData($all_data)
{
    foreach ($all_data as $ky => $data) {if (empty($data)) {$all_data[$ky] = null;}}
    return $all_data;
}

function prepareImageData($ref_type, $img_path, $data, $note = "")
{
    $img_data = [
        "ref_type" => $ref_type,
        "path" => $img_path,
        "note" => $note,
        "created_source" => $data['created_source'],
        "created_by" => $data['created_by'],
        "created_at" => date("Y-m-d H:i:s"),
    ];
    return $img_data;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename)) {
        return false;
    }
    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }

        }
        fclose($handle);
    }

    return $data;
}

function SendEmail($sendto, $title, $desription)
{
    $formData = ["message" => $title, "email" => $desription];
    Mail::to($sendto)->send(new MailService($formData));
}

if (!function_exists('generateUniqueId')) {
    /**
     * Generate a unique 12-character item ID.
     *
     * @param string $table
     * @param string $column
     * @param int $length
     * @return string
     */
    function generateUniqueId(string $table, string $column, int $length): string
    {
        $unique = false;
        $itemId = '';

        while (!$unique) {
            $itemId = Str::random(12);
            $exists = DB::table($table)->where($column, $itemId)->exists();
            if (!$exists) {
                $unique = true;
            }
        }

        return $itemId;
    }
}

if (!function_exists('is_valid_json')) {
    function isValidJson($string)
    {
        json_decode($string);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        // Check if the decoded value is an object or an array
        $decoded = json_decode($string);
        return is_object($decoded) || is_array($decoded);
    }
}

if (!function_exists('user_in_group')) {
    function user_in_group($group)
    {
        $status = null;
        if (auth()->check() && !auth()->user()->hasRole('admin') && auth()->id() != $group->created_by) {
            $status = Groupmember::where('member_id', auth()->user()->id)
                ->where("status", "added")
                ->where('group_id', $group->id)->first();
        }
        return $status;
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key)
    {
        $setting = Setting::where('name', $key)->value('value');

        if (is_null($setting)) {
            return null;
        }

        if (in_array($setting, ['0', '1'])) {
            return $setting === '1';
        }

        return $setting;
    }
}

if (!function_exists('calculateTotalAmount')) {
    function calculateTotalAmount($coinValue)
    {
        $systemFee = Setting::where('name', "system_fee")->value('value');
        $systemFee = (int)$systemFee;

        $processingFee = $coinValue * ($systemFee / 100);
        $totalAmount = $coinValue + $processingFee;
        return round($totalAmount, 2);
    }
}

if (!function_exists('calculateAmountFromCoins')) {
    function calculateAmountFromCoins($rewardCoins)
    {
        $coinValue = Setting::where('name', "coin_value")->value('value'); // Fetch coin value from settings
        $coinValue = (float)$coinValue; // Convert to float

        $amount = $rewardCoins * $coinValue; // Calculate amount
        return round($amount, 2); // Rounded to 2 decimal places
    }
}

if (!function_exists('calculatePackageAmount')) {
    function calculatePackageAmount($package)
    {
        $systemFee = (int) Setting::where('name', "system_fee")->value('value'); // Fetch system fee
        $price = (float) $package->price; // Package price

        $processingFee = $price * ($systemFee / 100); // Calculate Fee
        $totalAmount = $price + $processingFee; // Final Amount with Fee

        return round($totalAmount, 2); // Rounded to 2 decimal
    }
}

if (!function_exists('intuitToken')) {
    function intuitToken()
    {
        return IntuitToken::first();
    }
}

if (!function_exists('sendMail')) {
    function sendMail($template, $email, $name, $subject, $data)
    {
        Mail::send(
            $template,
            ['name' => $name, 'data' => $data],
            function ($mail) use ($email, $name, $subject) {
                $mail->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                $mail->to($email, $name);
                $mail->subject($subject);
            }
        );
    }
}

