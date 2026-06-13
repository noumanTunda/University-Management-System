<?php
/**
 * OSUMS — Installation Wizard
 * 
 * Creates the Super Administrator account and locks setup after completion.
 * Design matches the system's login page (Gentelella theme).
 */

// ─── Gatekeeping ───────────────────────────────────────────────
$lockFile = __DIR__ . '/../storage/installed.lock';

if (file_exists($lockFile)) {
    header('HTTP/1.1 403 Forbidden');
    die('<h1 style="color:#2A3F54;font-family:sans-serif;text-align:center;margin-top:20vh">403 Forbidden</h1>
         <p style="text-align:center;color:#73879C;font-family:sans-serif">OSUMS is already installed. Remove <code>storage/installed.lock</code> to re-run setup.</p>');
}

// Bootstrap Laravel
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    $userCount = DB::table('users')->count();
    if ($userCount > 0) {
        file_put_contents($lockFile, date('Y-m-d H:i:s') . ' — Locked (users existed)');
        $kernel->terminate(request(), response('', 403));
        exit;
    }
} catch (\Exception $e) {}

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

            try {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $body = "Hello $name,\n\nYour OSUMS administrator account has been created.\n\n";
                $body .= "Login URL: $protocol://$host/login\nUsername: $email\nPassword: (as entered)\n\n— OSUMS Team";
                @mail($email, 'Welcome to OSUMS — Admin Account', $body, "From: noreply@osums.edu\r\nReply-To: noreply@osums.edu");
            } catch (\Exception $e) {}
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

if (isset($kernel)) $kernel->terminate(request(), response(''));
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>OSUMS — Installation Wizard</title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<style>
* { box-sizing: border-box; }
html, body {
    height: 100vh; margin: 0; padding: 0; overflow: hidden;
    font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
    background-color: #F7F7F7; -webkit-font-smoothing: antialiased;
}
/* ─── Split Layout ─── */
.setup-split {
    display: flex; height: 100vh; width: 100vw;
}
/* Left Panel: Charcoal Slate */
.setup-left {
    flex: 0 0 62%; width: 62%; height: 100vh; padding: 0;
    background: #2A3F54; position: relative;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.setup-left .bg-grid {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
}
.setup-left .brand-content { position: relative; z-index: 2; text-align: center; padding: 40px; }
.setup-left .brand-content .icon-circle {
    width: 90px; height: 90px; border-radius: 50%; background: rgba(26, 187, 156, 0.15);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.setup-left .brand-content .icon-circle i { font-size: 44px; color: #1ABB9C; }
.setup-left .brand-content h2 { color: #fff; font-weight: 700; font-size: 28px; margin: 0 0 8px; letter-spacing: -0.5px; }
.setup-left .brand-content p { color: rgba(255,255,255,0.7); font-size: 14px; max-width: 400px; margin: 0 auto 30px; line-height: 1.6; }
.setup-left .feature-list { text-align: left; max-width: 360px; margin: 0 auto; }
.setup-left .feature-list .item { color: rgba(255,255,255,0.8); padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.setup-left .feature-list .item i { color: #1ABB9C; width: 22px; margin-right: 8px; }
/* Right Panel: Form */
.setup-right {
    flex: 0 0 38%; width: 38%; height: 100vh; display: flex;
    align-items: center; justify-content: center;
    padding: 5%; background: #F7F7F7; border-left: 1px solid #E6E9ED;
}
.setup-box { width: 100%; max-width: 420px; background: transparent; }
.setup-box .logo-area { text-align: center; margin-bottom: 10px; }
.setup-box .logo-area img { max-width: 140px; height: auto; }
.setup-box h4 { color: #2A3F54; font-weight: 700; font-size: 19px; margin: 20px 0 6px; letter-spacing: -0.5px; text-align: center; }
.setup-box p.subtitle { color: #73879C; font-size: 13px; margin: 0 0 28px; font-weight: 500; text-align: center; }
.form-group-custom { position: relative; margin-bottom: 18px; }
.form-group-custom .form-control {
    height: 50px !important; padding: 2px 14px !important;
    border: 1.5px solid #CBD5E1 !important; border-radius: 4px !important;
    background: #fff !important; box-shadow: none !important;
    font-size: 14px !important; color: #2A3F54 !important;
    font-weight: 600 !important; transition: all 0.2s ease !important;
}
.form-group-custom .form-control:focus {
    border-color: #337ab7 !important; box-shadow: 0 0 0 3px rgba(51,122,183,0.15) !important;
}
.form-group-custom .form-control::placeholder { color: #94A3B8 !important; font-weight: 500; }
.form-group-custom label { font-size: 12px; color: #2A3F54; font-weight: 700; margin-bottom: 4px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
.setup-box .btn-primary {
    background: #337ab7 !important; border-color: #337ab7 !important;
    height: 52px; font-size: 14px !important; font-weight: 700;
    border-radius: 4px !important; text-transform: uppercase; letter-spacing: 0.5px;
    color: #fff !important; transition: all 0.2s ease; width: 100%;
}
.setup-box .btn-primary:hover { background: #286090 !important; border-color: #204d74 !important; }
.alert { border-radius: 4px; font-weight: 600; padding: 12px 16px; margin-bottom: 18px; font-size: 13px; }
.alert-danger { background: #E74C3C !important; border-color: #E74C3C !important; color: #fff !important; }
.alert-success { background: #337ab7 !important; border-color: #337ab7 !important; color: #fff !important; }
.alert ul { margin: 6px 0 0; padding-left: 18px; }
.help-block { font-size: 11px; color: #94A3B8; margin-top: 4px; }
@media (max-width: 991px) {
    .setup-left { display: none; }
    .setup-right { flex: 0 0 100%; width: 100%; border-left: none; padding: 40px 20px; }
}
</style>
</head>
<body>
<div class="setup-split">

    <!-- LEFT: Brand Panel -->
    <div class="setup-left">
        <div class="bg-grid"></div>
        <div class="brand-content">
            <div class="icon-circle"><i class="fa fa-cogs"></i></div>
            <h2>System Installation</h2>
            <p>Welcome to the OSUMS setup wizard. This process will create the Super Administrator account and prepare the system for first use.</p>
            <div class="feature-list">
                <div class="item"><i class="fa fa-check-circle"></i> Create Super Admin account</div>
                <div class="item"><i class="fa fa-check-circle"></i> Secure password hashing (bcrypt)</div>
                <div class="item"><i class="fa fa-check-circle"></i> Automatic environment lock</div>
                <div class="item"><i class="fa fa-check-circle"></i> Welcome email notification</div>
                <div class="item"><i class="fa fa-check-circle"></i> Ready for Academic Year setup</div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form Panel -->
    <div class="setup-right">
        <div class="setup-box">
            <div class="logo-area">
                <img src="assets/images/logo.jpg" alt="OSUMS Logo">
            </div>
            <h4>Initial Setup</h4>
            <p class="subtitle">Create the Super Administrator to get started</p>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Installation Complete!</strong><br>
                Admin account created. A welcome email has been sent.<br><br>
                <a href="/login" class="btn btn-primary" style="display:inline-block;width:auto;padding:10px 28px;height:auto;font-size:13px">Proceed to Login</a>
            </div>

            <?php else: ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Please fix:</strong>
                <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group-custom">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. John Doe"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="form-group-custom">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@osums.edu"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <span class="help-block">Used as your login username.</span>
                </div>
                <div class="form-group-custom">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" minlength="8" required>
                    <span class="help-block">Minimum 8 characters. Will be bcrypt hashed.</span>
                </div>
                <div class="form-group-custom">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password" minlength="8" required>
                </div>
                <div class="form-group-custom" style="margin-bottom:8px">
                    <label class="checkbox" style="font-weight:600;text-transform:none;letter-spacing:0;font-size:13px">
                        <input type="checkbox" onclick="this.form.password.type=this.checked?'text':'password';this.form.password_confirm.type=this.checked?'text':'password'">
                        Show Password
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-shield"></i> Install &amp; Create Admin</button>
            </form>

            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>
