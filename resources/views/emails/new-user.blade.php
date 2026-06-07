<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Welcome to Resolve</h2>

<p>Hello {{ $user->name }},</p>

<p>Your account has been created successfully.</p>

<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Password:</strong> {{ $plainPassword }}</p>
<p><strong>Role:</strong> {{ $user->role }}</p>

<p>Please login and change your password after first login.</p>

<p>Thank you,<br>Resolve</p>
</body>
</html>
