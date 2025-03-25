<!DOCTYPE html>
<html>
<head>
    <title>Redeem Request Approved</title>
</head>
<body>
<h1>Hi {{ $data['userName'] }}, 🎉</h1>

<p>Great news! Your redeem request of <strong>{{ $data['points'] }} coins</strong> has been **approved**.</p>

<p><strong>Equivalent Amount:</strong> {{ $data['amount'] }} {{ $data['currency'] }}</p>

<p>Your amount has been successfully transferred to your Stripe account.</p>

<p><a href="{{ route('my-rewards') }}" style="padding: 10px 15px; border-radius: 30px; border: 2px solid #094042!important; background-color: #094042; color: #fff; text-decoration: none;">View Your Rewards</a></p>

<p>Thank you for being a valuable member of {{ config('app.name') }}!</p>
</body>
</html>
