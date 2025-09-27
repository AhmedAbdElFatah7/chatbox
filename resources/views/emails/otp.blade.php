<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        h2 {
            color: #333333;
        }
        p {
            font-size: 16px;
            color: #555555;
        }
        .otp-code {
            display: inline-block;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .footer {
            font-size: 12px;
            color: #888888;
            margin-top: 20px;
        }
        .thank-you {
            font-size: 14px;
            color: #333333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello!</h2>
        <p>Your OTP code is:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This code will expire in 5 minutes.</p>
        <div class="footer">
            If you did not request this code, please ignore this email.
        </div>
        <div class="thank-you">
            Thank you
        </div>
    </div>
</body>
</html>