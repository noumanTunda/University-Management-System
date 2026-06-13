<!DOCTYPE html>
<html>
<head><title>Account Created</title></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:500px;margin:auto;background:#fff;border-radius:8px;padding:30px">
  <h2 style="color:#2A3F54">Welcome to OSUMS</h2>
  <p>Dear <strong>{{$name}}</strong>,</p>
  <p>Your account has been created successfully. Below are your login credentials:</p>
  <table style="background:#f9f9f9;padding:15px;border-radius:6px;width:100%">
    <tr><td><strong>Login URL:</strong></td><td>{{url('/login')}}</td></tr>
    <tr><td><strong>Username:</strong></td><td><code>{{$login}}</code></td></tr>
    <tr><td><strong>Password:</strong></td><td><code>{{$password}}</code></td></tr>
  </table>
  <p style="color:#999;font-size:12px;margin-top:20px">Please change your password after first login.</p>
</div>
</body>
</html>
