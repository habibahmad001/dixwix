<!DOCTYPE html>
<html>
<head>
    <title>Points Redemption Confirmation</title>
</head>
<body>
<h1>Hi {{ $data['userName'] }}, 🎉</h1>
<p>Your points redemption request has been received!</p>

<p><strong>Redeemed Points:</strong> {{ $data['points'] }}</p>
<p><strong>Equivalent Amount:</strong> {{ $data['amount'] }} {{ $data['currency'] }}</p>

@if($data['points'] <= $data['manualApprovalLimit'])
    <p>Your reward points have been successfully redeemed and transferred to your Stripe account.</p>
@else
    <p>Your redemption request has been submitted for approval. Once approved by the admin, the amount will be transferred to your Stripe account.</p>
    <p>You will be notified once the transfer is completed.</p>
@endif

<p><a href="{{ route('my-rewards') }}" style="padding: 10px 15px; border-radius: 30px; border: 2px solid #094042!important; background-color: #094042; color: #fff; text-decoration: none;">Check Your Rewards</a></p>

<p>Thank you for being a valuable member of {{ config('app.name') }}!</p>
</body>
</html>
