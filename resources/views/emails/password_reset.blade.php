<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { background: #2A3F54; padding: 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 700; }
        .body { padding: 30px; }
        .body p { color: #555; font-size: 14px; line-height: 1.6; margin: 0 0 15px; }
        .credentials { background: #f8f9fa; border-left: 4px solid #1ABB9C; padding: 16px 20px; border-radius: 4px; margin: 20px 0; }
        .credentials strong { color: #2A3F54; display: inline-block; width: 120px; }
        .credentials .value { color: #333; font-weight: 600; }
        .btn { display: inline-block; background: #1ABB9C; color: #fff !important; text-decoration: none; padding: 12px 32px; border-radius: 4px; font-weight: 600; font-size: 14px; margin-top: 10px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>Your OSUMS password has been reset successfully. Below are your updated login credentials:</p>

            <div class="credentials">
                <p style="margin:5px 0"><strong>Login URL:</strong> <span class="value">{{ $loginUrl }}</span></p>
                <p style="margin:5px 0"><strong>Username:</strong> <span class="value">{{ $username }}</span></p>
                <p style="margin:5px 0"><strong>New Password:</strong> <span class="value">{{ $password }}</span></p>
            </div>

            <p style="text-align:center; margin-top:25px">
                <a href="{{ $loginUrl }}" class="btn">Login to OSUMS</a>
            </p>

            <p style="color:#999; font-size:12px; margin-top:20px;">For security reasons, please change your password after logging in.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} OSUMS — Open Source University Management System
        </div>
    </div>
</body>
</html>
