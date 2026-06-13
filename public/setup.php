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
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Access Denied — OSUMS</title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<style>
* { box-sizing: border-box; }
html, body { height:100vh; margin:0; padding:0; overflow:hidden; font-family:"Helvetica Neue",Roboto,Arial,sans-serif; background:#2A3F54; }
.center-wrap { display:flex; align-items:center; justify-content:center; height:100vh; }
.error-card { background:#fff; border-radius:8px; padding:50px 60px; max-width:500px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,.3); }
.error-card .icon { width:80px; height:80px; border-radius:50%; background:#E74C3C; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
.error-card .icon i { font-size:36px; color:#fff; }
.error-card h1 { color:#2A3F54; font-weight:700; font-size:24px; margin:0 0 8px; }
.error-card p { color:#73879C; font-size:14px; margin:0 0 25px; line-height:1.6; }
.error-card .btn { background:#337ab7; color:#fff; border:0; padding:10px 30px; border-radius:4px; font-weight:600; text-decoration:none; display:inline-block; }
.error-card .btn:hover { background:#286090; }
</style>
</head>
<body>
<div class="center-wrap">
<div class="error-card">
<div class="icon"><i class="glyphicon glyphicon-lock"></i></div>
<h1>Access Denied</h1>
<p>This system has already been configured and is currently operational.<br>If you need to reconfigure the system, please contact the system administrator for assistance.</p>
<a href="/login" class="btn">Return to Login</a>
</div>
</div>
</body>
</html><?php
    exit;
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
        header('HTTP/1.1 403 Forbidden');
        ?><!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Access Denied</title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#2A3F54;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif}.card{background:#fff;border-radius:8px;padding:40px;max-width:450px;text-align:center}.card h1{color:#E74C3C;font-size:22px}.card p{color:#73879C;font-size:13px;line-height:1.6}.card a{color:#337ab7;font-weight:600}</style>
</head>
<body><div class="card"><h1>Setup Unavailable</h1><p>The database already contains user records. The installation wizard cannot proceed.<br>Please contact your system administrator.</p><a href="/login">Back to Login</a></div></body>
</html><?php
        exit;
    }
} catch (\Exception $e) {}

// ─── Handle Form Submission ────────────────────────────────────
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['password_confirm'] ?? '';

    if (empty($firstname)) $errors[] = 'First Name is required.';
    if (empty($lastname)) $errors[] = 'Last Name is required.';
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email)) $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    // Check username uniqueness
    if (!empty($username)) {
        try {
            $exists = DB::table('users')->where('login', $username)->exists();
            if ($exists) $errors[] = 'Username "' . htmlspecialchars($username) . '" is already taken.';
        } catch (\Exception $e) {}
    }

    if (empty($errors)) {
        try {
            DB::beginTransaction();
            DB::table('users')->insert([
                'firstname'   => $firstname,
                'lastname'    => $lastname,
                'login'       => $username,
                'email'       => $email,
                'password'    => password_hash($password, PASSWORD_BCRYPT),
                'group'       => 'Admin',
                'description' => $desc,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            file_put_contents($lockFile, date('Y-m-d H:i:s') . ' — Installed by setup.php');
            DB::commit();
            $success = true;

            try {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $body = "Hello $firstname $lastname,\n\nYour OSUMS administrator account has been created.\n\n";
                $body .= "Login URL: $protocol://$host/login\nUsername: $username\nPassword: (as entered)\n\n— OSUMS Team";
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
.setup-split { display: flex; height: 100vh; width: 100vw; }
.setup-left {
    flex: 0 0 62%; width: 62%; height: 100vh; padding: 0;
    background: #2A3F54; position: relative;
    display: flex; align-items: center; justify-content: center; overflow: hidden;
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
.setup-left .brand-content h2 { color: #fff; font-weight: 700; font-size: 26px; margin: 0 0 8px; letter-spacing: -0.5px; }
.setup-left .brand-content p { color: rgba(255,255,255,0.7); font-size: 13px; max-width: 380px; margin: 0 auto 25px; line-height: 1.6; }
.setup-left .feature-list { text-align: left; max-width: 340px; margin: 0 auto; }
.setup-left .feature-list .item { color: rgba(255,255,255,0.8); padding: 7px 0; font-size: 12px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.setup-left .feature-list .item i { color: #1ABB9C; width: 20px; margin-right: 6px; }
.setup-right {
    flex: 0 0 38%; width: 38%; height: 100vh; display: flex;
    align-items: center; justify-content: center;
    padding: 4% 5%; background: #F7F7F7; border-left: 1px solid #E6E9ED; overflow-y: auto;
}
.setup-box { width: 100%; max-width: 420px; }
.setup-box .logo-area { text-align: center; margin-bottom: 6px; }
.setup-box .logo-area img { max-width: 120px; height: auto; }
.setup-box h4 { color: #2A3F54; font-weight: 700; font-size: 18px; margin: 14px 0 4px; letter-spacing: -0.5px; text-align: center; }
.setup-box p.subtitle { color: #73879C; font-size: 12px; margin: 0 0 20px; font-weight: 500; text-align: center; }
.form-group-custom { position: relative; margin-bottom: 12px; }
.form-group-custom .form-control {
    height: 42px !important; padding: 2px 12px !important;
    border: 1.5px solid #CBD5E1 !important; border-radius: 4px !important;
    background: #fff !important; box-shadow: none !important;
    font-size: 13px !important; color: #2A3F54 !important;
    font-weight: 500 !important; transition: all 0.2s ease !important;
}
.form-group-custom .form-control:focus { border-color: #337ab7 !important; box-shadow: 0 0 0 3px rgba(51,122,183,0.15) !important; }
.form-group-custom .form-control::placeholder { color: #94A3B8 !important; font-weight: 400; }
.form-group-custom label { font-size: 11px; color: #2A3F54; font-weight: 700; margin-bottom: 2px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
.setup-box .btn-primary {
    background: #337ab7 !important; border-color: #337ab7 !important;
    height: 46px; font-size: 13px !important; font-weight: 700;
    border-radius: 4px !important; text-transform: uppercase; letter-spacing: 0.5px;
    color: #fff !important; transition: all 0.2s ease; width: 100%;
}
.setup-box .btn-primary:hover { background: #286090 !important; border-color: #204d74 !important; }
.row-fields { display: flex; gap: 10px; }
.row-fields > div { flex: 1; }
.alert { border-radius: 4px; font-weight: 500; padding: 10px 14px; margin-bottom: 14px; font-size: 12px; }
.alert-danger { background: #E74C3C !important; border-color: #E74C3C !important; color: #fff !important; }
.alert-success { background: #337ab7 !important; border-color: #337ab7 !important; color: #fff !important; }
.alert ul { margin: 4px 0 0; padding-left: 16px; }
.help-block { font-size: 10px; color: #94A3B8; margin-top: 2px; }
@media (max-width: 991px) { .setup-left { display: none; } .setup-right { flex: 0 0 100%; width: 100%; border-left: none; padding: 30px 20px; } }
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
                <a href="/login" class="btn btn-primary" style="display:inline-block;width:auto;padding:8px 24px;height:auto;font-size:12px">Proceed to Login</a>
            </div>

            <?php else: ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Please fix:</strong>
                <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="post">
                <div class="row-fields">
                    <div class="form-group-custom">
                        <label>First Name</label>
                        <input type="text" name="firstname" class="form-control" placeholder="John"
                               value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" required>
                    </div>
                    <div class="form-group-custom">
                        <label>Last Name</label>
                        <input type="text" name="lastname" class="form-control" placeholder="Doe"
                               value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label>Description / Title</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g. System Administrator"
                           value="<?= htmlspecialchars($_POST['description'] ?? '') ?>">
                </div>

                <div class="form-group-custom">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. admin"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <span class="help-block">This will be used to log in.</span>
                </div>

                <div class="form-group-custom">
                    <label>Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="admin@osums.edu"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group-custom">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" minlength="8" required>
                    <span class="help-block">Minimum 8 characters. Will be bcrypt hashed.</span>
                </div>

                <div class="form-group-custom">
                    <label>Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password" minlength="8" required>
                </div>

                <div class="form-group-custom" style="margin-bottom:6px">
                    <label class="checkbox" style="font-weight:600;text-transform:none;letter-spacing:0;font-size:12px">
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
