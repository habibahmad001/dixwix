<?php

namespace App\Http\Controllers;

use App\Mail\MailService;
use App\Mail\RedeemRewardMail;
use App\Models\Point;
use App\Models\RewardTransaction;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\ApiErrorException;


class RedeemRequestController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function index()
    {
        $data['title'] = 'Redeem Requests';
        $data['template'] = 'admin.reward.transaction-list';
        $transactions = RewardTransaction::orderBy('created_at', 'desc')->get();

        return view('with_login_common', compact('data', 'transactions'));
    }

    public function edit($id)
    {
        $transaction = RewardTransaction::findOrFail($id);
        $data['title'] = 'Edit Redeem Request';
        $data['template'] = 'admin.reward.edit-redeem-request';

        return view('with_login_common', compact('data', 'transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = RewardTransaction::find($id);

        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

//        $user = $transaction->user;
        $user =  User::find($transaction->user_id);

        $paymentMethod = $user->paymentMethods()->where('default', true)->first();

        if (!$paymentMethod && $request->status == RewardTransaction::APPROVED) {
            return back()->with('error', "User has not set a default payment method. Please set the default payment method before approving the request.");
        }

        try {

            $transaction->status = $request->status;
            $transaction->approved_by = auth()->user()->id;
            $transaction->approved_at = now();
            $transaction->status = $request->status;

            if($request->status == RewardTransaction::APPROVED){

                $description = "Your reward points have been transferred";

                $paymentResponse = $this->stripeService->redeemPoints($user,$transaction->coins, $paymentMethod, $description, []);

                Point::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'points' => $transaction->coins,
                    'data' => $paymentResponse,
                    'amount' => $transaction->amount,
                    'description' => $description,
                    'trans_type' => Point::TRANS_TYPE_REWARD,
                ]);
            }

            $this->handleRedeemNotification($request->status, $user, $transaction);
            $transaction->save();

            return redirect()->back()->with('success', 'Request updated successfully!');

        } catch (ApiErrorException $e) {
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to switch plan: ' . $e->getMessage());
        }
    }

    protected function handleRedeemNotification($status, $user, $transaction)
    {
        $recipientEmail = $user->email;
        $points = $transaction->coins;
        $emailData = [];
        $notificationData = [];

        if ($status == RewardTransaction::APPROVED) { // Approved

            $notificationData = [
                'title'   => 'Redeem Request Approved 🎉',
                'type'    => 'redeem_approved',
                'subject' => 'Your Redeem Request Approved',
                'message' => 'Congratulations! Your redeem request of ' . $points . ' coins has been approved successfully.',
                'user_id' => $user->id,
                'url'     => route('my-rewards'),
                'action'  => 'View Rewards',
            ];

            $emailData = [
                'name' => $user->name,
                'subject' => 'Congratulations! Your Redeem Request Approved 🎉',
                'view' => 'redeem-request-accept',
                'status' => RewardTransaction::$STATUS_TEXT[1],
                'coins' => $points,
            ];

        } elseif ($status ==  RewardTransaction::REJECTED) { // Rejected

            $emailData = [
                'name' => $user->name,
                'subject' => 'Redeem Request Rejected ❌',
                'view' => 'redeem-request-reject',
                'status' => RewardTransaction::$STATUS_TEXT[2],
                'coins' => $points,
            ];

            $notificationData = [
                'title'   => 'Redeem Request Rejected ❌',
                'type'    => 'redeem_rejected',
                'subject' => 'Your Redeem Request Rejected',
                'message' => 'Sorry! Your redeem request of ' . $points . ' coins has been rejected.',
                'user_id' => $user->id,
                'url'     => url('/'),
                'action'  => 'Contact Support',
            ];
        }

        if (!empty($notificationData)) {
            $user->notify(new GeneralNotification($notificationData));
        }

        if (!empty($emailData)) {
            $this->sendRedeemRewardMail($user,$emailData);
        }
    }

    private function sendRedeemRewardMail($user, $mailData)
    {
        // Prepare email data
        $data = [
            'userName' => $user->name,
            'points'   => $mailData['coins'],
            'status'  =>  $mailData['status'],
            'subject'  => $mailData['subject'],
            'currency'  => 'USD',
            'view'     => $mailData['view'],
        ];

        // Send email
        Mail::to($user->email)->send(new RedeemRewardMail($data));
    }
}
