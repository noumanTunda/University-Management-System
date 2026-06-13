<?php
/**
 * OSUMS Installation Wizard
 * 
 * Creates the Super Admin account and locks setup after completion.
 */

// ─── Gatekeeping ───────────────────────────────────────────────
$lockFile = __DIR__ . '/../storage/installed.lock';

if (file_exists($lockFile)) {
    header('HTTP/1.1 403 Forbidden');
    die('<h1>403 Forbidden</h1><p>OSUMS is already installed. Remove <code>storage/installed.lock</code> to re-run setup.</p>');
}

// Bootstrap Laravel
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Resolve the HTTP kernel to boot the app fully
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

// Now we can use DB facade
use Illuminate\Support\Facades\DB;

try {
    $userCount = DB::table('users')->count();
    if ($userCount > 0) {
        file_put_contents($lockFile, date('Y-m-d H:i:s') . ' — Locked (users existed)');
        $kernel->terminate(request(), response('OSUMS is already installed.', 403));
        exit;
    }
} catch (\Exception $e) {
    // DB not accessible — allow setup to proceed
}

// ─── Handle Form Submission ────────────────────────────────────
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (empty($name)) $errors[] = 'Full Name is required.';
    if (empty($email)) $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        try {
            DB::beginTransaction();

            DB::table('users')->insert([
                'firstname' => $name,
                'lastname'  => '',
                'login'     => $email,
                'email'     => $email,
                'password'  => password_hash($password, PASSWORD_BCRYPT),
                'group'     => 'Admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            file_put_contents($lockFile, date('Y-m-d H:i:s') . ' — Installed by setup.php');

            DB::commit();
            $success = true;

            // Send welcome email
            try {
                $subject = 'Welcome to OSUMS — Your Admin Account';
                $body = "Hello $name,\n\n";
                $body .= "Your OSUMS administrator account has been created successfully.\n\n";
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $body .= "Login URL: $protocol://$host/login\n";
                $body .= "Username: $email\n";
                $body .= "Password: (the password you entered)\n\n";
                $body .= "Please login and change your password.\n\n— OSUMS Team";
                @mail($email, $subject, $body, "From: noreply@osums.edu\r\nReply-To: noreply@osums.edu");
            } catch (\Exception $e) {}
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Terminate the kernel so Laravel cleans up
if (isset($kernel)) {
    $kernel->terminate(request(), response(''));
}

// ─── Render Page ───────────────────────────────────────────────
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>OSUMS — Installation Wizard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#2A3F54; display:flex; align-items:center; min-height:100vh; }
.setup-card { background:#fff; border-radius:8px; padding:40px; max-width:560px; margin:0 auto; box-shadow:0 8px 24px rgba(0,0,0,.2); }
.setup-card h1 { color:#2A3F54; font-size:26px; margin-top:0; border-bottom:2px solid #1ABB9C; padding-bottom:12px; }
.help-block { font-size:12px; color:#999; }
</style>
</head>
<body>
<div class="container">
<div class="setup-card">
  <h1>OSUMS — Initial Setup</h1>
  <p class="text-muted">Create the Super Administrator account to get started.</p>

  <?php if ($success): ?>
  <div class="alert alert-success">
    <strong>Installation Complete!</strong><br><br>
    Your admin account has been created successfully.<br>
    A welcome email has been sent to <strong><?= htmlspecialchars($email) ?></strong>.<br><br>
    <a href="/login" class="btn btn-primary">Proceed to Login</a>
  </div>
  <?php else: ?>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <strong>Please fix the following errors:</strong>
    <ul style="margin:8px 0 0;padding-left:20px">
      <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>Full Name <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control" placeholder="e.g. John Doe"
             value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label>Email Address <span class="text-danger">*</span></label>
      <input type="email" name="email" class="form-control" placeholder="admin@osums.edu"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      <span class="help-block">Used as your login username.</span>
    </div>
    <div class="form-group">
      <label>Password <span class="text-danger">*</span></label>
      <input type="password" name="password" class="form-control" placeholder="Min 8 characters" minlength="8" required>
      <span class="help-block">Minimum 8 characters.</span>
    </div>
    <div class="form-group">
      <label>Confirm Password <span class="text-danger">*</span></label>
      <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password" minlength="8" required>
    </div>
    <hr>
    <button type="submit" class="btn btn-success btn-lg btn-block">
      Install & Create Admin
    </button>
  </form>
  <?php endif; ?>
</div>
</div>
</body>
</html>
