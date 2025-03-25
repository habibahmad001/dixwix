<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Sign Up Reward</title>
</head>
<body>
<h1>Welcome, {{ $data['userName'] }}! 🎉</h1>
<p>We are excited to have you at {{ config('app.name') }}.</p>
<p>As a welcome gift, we have credited <strong>{{ $data['points'] }} points</strong> to your account.</p>
<p>Start exploring and redeem your points now!</p>
<p><a href="{{ route('my-rewards') }}" style="padding: 10px 15px; border-radius: 30px; border: 2px solid #094042!important; background-color: #094042; color: #fff; text-decoration: none;">View Rewards</a></p>
<p>Happy earning!</p>
<p>Best regards,</p>
<p>{{ config('app.name') }} Team</p>
</body>
</html>
