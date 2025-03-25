<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333333;
        }

        p {
            color: #666666;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="https://dixwix.com/assets/media/logo.png" alt="Logo" style="margin-bottom: 20px;">
        <h2>Password Reset Request</h2>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p>If you didn't request a password reset, please ignore this email.</p>
        <a href="{{ $resetLink }}" class="btn">Reset Password</a>
        <div class="footer">
            <p>© {{ date('Y') }} Dixwix. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
