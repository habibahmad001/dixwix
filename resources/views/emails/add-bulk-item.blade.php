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
    <title>New Bulk Item Added in {{ $data['group_name'] }}</title>
</head>
<body>
<h1>New Items Have Been Added to {{ $data['group_name'] }}</h1>

<p>Hello {{ $data['member_name'] }},</p>

<p>{{ $data['creator_name'] }} has added multiple new items to your group, <strong>{{ $data['group_name'] }}</strong>.</p>

<p>Click the button below to view the item:</p>

<p>
    <a href="{{ route('show-group', $data['group_id']) }}"
       style="display: inline-block; padding: 10px 20px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 5px;">
        View Item
    </a>
</p>

<p>Thank you for being a part of {{ $data['group_name'] }}!</p>

<p>Best regards,</p>
<p>{{ config('app.name') }} Team</p>
</body>
</html>
