<!DOCTYPE html>
<html>
<head>
    <title>Redemption Request</title>
</head>
<body>
<p>Hello {{ $data['name'] }},</p>

<p>
    {{ $data['message']}}
</p>

<p>Thank you for using {{ config('app.name') }}!</p>

<p>Best regards,</p>
<p>{{ config('app.name') }} Team</p>
</body>
</html>
