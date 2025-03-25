<?php

namespace App\Http\Controllers;

use App\Models\CoinPackage;
use App\Models\Point;
use App\Models\Setting;
use App\Models\TransferRequest;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use function Symfony\Component\String\u;

class RewardController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function ShowMyRewards(Request $request)
    {
        $rewards_prices = CoinPackage::orderBy('price', 'asc')->get();
        $user =  auth()->user();

        $reward_balance = $user->reward_balance;

        $payment_methods = $user->paymentMethods()->latest()->get();
        $payment_methods = collect($payment_methods)->sortByDesc('default')->values();

        $points = $request->get('points') ?? null;
        $price = $request->get('price') ?? null;
        $client_secret = $request->get('client_secret') ?? null;
        $payment_intent_id = $request->get('payment_intent_id') ?? null;

        $data = [
            'title' => 'My Rewards',
            'template' => 'reward.main',
        ];

        return view('with_login_common', compact('data', 'reward_balance', 'rewards_prices', 'points', 'price', 'client_secret', 'payment_methods','payment_intent_id'));
    }

    public function purchasePoints(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'price' => 'required|numeric',
            'package_id' => 'required',
        ]);

        $points = $validated['points'];
        $price = $validated['price'];
        $package_id = $validated['package_id'];
        $user = auth()->user();

        $coinPackage = CoinPackage::find($package_id);

        $totalAmount = calculatePackageAmount($coinPackage);
        $systemFee = getSetting('system_fee');
        $description = "Purchase points";
        try {
            $paymentIntent =  $this->stripeService->createPaymentIntentForPurchase($totalAmount, 'usd', $user->stripe_customer_id, $description,  [
                'user_id' => auth()->id(),
                'points' => $points,
                'package_price' => $coinPackage->price,
                'system_fee' => $systemFee,
                'package_id' => $package_id,
            ]);

            return redirect()->route('my-rewards', [
                'points' => $points,
                'price' => $price,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function payWithSavedCard(Request $request)
    {
        try {
            $user = auth()->user();
            $paymentMethodId = $request->payment_method_id;
            $paymentIntentId = $request->payment_intent;

            $paymentMethod = $user->paymentMethods()->where('id', $paymentMethodId)->first();
            if (empty($paymentMethod)) {
                return redirect()->route('my-rewards')->withErrors('Payment method not found.');
            }

            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            // Confirm the payment using the saved payment method
            $paymentIntent->confirm([
                'payment_method' => $paymentMethod->stripe_payment_method_id,
                'return_url' => route('payment-success'),
            ]);
            return response()->json([
                'success' => true,
                'payment_intent_id' => $paymentIntent->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');

        try {
            DB::beginTransaction();

            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            $userId = $paymentIntent->metadata->user_id;
            $points = $paymentIntent->metadata->points;
            $systemFee = $paymentIntent->metadata->system_fee;
            $packagePrice = $paymentIntent->metadata->package_price;
            $package_id = $paymentIntent->metadata->package_id;
            $amount = $paymentIntent->amount / 100;

            $user = User::findOrFail($userId);

            $description = "Purchased points via Stripe payment intent ID: {$paymentIntentId}";
            $existingRecord = Point::where('description', $description)
                ->where('amount', $amount)
                ->where('user_id', $userId)
                ->exists();

            if ($existingRecord) {
                DB::rollBack();
                return redirect()->route('my-rewards')->withErrors('Payment has already been processed.');
            }

            $purchase_point_rewards = getSetting('purchase_point_rewards');

            $reward_amount = calculateAmountFromCoins($purchase_point_rewards);
            Point::create([
                'user_id' => $user->id,
                'package_id' => $package_id,
                'type' => 'credit',
                'points' => $points,
                'price' => $packagePrice,
                'amount' => $amount,
                'system_fee' => $systemFee,
                'description' => $description,
                'trans_type' => Point::TRANS_TYPE_PURCHASED,
            ]);

            $user->reward_balance += $points;
            $user->save();

//            if(getSetting('has_purchase_point_rewards')){
//                Point::create([
//                    'user_id' => $user->id,
//                    'type' => 'credit',
//                    'points' => $purchase_point_rewards,
//                    'amount' => $reward_amount,
//                    'description' => "Purchased points reward",
//                ]);
//
//                $user->reward_balance += $purchase_point_rewards;
//                $user->save();
//            }

            DB::commit();

            return redirect()->route('my-rewards')->with('success', 'Points purchased successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('my-rewards')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function findUsers(Request $request)
    {
        $request->validate(['search_user' => 'required|string|min:1|max:255']);

        $users = User::where(function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search_user}%")
                ->orWhere('email', 'LIKE', "%{$request->search_user}%");
        })
            ->where('id', '!=', auth()->id())
            ->whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function assignPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
        ]);

        $user = User::find($request->user_id);
        $authUser = auth()->user();

        $credit = $authUser->points()->where('type', 'credit')->sum('points');
        $debit = $authUser->points()->where('type', 'debit')->sum('points');
        $availablePoints = $credit - $debit;
        $transferCoinLimit = getSetting('user_transfer_coint_limit');

        if ($availablePoints < $request->points) {
            return response()->json([
                'success' => false,
                'message' => "You don't have enough points to gift.",
            ]);
        }

        if ($request->points > $transferCoinLimit) {

            $checkExistsReq = $authUser->transferPointRequestFromUser()
                ->where('to_user_id',$request->user_id)
                ->where('status', TransferRequest::PENDING)
                ->exists();
            if($checkExistsReq){
                return response()->json([
                    'success' => false,
                    'message' => 'Your gift points request is already pending admin approval. You can\'t submit a new request until the previous one is completed.',
                ]);
            }

            TransferRequest::create([
                'from_user_id' => $authUser->id,
                'to_user_id' => $user->id,
                'points' => $request->points,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your transfer request has been sent for admin approval.',
            ]);
        }

        // Directly transfer points if within limit
        $this->processPointTransfer($authUser, $user, $request->points);

        return response()->json(['success' => true, 'message' => 'Points gifted successfully!']);
    }

    private function processPointTransfer($fromUser, $toUser, $points)
    {
        Point::create([
            'user_id' => $fromUser->id,
            'type' => 'debit',
            'points' => $points,
            'description' => "Points gifted to {$toUser->name}",
        ]);

        $fromUser->reward_balance -= $points;
        $fromUser->save();

        Point::create([
            'user_id' => $toUser->id,
            'type' => 'credit',
            'points' => $points,
            'trans_type' => Point::TRANS_TYPE_GIFT,
            'description' => "Gift received from {$fromUser->name}",
        ]);

        $toUser->reward_balance += $points;
        $toUser->save();
    }


}
