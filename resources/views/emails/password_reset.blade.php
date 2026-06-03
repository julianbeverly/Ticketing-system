<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #0b57d0;">Resolve Password Reset</h2>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background-color: #0b57d0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Password</a>
        </p>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p>Thank you,<br>Resolve</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 0.8em; color: #777;">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
        <p style="font-size: 0.8em; color: #777; word-break: break-all;">{{ $resetUrl }}</p>
    </div>
</body>
</html>
