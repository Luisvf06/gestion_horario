<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <p>Hi,</p>
    <p>You requested a password reset. Click the link below to reset your password:</p>
    <p>
        <a href="{{ url('/reset-password/'.$token.'?email='.$email) }}">
            Reset Password
        </a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
