<?php

namespace App\Services;

use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Transfer;
use Stripe\Exception\ApiErrorException;
use App\Models\User;
use Stripe\PaymentMethod;
use function Carbon\settings;

//use App\Models\Transaction;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCustomer(User $user)
    {
        try {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();

            return $customer;
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createPaymentIntentForPurchase($amount, $currency = 'usd', $customerId = null, $description = null, $metadata= [])
    {
        try {

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'customer' => $customerId,
                'description' => $description,
                'metadata' => $metadata,
            ]);

            return $paymentIntent;
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createPaymentIntentForConfirm($amount, $currency = 'usd', $customerId = null, $paymentMethodId = null, $description = null, $metadata= [])
    {
        try {

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'customer' => $customerId,
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'description' => $description,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            return $paymentIntent;
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function retrievePaymentIntent($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent;
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function redeemPoints(User $user, $points, $paymentMethod, $description, $metadata)
    {
        $amount = calculateAmountFromCoins($points); // Example: 1 point = $0.10
        $amountCents = $amount * 100;

        try {

            $paymentIntent = $this->createPaymentIntentForConfirm($amount, 'usd', $user->stripe_customer_id, $paymentMethod->stripe_payment_method_id, $description, $metadata);
            $refund = $this->refundPoints($paymentIntent->id, $amountCents);

            $user->reward_balance -= $points;
            $user->save();
            return $refund;
        } catch (ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function savePaymentMethod(User $user, $paymentMethodId)
    {
        try {
            // Attach Payment Method to Customer
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

            // Set Payment Method as Default
           Customer::update($user->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            return $paymentMethod;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function onlySavePaymentMethod(User $user, $paymentMethodId)
    {
        try {
            // Attach Payment Method to Customer
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $user->stripe_customer_id]);

            return $paymentMethod;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function refundPoints($paymentIntentId, $amount)
    {
        $refund = Refund::create([
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
        ]);

        return $refund->id;
    }
}
