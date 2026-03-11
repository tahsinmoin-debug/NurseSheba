<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="margin-bottom: 8px;">NurseSheba Password Reset</h2>
    <p>Your OTP code is:</p>
    <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px; color: #0288d1;">{{ $otpCode }}</p>
    <p>This OTP expires in {{ $expiresInMinutes }} minutes.</p>
    <p>If you did not request this, you can ignore this email.</p>
</body>
</html>
