<?php

use App\Http\Controllers\BlogController;
use App\Mail\MailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\joinUsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\HowDoesItController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\RewardPlanController;
use App\Http\Controllers\RedeemRequestController;
use App\Http\Controllers\TransferRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/send-test-email', function () {
    $to = ['shoaibansari824@gmail.com', 'shoaibansari824@yopmail.com', 'admin2@yopmail.com']; // Change to the recipient email
    $subject = 'Test Email from Laravel';
    $emailData = [
        'title' => "Test Email from Laravel",
        'type' => "test_notification",
        'subject' => "Test Email from Laravel",
        'url' => url('/'),
        'action' => url('/'),
        'email' => 'admin@yopmail.com',
        'name' => 'Test User',
        'message' => 'This is a test email sent from Laravel using Hostinger SMTP.',
    ];

//    Mail::raw($message, function ($mail) use ($to, $subject) {
//        $mail->to($to)
//            ->subject($subject);
//    });
//    sendMail('emails.redeem-request', $to, "User", $subject, $emailData);
    $user = \App\Models\User::where('id', 1)->first();
    $user->notify(new \App\Notifications\GeneralNotification($emailData));

    Mail::to($to)->send(new MailService($emailData));

    return 'Test email sent successfully!';
});

Route::get('/link', function () {
    $target = '/home/dixwix/htdocs/dixwix.com/dixwix_main_files/storage/app/storage';
    $shortcut = '/home/dixwix/htdocs/dixwix.com/public';
    symlink($target, $shortcut);
});

Route::get('/clear-all-route9', function () {
    Artisan::call('optimize:clear');
    dd('All caches have been cleared.');
});

Route::get('/script_command_to_clear_cache_000009', function (Request $request) {

    $command = $request->input('command');
    Artisan::call($command);
    return response()->json(['message' => 'Cache cleared successfully for ' . $command], 200);
});

Route::get('/initial-setup', [SetupController::class, 'Main'])->name("initial-setup");

Route::get('/', [PageController::class, 'Homepage'])->name("home");

Route::get('/login', [PageController::class, 'Login'])->name("login")->middleware('guest');

Route::get('/signup', [PageController::class, 'Signup'])->name('signup')->middleware('guest');

Route::get('/group/join/{group_id}', [GroupController::class, 'joinGroup'])->name('group.join');
Route::get('/signup_via_group_invite/{referrer_id}/{group_id}/{group_type_id}', [PageController::class, 'SignupViaGroup'])->name('signup_via_group_invite');

Route::get('/contact-us', [PageController::class, 'Contactus'])->name('contactus');

Route::get('/how-it-works', [PageController::class, 'HowItWorks'])->name('howitworks');

Route::get('/pricing', [PageController::class, 'Pricing'])->name('pricing');

Route::get('/faq', [PageController::class, 'Faq'])->name('faq');

Route::get('/blog', [PageController::class, 'Blog'])->name('blog');

Route::get('blog/{slug}', [BlogController::class, 'postBySlug'])->name('post-slug');


Route::get('/security', [PageController::class, 'Security'])->name('security');

Route::get('/support', [PageController::class, 'Support'])->name('support');

Route::post('/store-user', [UserController::class, 'RegisterUser'])->name("store-user");
Route::post('/add-group-location', [UserController::class, 'addGroupLocation']);
Route::post('/store-via-user', [UserController::class, 'StoreViaUser'])->name("store-via-user");
Route::post('/login-user', [UserController::class, 'CustomLogin'])->name("login-user");
Route::get('/google-login', [UserController::class, 'redirectToGoogle'])->name("google-login");
Route::get('/google-callback', [UserController::class, 'GoogleCallback'])->name('google-callback');
Route::get('/activate-account', [UserController::class, 'ActivateAccount'])->name('activate-account');

Route::get('/signup_via_group_invite/{referrer_id}/{group_id}/{group_type_id}', [PageController::class, 'SignupViaGroup'])->name('signup_via_group_invite');
Route::get('/group/accept/{group_id}/{group_type_id}/{email_id}/{created_by}', [GroupController::class, 'AcceptInvite'])->name('accept-invite');
Route::get('/group/reject/{group_id}/{email_id}', [GroupController::class, 'RejectInvite'])->name('reject-invite');

Route::post('/store-contact', [PageController::class, 'SaveContactUs'])->name("store-contact");

Route::get('/quickbooks/connect', [\App\Http\Controllers\QuickBookController::class, 'connect']);
Route::get('/qb/customer/store', [\App\Http\Controllers\QuickBookController::class, 'store']);
Route::get('/quickbooks/callback', [\App\Http\Controllers\QuickBookController::class, 'callback'])->name('quickbooks.callback');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', function () {
        return redirect('dashboard');
    });

    Route::get('/dashboard', [UserController::class, 'Dashboard'])->name("dashboard");
    Route::get('/user-type', [PageController::class, 'SelectUserType'])->name("user-type");
    Route::get('/save-user-type/{type_id}', [PageController::class, 'SaveUserType'])->name("save-user-type");
    Route::get('/my-items', [BookController::class, 'ShowMyBooks'])->name("my-items");
    Route::get('/borrowed-items', [BookController::class, 'BorrowedItems'])->name("borrowed-items");
    Route::get('/bulk-items-edit', [BookController::class, 'bulkItemsEdit'])->name("bulk-items-edit");
    Route::get('/bulk-items-delete', [BookController::class, 'bulkItemsDeleteWorking'])->name("bulk-items-delete");
    Route::post('/bulk-update-items', [BookController::class, 'bulkUpdateItems'])->name('bulk-update-items');
    Route::post('/search-items', [BookController::class, 'searchItems'])->name('search-items');
    Route::post('/add-to-group', [BookController::class, 'addToGroup'])->name('add-to-group');
    Route::get('/all-items', [BookController::class, 'ShowAllItems'])->name("all-items");
    Route::get('/add-item', [BookController::class, 'AddBook'])->name("add-book");
    Route::get('/edit-item/{id?}', [BookController::class, 'AddBook'])->name("edit-book");
    Route::post('/store-item', [BookController::class, 'StoreBook'])->name("store-book");
    Route::post('/store-item-api', [BookController::class, 'StoreBookAPI'])->name("store-item-api");
    Route::post('/update-item-api/{id}', [BookController::class, 'UpdateBookAPI'])->name("update-item-api");
    Route::delete('/delete-item', [BookController::class, 'DeleteBook'])->name("delete-item");
    Route::get('/show-item/{id?}', [BookController::class, 'ShowBook'])->name("show-item");
    Route::post('/search-item', [BookController::class, 'ShowSearchItems'])->name("search-item");
    Route::post('/import-item', [BookController::class, 'ImportFromCSV'])->name("import-item-csv");
    Route::post('/set-book-status', [BookController::class, 'SetBookStatus']);
    Route::post('/return-book', [BookController::class, 'ReturnBook']);
    Route::post('/admin-return-book', [BookController::class, 'AdminReturnBook']);
    Route::post('/reserve-approval', [BookController::class, 'ApproveDisapproveReservation'])->name("reserve-approval");
    Route::post('/reject-return-request', [BookController::class, 'RejectReturnRequest'])->name("reject-return-request");

    Route::get('/my-rewards', [RewardController::class, 'ShowMyRewards'])->name("my-rewards");
    Route::post('/purchase-points', [RewardController::class, 'purchasePoints'])->name('purchase-points');
    Route::post('/pay-with-saved-card', [RewardController::class, 'payWithSavedCard'])->name('pay-with-saved-card');
    Route::get('/payment-success', [RewardController::class, 'paymentSuccess'])->name('payment-success');
    Route::get('/payment-error', [RewardController::class, 'paymentError'])->name('payment-error');
    Route::get('find-users', [RewardController::class, 'findUsers']);
    Route::post('assign-points', [RewardController::class, 'assignPoints']);

    Route::get('/my-groups', [GroupController::class, 'ShowMyGroups'])->name("my-groups");
    Route::get('/search-users', [GroupController::class, 'searchUsers']);
    Route::get('/all-groups', [GroupController::class, 'ShowAllGroups'])->name("all-groups");
    Route::get('/lender-groups', [GroupController::class, 'ShowLenderGroups'])->name("lender-groups");
    Route::get('/borrower-groups', [GroupController::class, 'ShowBorrowerGroups'])->name("borrower-groups");
    Route::get('/join-group', [GroupController::class, 'JoinGroup'])->name("join-group");
    Route::get('/add-group', [GroupController::class, 'AddGroup'])->name("add-group");
    Route::get('/show-group/{id?}', [GroupController::class, 'ShowGroup'])->name("show-group");
    Route::get('/edit-group/{id}', [GroupController::class, 'AddGroup'])->name("edit-group");
    Route::delete('/delete-group', [GroupController::class, 'DeleteGroup'])->name("delete-group");
    Route::post('/store-group', [GroupController::class, 'StoreGroup'])->name("store-group");
    Route::get('group/get-members/{group_id}/{group_type_id}', [GroupController::class, 'GetMembersToAdd'])->name("get-members-to-add");
    Route::post('/update-group-status', [GroupController::class, 'UpdateGrouptatus'])->name('update-group-status');
    Route::get('/group-invitation', [GroupController::class, 'HandleInvitation'])->name('accept-invitation');

    // Route::post('/add-comment', [CommentController::class, 'addComment'])->name('add-comment');
    // Route::get('/comments/{itemId}', [CommentController::class, 'getComments'])->name('comments');
    // Route::delete('/comments/delete-all', [CommentController::class, 'deleteAll'])->name('comments.deleteAll');
    // Route::delete('/comments/{id}', [CommentController::class, 'delete'])->name('comments.delete');

    Route::post('/add-review', [ReviewController::class, 'addReview'])->name('add-review');
    Route::get('/reviews/{itemId}', [ReviewController::class, 'getReviews'])->name('reviews');
    Route::delete('/reviews/delete-all', [ReviewController::class, 'deleteAll'])->name('reviews.deleteAll');
    Route::delete('/reviews/{id}', [ReviewController::class, 'delete'])->name('reviews.delete');

    Route::get('/admin-dashboard', [AdminController::class, 'Dashboard'])->name("admin-dashboard");

    Route::get('/edit-profile', [UserController::class, 'EditUser'])->name("edit-profile");
    Route::post('/store-profile', [UserController::class, 'StoreUser'])->name("store-profile");
    Route::post('/users/delete-account', [UserController::class, 'DeleteAccount'])->name("delete-account");
    Route::get('/users/my-account', [UserController::class, 'MyAccount'])->name("my-account");
    Route::post('/users/switch-plan', [UserController::class, 'SwitchPlan'])->name("switch-plan");
    Route::post('/users/redeem-rewards', [UserController::class, 'redeemReward'])->name("redeem-rewards");
    Route::post('/users/save-payment-method-only', [UserController::class, 'saveCustomerPaymentMethod'])->name('save-payment-method-only');
    Route::post('/users/save-payment-method', [UserController::class, 'SavePaymentMethod'])->name('save-payment-method');
    Route::post('/users/remove-payment-method', [UserController::class, 'RemovePaymentMethod'])->name('remove-payment-method');
    Route::post('/users/make-default-payment-method', [UserController::class, 'MakeDefaultPaymentMethod'])->name('make-default-payment-method');

    Route::get('/my-notifications', [NotificationController::class, 'index'])->name("my-notifications");
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    Route::get('/settings/add-category', [AdminController::class, 'AddCategory'])->name("add-category");
    Route::get('/settings/edit-category/{id}', [AdminController::class, 'AddCategory'])->name("edit-category");
    Route::post('/settings/store-category', [AdminController::class, 'StoreCategory'])->name("store-category");
    Route::post('/settings/delete-category', [AdminController::class, 'deleteCategory'])->name("delete-category");
    Route::get('/settings/view-all-categories', [AdminController::class, 'ViewAllCategories'])->name("view-all-categories");
    Route::get('/settings/add-settings', [AdminController::class, 'AddSettings'])->name("add-settings");
    Route::get('/settings/edit-settings/{id}', [AdminController::class, 'AddSettings'])->name("edit-settings");
    Route::post('/settings/store-settings', [AdminController::class, 'StoreSettings'])->name("store-settings");
    Route::get('/settings/view-all-settings', [AdminController::class, 'ViewAllSettings'])->name("view-all-settings");

    Route::middleware('isAdmin')->group(function () {

        Route::get('/users/all-users', [AdminController::class, 'allUsers'])->name("all-users");
        Route::get('/users/add-user', [AdminController::class, 'addUser'])->name("add-user");
        Route::post('/users/store-user', [AdminController::class, 'storeUser'])->name("admin-add-user");
        Route::get('/users/edit-user/{id}', [AdminController::class, 'editUser'])->name("edit-user");
        Route::put('/users/update-user/{id}', [AdminController::class, 'updateUser'])->name("update-user");
        Route::post('/users/delete-user', [AdminController::class, 'deleteUser'])->name("delete-user");

        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name("subscription-plans");
        Route::get('/subscription-plans/{id}', [SubscriptionPlanController::class, 'edit'])->name("edit-plan");
        Route::put('/subscription-plans/{id}', [SubscriptionPlanController::class, 'update'])->name("update-plan");


        Route::get('/reward-plans', [RewardPlanController::class, 'index'])->name("reward-plans");
        Route::get('/create/reward-plans', [RewardPlanController::class, 'create'])->name("create-reward");
        Route::post('/store/reward-plans', [RewardPlanController::class, 'store'])->name("store-reward");
        Route::get('/reward-plans/{id}', [RewardPlanController::class, 'edit'])->name("edit-reward");
        Route::put('/reward-plans/{id}', [RewardPlanController::class, 'update'])->name("update-reward");

        Route::get('/redeem-requests', [RedeemRequestController::class, 'index'])->name("redeem-requests");
        Route::get('/redeem-requests/{id}', [RedeemRequestController::class, 'edit'])->name("edit-redeem-requests");
        Route::put('/redeem-requests/{id}', [RedeemRequestController::class, 'update'])->name("update-redeem-requests");

        Route::get('/transfer-point-requests', [TransferRequestController::class, 'index'])->name("transfer-requests");
        Route::get('/transfer-point-requests/{id}', [TransferRequestController::class, 'edit'])->name("edit-transfer-requests");
        Route::put('/transfer-point-requests/{id}', [TransferRequestController::class, 'update'])->name("update-transfer-requests");

        Route::post('/admin/backup', [AdminController::class, 'runBackup'])->name('admin.backup');

        Route::resource('loan-rules', RentalController::class);

        Route::get('/admin/overdue-rent-items', [RentalController::class, 'overdueRentItems'])->name('admin.overdue-rent-items');
        Route::get('/admin/loan-history', [RentalController::class, 'loanHistory'])->name('admin.loan-history');
        Route::get('/admin/reservation-report', [RentalController::class, 'reservationReport'])->name('admin.reservation-report');

        Route::resource('blog-post', BlogController::class);

    });

    Route::get('/make-barcode', [BookController::class, 'MakeBarcode'])->name("make-barcode");
    Route::post('/view-barcode', [BookController::class, 'ViewBarcode'])->name("view-barcode");
    Route::get('/show-copies/{id}', [BookController::class, 'ShowEntries'])->name("show-copies");

    Route::post('/invite-user', [GroupController::class, 'InviteUser'])->name("invite-user");
    Route::post('/update-member-role', [GroupController::class, 'UpdateMemberRole'])->name("update-member-role");
    Route::post('/update-member-status', [GroupController::class, 'UpdateMemberStatus'])->name("update-member-status");

    Route::match(['get', 'post'], '/accept-member-request', [GroupController::class, 'AcceptRequest'])->name('accept-member-request');

    Route::post('/add-group-member', [GroupController::class, 'AddGroupMember'])->name("add-group-member");

    Route::delete('/delete-member-from-group', [GroupController::class, 'DeleteMemberFromGroup'])->name("delete-member-from-group");

    Route::patch('/accept-member-in-group', [GroupController::class, 'AcceptGroupMember'])->name("accept-member-in-group");

    Route::get('/membership', [MembershipController::class, 'MembershipPage'])->name("membership");
    Route::get('/activate-membership/{plan_id}', [MembershipController::class, 'ActivateMembership'])->name("activate-membership");
    Route::get('/confirm-group-add/{id}/{member_id}/{created_by}', [GroupController::class, 'ConfirmGroupAdd'])->name("confirm-group-add");
    Route::get('/reject-group-add/{id}/{member_id}', [GroupController::class, 'RejectGroupAdd'])->name("reject-group-add");

    Route::post('/ticket/store', [TicketController::class, 'store'])->name("ticket.store");
    Route::post('/ticket/comment/store', [TicketController::class, 'ticketCommentStore'])->name("ticket.comment.store");
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::delete('/ticket/comments/{comment}', [TicketController::class, 'ticketCommentDestroy'])->name('comments.destroy');
    Route::post('/ticket/{ticket}/status', [TicketController::class, 'updateStatus']);

    Route::post('/post/store', [PostController::class, 'store'])->name("post.store");
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::post('/post/comment/store', [PostController::class, 'postCommentStore'])->name('post.comment.store');
    Route::delete('/post/comments/{id}', [CommentController::class, 'postCommentDestroy'])->name('post.comments.destroy');

    Route::get('/get-item-details', [UserController::class, 'getItemDetails']);
    // Route::get('/showmymenu', [UserController::class, 'showmymenu']);
    Route::get('/showmymenu/{id}', [UserController::class, 'showMenu'])->name('showmymenu');


});

Route::get('/reject-all-invitations', [GroupController::class, "RejectGroupInvitaions"])->name("reject-all-invitations");
Route::post('/reload-group-by-type', [GroupController::class, 'ReloadGroupsByType'])->name("reload-group-by-type");
Route::post('/reload-category-by-type', [GroupController::class, 'ReloadCatsByType'])->name("reload-category-by-type");
Route::get('/get-qr-codes', [BookController::class, 'GetQRCodes'])->name("get-qr-codes");
Route::post('/request-join', [GroupController::class, 'RequestJoin'])->name("request-join");

Route::get('/logout', [UserController::class, 'Logout'])->name('logout');
Route::get('/run-sch', [UserController::class, 'runScheduler']);

Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
Route::get('/how-does-it-work', [HowDoesItController::class, 'index'])->name('how_d_it_wo');
Route::get('/join-us', [joinUsController::class, 'index'])->name('join.us');


Route::get('/terms-of-service', [HowDoesItController::class, 'tos'])->name('terms-of-service');
Route::get('/our-story', [PageController::class, 'ourstory'])->name('ourstory');
Route::get('/privacy', [PageController::class, 'privacypolicy'])->name('privacy');
Route::get('/careers', [PageController::class, 'careers'])->name('careers');
Route::get('/your-security-matters', [PageController::class, 'yoursecurity'])->name('your-security-matters');
Route::get('/site-map', [PageController::class, 'sitemap'])->name('site-map');
