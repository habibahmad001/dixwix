<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <p>Hi User,</p>

    @if ($email_message)
    <p>
        <b><i>"{{ $email_message }}"</i></b>
    </p>
    @endif
    @if ($customer_email)
    <p>
        <b>Details:</b>:<br/> <?=$customer_email?>
    </p>
    @endif
</body>

</html>