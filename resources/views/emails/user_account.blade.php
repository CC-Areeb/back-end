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
        background-color: beige;
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

    .main_content {
        padding: 50px;
        background-color: white;
    }
</style>

<body class="mail_body">
    <div class="main_content">
        <h3 class="mail_heading">Welcome, {{ $body['name'] }}!</h3>
        <p class="mail_text">
            We have created your account and sent you the details. Please do update your credentials as soon as
            possible.
        </p>
        Your Name: <span class="user_name">{{ $body['name'] }}</span><br><br>
        Email: <span class="user_email">{{ $body['email'] }}</span><br><br>
        Password: <span class="user_password">{{ $body['password'] }}</span><br><br>
        <p class="mail_sender">Best regards, {{ env('APP_NAME') }}
    </div>
</body>

</html>
