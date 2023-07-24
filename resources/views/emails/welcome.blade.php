<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title class="mail_title">Welcome!</title>
</head>

<style>
    .mail_body {
        padding: 20px 100px 0px 100px;
        background-color: gainsboro;
        font-family: sans-serif;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
</style>

<body class="mail_body">
    <h3 class="mail_heading">Welcome, {{ $body['name'] }}!</h3>
    <p class="mail_text">
        Thank you for registering on our website. To complete your registration, please use the following OTP:
    </p>
    <h4 class="mail_otp">{{ $body['otp'] }}</h4>
    <p class="mail_footer">If you did not sign up for an account on our website, you can ignore this email.</p>
    <p class="mail_sender">Best regards, {{ env('APP_NAME') }}<br>
</body>

</html>
