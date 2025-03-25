<!DOCTYPE html>
<html>
<head>
    <title>Redeem Request Rejected</title>
</head>
<body>
<h1>Hi {{ $data['userName'] }},</h1>

<p>Unfortunately, your redeem request of <strong>{{ $data['points'] }} coins</strong> has been rejected.</p>

<p>If you believe this was a mistake or need further details, please contact our support team.</p>

<p><strong>Reason for Rejection:</strong> {{ $data['status'] ?? 'Not specified' }}</p>

<p><a href="{{ url('/support') }}" style="padding: 10px 15px; background-color: #dc3545; color: #fff; text-decoration: none;">Contact Support</a></p>

<p>Thank you for understanding,<br>{{ config('app.name') }} Team</p>
</body>
</html>
