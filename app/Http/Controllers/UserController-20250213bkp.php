<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MailService;
use App\Models\Book;
use App\Models\Entries;
use App\Models\Group;
use App\Models\Groupmember;
use App\Models\GroupUserInvited;
use App\Models\ItemRejectedRequest;
use App\Models\Membershipplan;
use App\Models\Type;
use App\Models\User;
use App\Models\Usermembership;
use App\Models\Usermodel;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Session;
use Socialite;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

class UserController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function addGroupLocation(Request $request)
    {
        $request->validate(['location' => 'required|string|max:100']);

        $user        = Auth::user();
        $newLocation = $request->location;

        $groupLocations = json_decode($user->group_locations, true) ?? [];

        if (! in_array($newLocation, $groupLocations)) {
            $groupLocations[] = $newLocation;

            $user->group_locations = json_encode($groupLocations);
            $user->save();
        }

        return response()->json(['success' => true]);

    }

    public function RegisterUser(Request $request)
    {
        $data = $request->input('user');
        unset($data["confirm_password"]);
        $activation_code         = generateRandomString();
        $data["activation_code"] = $activation_code;
        $model                   = new Usermodel();
        $data["group_type"]      = 2;
        $user                    = $model->add($data);
        if (! is_object($user)) {
            $errors = \App\Message\Error::get('user.add');
            if (count($errors) == 0) {
                $errors = \App\Message\Error::get('usermodel.add');
            }
        }
        if (isset($errors) && count($errors) > 0) {
            $message = returnErrorMsg($errors);
            $retdata = [];
            // $retdata['errs'] = $message;
            $retdata['errs']        = $errors;
            $retdata['user']        = $data;
            $retdata['err_message'] = $message;
            // $this->flashError($retdata['err_message']);
            return view('signup', compact('retdata'));
        }

        $create_invite = session()->pull('create_group_invite');
        session()->forget('create_group_invite');

        if ($create_invite) {
            $group_id   = session()->pull('invite_group_id');
            $created_by = session()->pull('created_by');

            $group = Group::find($group_id);

            $group->groupmembers()->create([
                "member_id"   => $user['id'],
                "status"      => "invited",
                "member_role" => "user",
                "created_by"  => $created_by,
                "created_at"  => now(),
            ]);

            session()->put('redirect_url', route('show-group', ['id' => $group->id]));

            session()->forget('create_group_invite');
        }

        $userModel = User::find($user["id"]);
        $userModel->assignRole('user');
        $link               = "<a href=\"" . url("activate-account?code=" . $activation_code) . "\">link</a>";
        $formData           = ["message" => "Activation Code", "email" => "Your Account on Dix Wix has been created Successfully. Please use this " . $link . " to activate your account."];
        $recipientEmail     = $data["email"];
        $retdata["success"] = "Activation Link has been shared to you on your email. Please use this link to activate.";
        $notificationData   = [
            'title'   => 'New user account created',
            'type'    => 'user_registration',
            'subject' => 'New User Registration',
            'message' => "A new user has registered: {$userModel->name}",
            'user_id' => $userModel->id,
            'url'     => route('edit-user', ['id' => $userModel->id]),
            'action'  => 'View User',
        ];

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }

        Mail::to($recipientEmail)->send(new MailService($formData));

        return view("signup", compact('retdata'));
    }

    public function StoreViaUser(Request $request)
    {
        $data          = $request->input('user');
        $referrer_id   = $request->input('referrer_id');
        $group_id      = $request->input('group_id');
        $group_type_id = $request->input('group_type_id');
        unset($data["confirm_password"]);
        // $data['group_type'] = $group_type_id;
        $data['group_type'] = 2;
        $model              = new Usermodel();
        $user               = $model->add($data);
        if (! is_object($user)) {
            $errors = \App\Message\Error::get('user.add');
            if (count($errors) == 0) {
                $errors = \App\Message\Error::get('usermodel.add');
            }
        }
        if (isset($errors) && count($errors) > 0) {
            $message                = returnErrorMsg($errors);
            $retdata                = [];
            $retdata['errs']        = $errors;
            $retdata['user']        = $data;
            $retdata['err_message'] = $message;
            $data                   = [];
            return view('signup', compact('retdata'));
        }
        $userModel = User::find($user["id"]);
        $userModel->assignRole('user');
        $formData       = ["message" => "Welcome Message", "email" => "Dear User welcome to DixWix Website"];
        $recipientEmail = $data["email"];
        Mail::to($recipientEmail)->send(new MailService($formData));
        $group = Group::find($group_id);
        $group->groupmembers()->create([
            "member_id"  => $userModel->id,
            "status"     => "added",
            "created_by" => $referrer_id,
            "created_at" => date("Y-m-d H:i:s"),
        ]);
        Auth::login($userModel, false);
        return redirect()->route('dashboard');
    }

    /*public function CustomLogin(Request $request)
    {
        // Validate request
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $retdata = [
            'title'       => 'Login',
            'err_message' => 'Email or password does not match',
        ];

        // Check if the user exists
        $finduser = User::where('email', $request->email)->first();
        if (! $finduser) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist.',
            ]);
        }

        // Check if the password is using the Bcrypt algorithm
        if (password_needs_rehash($finduser->password, PASSWORD_BCRYPT, ['cost' => 12])) {
            return response()->json(['success' => false, 'message' => 'This password does not use the Bcrypt algorithm. Please reset your password.']);
        }

        // Remember me option
        $remember = $request->has('remember_me');

        // Attempt to authenticate
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            // Check if email is verified
            if (Auth::user()->email_verified_at == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is not verified.',
                ]);
            }

            $redirectUrl = session()->pull('redirect_url', route('dashboard')); // Fallback to dashboard
            session()->forget('redirect_url');

            // Check if the user has admin privileges and redirect accordingly
            if (Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success'      => true,
                    'redirect_url' => $redirectUrl ?: route('admin-dashboard'),
                ]);
            }

            // Redirect normal users
            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        // If user exists but uses a different login method
        $finduser = User::where('email', $request->email)->first();
        if ($finduser && $finduser->source != 'email') {
            return response()->json([
                'success' => false,
                'message' => 'You previously logged in using ' . $finduser->source . '. Please log in using that method.',
            ]);
        }

        // Default error message
        return response()->json([
            'success' => false,
            'message' => $retdata['err_message'],
        ]);
    }*/
    public function CustomLogin(Request $request)
{
    // If in local environment, skip reCAPTCHA validation
    if (env('APP_ENV') === 'local') {
        // Just for local development, you can skip reCAPTCHA validation
        $recaptchaValidated = true;
    } else {
        // Validate reCAPTCHA in production or staging environments
        $recaptchaValidated = $this->validateRecaptcha($request->get('g-recaptcha-response'));
    }

    // Validate request
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $retdata = [
        'title'       => 'Login',
        'err_message' => 'Email or password does not match',
    ];

    // Check if the user exists
    $finduser = User::where('email', $request->email)->first();
    if (! $finduser) {
        return response()->json([
            'success' => false,
            'message' => 'User does not exist.',
        ]);
    }

    // Check if the password is using the Bcrypt algorithm
    if (password_needs_rehash($finduser->password, PASSWORD_BCRYPT, ['cost' => 12])) {
        return response()->json(['success' => false, 'message' => 'This password does not use the Bcrypt algorithm. Please reset your password.']);
    }

    // Remember me option
    $remember = $request->has('remember_me');

    // Attempt to authenticate (if reCAPTCHA validated or skipped)
    if ($recaptchaValidated && Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
        // Check if email is verified
        if (Auth::user()->email_verified_at == null) {
            return response()->json([
                'success' => false,
                'message' => 'Email is not verified.',
            ]);
        }

        $redirectUrl = session()->pull('redirect_url', route('dashboard')); // Fallback to dashboard
        session()->forget('redirect_url');

        // Check if the user has admin privileges and redirect accordingly
        if (Auth::user()->hasRole('admin')) {
            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl ?: route('admin-dashboard'),
            ]);
        }

        // Redirect normal users
        return response()->json([
            'success'      => true,
            'redirect_url' => $redirectUrl,
        ]);
    }

    // If user exists but uses a different login method
    $finduser = User::where('email', $request->email)->first();
    if ($finduser && $finduser->source != 'email') {
        return response()->json([
            'success' => false,
            'message' => 'You previously logged in using ' . $finduser->source . '. Please log in using that method.',
        ]);
    }

    // Default error message
    return response()->json([
        'success' => false,
        'message' => $retdata['err_message'],
    ]);
}

protected function validateRecaptcha($recaptchaResponse)
{
    $recaptchaSecret = env('RECAPTCHA_SECRET_KEY'); // Secret key from your .env file

    // Verify reCAPTCHA response with Google
    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret'   => $recaptchaSecret,
        'response' => $recaptchaResponse,
    ]);

    $data = $response->json();
    
    return $data['success'] ?? false; // Return true if reCAPTCHA is successful
}


    public function Dashboard(Request $request)
    {
        $data = [
            'title'             => 'Dashboard',
            'template'          => 'dashboard.main',
            'script_file'       => 'listing',
            'selected_alphabet' => $request->get('selected_alphabet', 'All'),
            'view_type'         => $request->get('view_type', 'title'),
            'group_filter'      => $request->get('group_filter', ''),
            'type_filter'       => $request->get('type_filter', ''),
        ];

        $user   = auth()->user();
        $userId = $user->id;

        $selectedAlphabet = $data['selected_alphabet'];

        $groupsQuery = Group::query();

        if ($selectedAlphabet !== 'All') {
            $groupsQuery->where('title', 'like', "{$selectedAlphabet}%");
        }

        $groupsQuery->where(function ($query) {
            $query->where('status', '!=', 0)->orWhere(function ($query) {
                $query->where('status', 0)->where('created_by', auth()->id());
            });
        });

        $data['groups'] = $groupsQuery->get();

        $data['types'] = Type::all();

        $data['itemMetrics'] = [

            'total_items_by_category' => Book::with('category:id,name')->select('type_id', DB::raw('count(*) as total'))
                ->where('created_by', $userId)->groupBy('type_id')->get()->toArray(),

            'items_rented_out'        => Entries::with(['book', 'book.user'])->
                whereHas('book', function ($query) use ($userId) {
                $query->where('created_by', $userId);
            })->where('is_reserved', 1)->whereNotNull('reserved_by')->get(),

            'rejected_items'          => ItemRejectedRequest::with(['book', 'user', 'disapprover'])
                ->whereHas('book', function ($query) use ($userId) {
                    $query->where('created_by', $userId);
                })
                ->get(),
        ];

        $count = User::selectRaw('
            COUNT(CASE WHEN email_verified_at IS NOT NULL AND deleted_at IS NULL THEN 1 END) as users
        ')->first();

        $isAdmin=false;
        if (Auth::user()->hasRole('admin')) {
            $isAdmin=true;
        }
        $totalItemsCount = Book::whereNull('deleted_at')  // Only count items that are not soft-deleted
        ->when(!$isAdmin, function ($query) use ($userId) {
            // If the user is not an admin, filter by 'created_by'
            $query->where('created_by', $userId);
        })
        ->count();

        $isAdmin = $user->hasRole('admin'); 
        $reservedCount = Book::join('book_entries', 'book_entries.book_id', '=', 'book.id')
            ->when(!$isAdmin, function ($query) use ($userId) {
                // If not an admin, filter by 'created_by' to get the user's own books
                $query->where('book.created_by', $userId);
            })
            ->where('book_entries.is_reserved', 1)
            ->whereNotNull('book_entries.reserved_by')
            ->where('book_entries.due_date', '<', Carbon::now())
            ->count(); 


        $data['totalusers']=$count->users;
        $data['totalItemsCount']=$totalItemsCount;
        $data['reservedBooks']=$reservedCount;


        $userGroupsIds = $user->createdgroups()
            ->pluck('id')->toArray();

        $userJoinedGroupsIds = $user->usergroups()
            ->where('member_role', 'admin')
            ->pluck('group_id')
            ->toArray();

        $groupsIds = array_merge($userGroupsIds, $userJoinedGroupsIds);

        $data['groupMetrics'] = Group::whereIn('id', $groupsIds)
            ->with(['books' => function ($query) use ($userId) {
                // $query->where('created_by', $userId)
                $query->select('group_id', 'price', 'rent_price');
            }])
            ->get()
            ->map(function ($group) {
                $totalPrice      = $group->books->sum('price');
                $totalRentalCost = $group->books->sum('rent_price');
                return [
                    'group_id'    => $group->id,
                    'group_title' => $group->title,
                    'total_items' => $group->books->count(),
                    'savings'     => $totalPrice - $totalRentalCost,
                ];
            });

        return view('with_login_common', compact('data'));
    }

    public function getItemDetails(Request $request)
{
    $cardId = $request->input('card_id');
    $userId = auth()->user()->id;  // Get the logged-in user's ID
    $isAdmin = auth()->user()->hasRole('admin'); // Check if the user is an admin

    $response = [];

    switch($cardId) {
        case 'overdue': // Overdue Books

            // Fetch the list of overdue books
            $overdueBooks = Book::join('book_entries', 'book_entries.book_id', '=', 'book.id')
                ->when(!$isAdmin, function ($query) use ($userId) {
                    // If not an admin, filter by 'created_by' to get the user's own books
                    $query->where('book.created_by', $userId);
                })
                ->where('book_entries.is_reserved', 1)
                ->whereNotNull('book_entries.reserved_by')
                ->where('book_entries.due_date', '<', Carbon::now())
                ->get(['book.id', 'book.name as title', 'book_entries.due_date', 'book_entries.reserved_by']);

            // Prepare response
            $response = [
                'success' => true,
                'data' => [
                    'title' => 'Overdue Books',
                    'reservedCount' => 0,
                    'overdueBooks' => $overdueBooks
                ]
            ];
            break;

        case 'itemsall': // For the "Completed Loans" or other cards
            $items = Book::whereNull('deleted_at') // Only non-deleted items
                ->when(!$isAdmin, function ($query) use ($userId) {
                    // If not an admin, filter by 'created_by' to get the user's own items
                    $query->where('created_by', $userId);
                })
                ->get(['id', 'name as title', 'writers as author', 'created_at']);

            $response = [
                'success' => true,
                'data' => [
                    'title' => 'Items List',
                    'totalItemsCount' => 0,
                    'items' => $items
                ]
            ];
            break;

        case 'itemrented':
        $itemsRentedOut = Entries::join('book', 'book_entries.book_id', '=', 'book.id')
        ->leftJoin('users', 'users.id', '=', 'book_entries.reserved_by')
        ->select('book_entries.*', 'users.name AS usersName')
        ->where('book_entries.is_reserved', 1) // Ensure the book is reserved
        ->whereNotNull('book_entries.reserved_by') // Ensure the book has been reserved
        ->where('book_entries.due_date', '<', Carbon::now()) // Ensure the due date is past
        ->where('book.created_by', $userId) // Filter by the creator of the book
        ->get();

        $response = [
            'success' => true,
            'data' => [
                'title' => 'My Items Rented Out',
                'count' => 0,
                'renteditem' => $itemsRentedOut
            ]
        ];
        break;

        case 'itemrejected':
        $rejectedItems = ItemRejectedRequest::join('book', 'item_rejected_requests.book_id', '=', 'book.id')
        ->leftJoin('users as disapprover', 'item_rejected_requests.disapproved_by', '=', 'disapprover.id')
        ->leftJoin('users as user', 'item_rejected_requests.user_id', '=', 'user.id')
        ->whereHas('book', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })
        ->select('item_rejected_requests.*', 'book.name as book_title', 'user.name as user_name', 'disapprover.name as disapprover_name')
        ->get();

        $response = [
            'success' => true,
            'data' => [
                'title' => 'Rejected Items',
                'totalItemsCount' => 0,
                'rejectedItems' => $rejectedItems
            ]
        ];
        break;

        // Add other cases for other card types...
        default:
            $response = ['success' => false];
            break;
    }

    return response()->json($response);
}



    public function Logout()
    {
        Session::flush();
        Auth::logout();

        return redirect('home');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function GoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $finduser = User::where(['external_id' => $user->id, 'source' => "google"])->first();

            if ($finduser) {

                Auth::login($finduser);

                $redirectUrl = session()->pull('redirect_url', route('dashboard'));
                session()->forget('redirect_url');

                return redirect()->to($redirectUrl)->withSuccess('Signed in');
            } else {

                DB::beginTransaction();

                $data = [
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'password'          => bcrypt('12345678'),
                    'external_id'       => $user->id,
                    'group_type'        => 2,
                    'email_verified_at' => now(),
                    'source'            => "google",
                ];

                $newUser = User::create($data);
                $newUser->assignRole('user');
                Auth::login($newUser);

                $create_invite = session()->pull('create_group_invite');
                $group_id      = session()->pull('invite_group_id');
                $created_by    = session()->pull('created_by');

                if ($create_invite && $group_id && $created_by) {
                    $group = Group::find($group_id);

                    if ($group) {

                        $existingMember = $group->groupmembers()
                            ->where('member_id', $newUser->id)
                            ->exists();

                        if (! $existingMember) {
                            $group->groupmembers()->create([
                                "member_id"   => $newUser->id,
                                "status"      => "invited",
                                "member_role" => "user",
                                "created_by"  => $created_by,
                                "created_at"  => now(),
                            ]);
                        }

                        session()->put('redirect_url', route('show-group', ['id' => $group->id]));
                    }

                    session()->forget('create_group_invite');
                }

                $groupModel = new Group();
                $groupData  = [
                    "title"         => "Dix New Group",
                    "default"       => true,
                    "description"   => "Please change title and description",
                    "group_picture" => 'media/group-dummy.jpg',
                    "created_at"    => now(),
                    "updated_at"    => now(),
                    "created_by"    => $newUser->id,
                    "status"        => 0,
                ];

                $groupModel->add($groupData);

                $latestGroup = $newUser->createdgroups()->latest()->first();

                $latestGroup->groupmembers()->updateOrCreate(
                    [
                        "member_id" => $newUser->id,
                        "group_id"  => $latestGroup->id,
                    ],
                    [
                        "status"     => "added",
                        "activated"  => 1,
                        "created_by" => $newUser->id,
                        "created_at" => now(),
                    ]
                );

                $groupNotification = [
                    'title'   => 'Group created',
                    'type'    => 'group_created',
                    'subject' => 'New Group Created',
                    'message' => 'A group created for you',
                    'user_id' => $newUser->id,
                    'url'     => route('show-group', ['id' => $latestGroup->id]),
                    'action'  => 'View Group',
                ];

                $newUser->notify(new GeneralNotification($groupNotification));

                $newUser->membership()->create([
                    "plan_id"    => 1,
                    "is_active"  => 1,
                    "start_date" => now(),
                    "created_by" => $newUser->id,
                    "created_at" => now(),
                ]);

                $newUser->group_type = 1;
                $newUser->save();

                $notificationData = [
                    'title'   => 'New user account created',
                    'type'    => 'user_registration',
                    'subject' => 'New User Registration',
                    'message' => "A new user has registered: {$newUser->name}",
                    'user_id' => $newUser->id,
                    'url'     => route('edit-user', ['id' => $newUser->id]),
                    'action'  => 'View User',
                ];

                $admins = User::role('admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GeneralNotification($notificationData));
                }

                $redirectUrl = session()->pull('redirect_url', route('dashboard'));
                session()->forget('redirect_url');

                DB::commit();
                return redirect()->to($redirectUrl)->withSuccess('Signed in');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error during Google Callback', ['error' => $e->getMessage()]);
            return redirect('login')->withErrors(['error' => 'An error occurred during login. Please try again.']);
        }
    }

    public function ActivateAccount(Request $request)
    {
        $activation_code  = $request->query("code");
        $retdata['title'] = 'Activation Code';

        if (! empty($activation_code)) {
            $finduser = User::where('activation_code', $activation_code)->first();

            if ($finduser) {

                if ($finduser->email_verified_at) {
                    $retdata['err_message'] = "Your account is already verified. Please login to use the portal.";
                    return view('login', compact('retdata'));
                }

                $finduser->email_verified_at = now();
                $finduser->save();

                $retdata['success_message'] = "Account Verification Successful. Login to use the portal.";

                if (! $finduser->createdgroups()->exists()) {

                    $groupData = [
                        "title"         => "Dix New Group",
                        "default"       => true,
                        "description"   => "Please change title and description",
                        "group_picture" => 'media/group-dummy.jpg',
                        "created_at"    => now(),
                        "updated_at"    => now(),
                        "created_by"    => $finduser->id,
                        "status"        => 0,
                    ];

                    $group = $finduser->createdgroups()->create($groupData);

                    $group->groupmembers()->create([
                        "member_id"  => $finduser->id,
                        "status"     => "added",
                        "created_by" => $finduser->id,
                        "created_at" => date("Y-m-d H:i:s"),
                    ]);

                    $groupNotification = [
                        'title'   => 'Group created',
                        'type'    => 'group_created',
                        'subject' => 'New Group Created',
                        'message' => 'A group created for you',
                        'user_id' => $finduser->id,
                        'url'     => route('show-group', ['id' => $group->id]),
                        'action'  => 'View Group',
                    ];

                    $finduser->notify(new GeneralNotification($groupNotification));
                }

                if (! $finduser->membership()->where('is_active', 1)->exists()) {
                    $finduser->membership()->create([
                        "plan_id"    => 1,
                        "is_active"  => 1,
                        "start_date" => now(),
                        "created_by" => $finduser->id,
                        "created_at" => now(),
                    ]);
                }

                // Set the user's group type
                $finduser->group_type = 1;
                $finduser->save();
            } else {
                $retdata['err_message'] = "Invalid activation code. Please sign up to create a new account.";
            }
        } else {
            $retdata['err_message'] = "No activation code provided.";
        }

        return view('login', compact('retdata'));
    }

    public function EditUser()
    {
        $user                = User::find(Auth::user()->id);
        $data['title']       = 'Edit Profile';
        $data['template']    = 'user.edit';
        $data['script_file'] = 'add_item';
        return view('with_login_common', compact('data', 'user'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'biodata'          => 'nullable|string',
            'state'            => 'nullable|string|max:255',
            'zipcode'          => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'locations'        => 'nullable|array',
            'profile_pic'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'current_password' => 'nullable|string',
            'password'         => 'nullable|string|min:6|confirmed',
        ], ['phone.regex' => 'Required Phone Format: +CountryCode number (e.g., +1 1234567890)']);

        $user = auth()->user();

        $formData = $request->except(['current_password', 'password', 'password_confirmation', 'profile_pictures']);

        if ($request->profile_pic) {
            $formData['profile_pic'] = $request->profile_pic->store('profile_pictures', 'public');
        }

        if ($request->current_password && $request->password) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.')->withInput();
            }
            $formData['password'] = $request->password;
        }

        if (empty($request->locations)) {
            $formData['locations'] = null;
        }

        $user->update($formData);

        return back()->with('success', 'Profile updated successfully');
    }

    public function DeleteAccount(Request $request)
    {
        try {
            $user = auth::user();

            if ($user->id == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Super admin account cannot be deleted successfully',
                ]);
            }

            GroupUserInvited::where("email", $user->email)->delete();
            Groupmember::where('member_id', $user->id)->delete();

            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function MyAccount(Request $request)
    {
        $user         = auth()->user();
        $current_plan = Usermembership::with('plan')->where('user_id', $user->id)->first();

        $plans = Membershipplan::where('id', '!=', $current_plan->plan_id)->get();

        $payment_methods = $user->paymentMethods()->latest()->get();

        $data = [
            'title'    => 'My Account',
            'template' => 'account.main',
        ];

        return view('with_login_common', compact('data', 'current_plan', 'plans', 'payment_methods'));
    }

    public function SavePaymentMethod(Request $request)
    {
        $request->validate(['payment_method_id' => 'required|string']);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $user = auth()->user();

            if (! $user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name'  => $user->name,
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
            $paymentMethod->attach(['customer' => $customer->id]);

            $customer->invoice_settings = [
                'default_payment_method' => $request->payment_method_id,
            ];
            $customer->save();

            DB::transaction(function () use ($user, $request, $paymentMethod) {

                $user->paymentMethods()->update(['default' => false]);

                $user->paymentMethods()->create([
                    'stripe_payment_method_id' => $request->payment_method_id,
                    'type'                     => $paymentMethod->card->brand,
                    'last4'                    => $paymentMethod->card->last4,
                    'expiry_month'             => $paymentMethod->card->exp_month,
                    'expiry_year'              => $paymentMethod->card->exp_year,
                    'default'                  => true,
                ]);
            });

            return back()->with('success', 'Payment method saved successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error saving payment method: ' . $e->getMessage());
        }
    }

    public function RemovePaymentMethod(Request $request)
    {
        $request->validate(['payment_method_id' => 'required|integer|exists:payment_methods,id']);

        try {
            $user = auth()->user();

            $paymentMethod = $user->paymentMethods()->findOrFail($request->payment_method_id);

            Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripePaymentMethod = PaymentMethod::retrieve($paymentMethod->stripe_payment_method_id);
            $stripePaymentMethod->detach();

            $paymentMethod->delete();

            if ($paymentMethod->default) {
                $nextDefault = $user->paymentMethods()->first();
                if ($nextDefault) {
                    $nextDefault->update(['default' => true]);
                    $customer                   = Customer::retrieve($user->stripe_customer_id);
                    $customer->invoice_settings = [
                        'default_payment_method' => $nextDefault->stripe_payment_method_id,
                    ];
                    $customer->save();
                }
            }

            return back()->with('success', 'Payment method removed successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error removing payment method: ' . $e->getMessage());
        }
    }

    public function makeDefaultPaymentMethod(Request $request)
    {
        $request->validate(['payment_method_id' => 'required|integer|exists:payment_methods,id']);

        try {
            $user = auth()->user();

            $paymentMethod = $user->paymentMethods()->findOrFail($request->payment_method_id);

            Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer                   = Customer::retrieve($user->stripe_customer_id);
            $customer->invoice_settings = [
                'default_payment_method' => $paymentMethod->stripe_payment_method_id,
            ];
            $customer->save();

            DB::transaction(function () use ($user, $request, $paymentMethod) {

                $user->paymentMethods()->update(['default' => false]);
                $paymentMethod->update(['default' => true]);

            });

            return back()->with('success', 'Payment method set as default successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error setting payment method as default: ' . $e->getMessage());
        }
    }

    public function switchPlan(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:membership_plans,id']);

        try {
            $user    = auth()->user();
            $newPlan = Membershipplan::findOrFail($request->plan_id);

            $currentPlan = Usermembership::with('plan')->where('user_id', $user->id)->where('is_active', true)->first();

            Stripe::setApiKey(env('STRIPE_SECRET'));

            if ($newPlan->price > 0) {

                if (! $user->stripe_customer_id) {
                    $customer = Customer::create([
                        'email' => $user->email,
                        'name'  => $user->name,
                    ]);
                    $user->update(['stripe_customer_id' => $customer->id]);
                }

                $defaultPaymentMethod = $user->paymentMethods()->where('default', true)->first();
                if (! $defaultPaymentMethod) {
                    return back()->with('error', 'Please add a default payment method to switch to a paid plan.');
                }

                $stripePaymentMethod = PaymentMethod::retrieve($defaultPaymentMethod->stripe_payment_method_id);
                if (! $stripePaymentMethod->customer) {
                    $stripePaymentMethod->attach(['customer' => $user->stripe_customer_id]);
                }

                if ($currentPlan && $currentPlan->stripe_subscription_id) {
                    $stripeSubscription = StripeSubscription::retrieve($currentPlan->stripe_subscription_id);
                    $stripeSubscription->cancel();
                }

                $stripeSubscription = StripeSubscription::create([
                    'customer'               => $user->stripe_customer_id,
                    'items'                  => [['price' => $newPlan->stripe_price_id]],
                    'default_payment_method' => $defaultPaymentMethod->stripe_payment_method_id,
                    'expand'                 => ['latest_invoice.payment_intent'],
                ]);

                $nextBillingDate = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

                if ($currentPlan) {
                    $currentPlan->update([
                        'plan_id'                => $newPlan->id,
                        'start_date'             => now(),
                        'end_date'               => $nextBillingDate,
                        'is_active'              => true,
                        'stripe_subscription_id' => $stripeSubscription->id,
                    ]);
                } else {
                    $user->subscription()->create([
                        'plan_id'                => $newPlan->id,
                        'start_date'             => now(),
                        'end_date'               => $nextBillingDate,
                        'is_active'              => true,
                        'stripe_subscription_id' => $stripeSubscription->id,
                    ]);
                }
            } else {
                if ($currentPlan && $currentPlan->stripe_subscription_id) {
                    $stripeSubscription = StripeSubscription::retrieve($currentPlan->stripe_subscription_id);
                    $stripeSubscription->cancel();
                }

                if ($currentPlan) {
                    $currentPlan->update([
                        'plan_id'                => $newPlan->id,
                        'start_date'             => now(),
                        'end_date'               => $newPlan->duration ? now()->addDays($newPlan->duration) : null,
                        'is_active'              => true,
                        'stripe_subscription_id' => null,
                    ]);
                } else {
                    $user->subscriptions()->create([
                        'plan_id'                => $newPlan->id,
                        'start_date'             => now(),
                        'end_date'               => $newPlan->duration ? now()->addDays($newPlan->duration) : null,
                        'is_active'              => true,
                        'stripe_subscription_id' => null,
                    ]);
                }
            }

            return back()->with('success', "Plan switched to {$newPlan->name} successfully.");
        } catch (ApiErrorException $e) {
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Failed to switch plan: ' . $e->getMessage());
        }
    }

}
