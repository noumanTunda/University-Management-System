<?php
/**
 * OSUMS — Installation Wizard
 * 
 * Creates the Super Administrator account and locks setup after completion.
 * Design matches the system's Gentelella admin theme.
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
html, body { height:100vh; margin:0; padding:0; font-family:"Helvetica Neue",Roboto,Arial,sans-serif; background:#2A3F54; }
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
<div class="center-wrap"><div class="error-card">
<div class="icon"><i class="glyphicon glyphicon-lock"></i></div>
<h1>Access Denied</h1>
<p>This system has already been configured and is currently operational.<br>If you need to reconfigure the system, please contact the system administrator for assistance.</p>
<a href="/login" class="btn">Return to Login</a>
</div></div>
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
use Illuminate\Support\Facades\Mail;

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

    if (!empty($username)) {
        try {
            if (DB::table('users')->where('login', $username)->exists()) {
                $errors[] = 'Username "' . htmlspecialchars($username) . '" is already taken.';
            }
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
            $userId = DB::getPdo()->lastInsertId();
            file_put_contents($lockFile, date('Y-m-d H:i:s') . ' — Installed by setup.php');
            DB::commit();
            $success = true;

            // Send welcome email via Laravel Mail (MailHog compatible)
            try {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $loginUrl = "$protocol://$host/login";
                
                Mail::send([], [], function($message) use ($email, $firstname, $username, $password, $loginUrl) {
                    $body = "Hello $firstname $lastname,\n\n"
                          . "Your OSUMS administrator account has been created successfully.\n\n"
                          . "Login URL: $loginUrl\n"
                          . "Username: $username\n"
                          . "Password: (the password you entered)\n\n"
                          . "Please login and change your password.\n\n"
                          . "— OSUMS Team";
                    $message->to($email, $firstname)
                            ->subject('Welcome to OSUMS — Admin Account')
                            ->setBody($body, 'text/plain');
                });
            } catch (\Exception $e) {
                // Email is optional
            }
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
<link href="assets/css/animate.min.css" rel="stylesheet">
<link href="assets/css/custom.min.css" rel="stylesheet">
<style>
html, body { height:100%; margin:0; background:#F7F7F7; font-family:"Helvetica Neue",Roboto,Arial,sans-serif; }
.setup-page { background:#F7F7F7; min-height:100vh; padding:40px 20px; }
.setup-container { max-width:820px; margin:0 auto; }
.setup-header { text-align:center; margin-bottom:30px; }
.setup-header img { max-width:100px; }
.setup-header h2 { color:#2A3F54; font-weight:700; font-size:24px; margin:10px 0 4px; }
.setup-header p { color:#73879C; font-size:13px; margin:0; }
.setup-card { background:#fff; border:1px solid #E6E9ED; border-radius:2px; padding:30px 35px; margin-bottom:20px; -webkit-column-break-inside:avoid; -moz-column-break-inside:avoid; column-break-inside:avoid; opacity:1; transition:all .2s ease; }
.setup-card .x_title { border-bottom:2px solid #E6E9ED; padding:1px 0 10px; margin-bottom:20px; }
.setup-card .x_title h2 { margin:5px 0 6px; float:left; font-size:18px; font-weight:400; color:#2A3F54; }
.setup-card .x_title .clearfix { clear:both; }
.form-group { margin-bottom:18px; }
.form-group label { font-weight:600; color:#2A3F54; font-size:12px; text-transform:uppercase; letter-spacing:0.3px; }
.form-group .form-control { height:42px; border:1.5px solid #CBD5E1; border-radius:3px; box-shadow:none; font-size:13px; color:#2A3F54; transition:all .2s ease; }
.form-group .form-control:focus { border-color:#337ab7; box-shadow:0 0 0 3px rgba(51,122,183,0.15); }
.form-group .help-block { font-size:11px; color:#94A3B8; margin-top:3px; }
.btn-success { background:#1ABB9C !important; border-color:#1ABB9C !important; color:#fff !important; font-weight:600; padding:10px 24px; border-radius:3px; }
.btn-success:hover { background:#169F85 !important; border-color:#169F85 !important; }
.btn-primary { background:#337ab7 !important; border-color:#337ab7 !important; color:#fff !important; font-weight:600; padding:10px 24px; border-radius:3px; }
.btn-primary:hover { background:#286090 !important; }
.info-grid { display:flex; gap:20px; flex-wrap:wrap; }
.info-grid .item { flex:1; min-width:140px; background:#f9f9f9; border-radius:4px; padding:14px 16px; border-left:3px solid #1ABB9C; }
.info-grid .item i { color:#1ABB9C; margin-right:6px; }
.info-grid .item span { font-size:12px; color:#73879C; }
.alert { border-radius:3px; font-weight:500; padding:12px 16px; margin-bottom:18px; font-size:13px; }
.alert-danger { background:#E74C3C; border-color:#E74C3C; color:#fff; }
.alert-success { background:#337ab7; border-color:#337ab7; color:#fff; }
.alert ul { margin:6px 0 0; padding-left:18px; }
@media(max-width:768px) { .setup-container { max-width:100%; } .info-grid { flex-direction:column; } }
</style>
</head>
<body class="nav-md">
<div class="container body">
<div class="main_container">
<div class="right_col" role="main">
<div class="setup-page">
<div class="setup-container">

<!-- Header -->
<div class="setup-header">
    <img src="assets/images/logo.jpg" alt="OSUMS">
    <h2>System Installation Wizard</h2>
    <p>Set up your OSUMS environment by creating the Super Administrator account.</p>
</div>

<?php if ($success): ?>

<!-- Success -->
<div class="setup-card">
    <div class="x_title"><h2><i class="fa fa-check-circle" style="color:#1ABB9C"></i> Installation Complete</h2><div class="clearfix"></div></div>
    <div class="alert alert-success">
        <strong>Success!</strong> The Super Administrator account has been created.<br>
        A welcome email has been sent to <strong><?= htmlspecialchars($email) ?></strong>.
    </div>
    <a href="/login" class="btn btn-primary"><i class="fa fa-sign-in"></i> Proceed to Login</a>
</div>

<?php else: ?>

<!-- Info Cards -->
<div class="info-grid" style="margin-bottom:20px">
    <div class="item"><i class="fa fa-shield"></i> <span>Bcrypt password hashing</span></div>
    <div class="item"><i class="fa fa-lock"></i> <span>Auto-lock after install</span></div>
    <div class="item"><i class="fa fa-envelope"></i> <span>Welcome email sent</span></div>
    <div class="item"><i class="fa fa-calendar"></i> <span>Ready for Academic Years</span></div>
</div>

<!-- Form Card -->
<div class="setup-card">
    <div class="x_title"><h2><i class="fa fa-user-plus"></i> Create Super Administrator</h2><div class="clearfix"></div></div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Please correct the following:</strong>
        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endif; ?>

    <form method="post" class="form-horizontal form-label-left">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>First Name <span class="required">*</span></label>
                    <input type="text" name="firstname" class="form-control" placeholder="John"
                           value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Last Name <span class="required">*</span></label>
                    <input type="text" name="lastname" class="form-control" placeholder="Doe"
                           value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Description / Title</label>
            <input type="text" name="description" class="form-control" placeholder="e.g. System Administrator"
                   value="<?= htmlspecialchars($_POST['description'] ?? '') ?>">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. admin"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <span class="help-block">Used to log in to the system.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="admin@osums.edu"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters" minlength="8" required>
                    <span class="help-block">Minimum 8 characters. Bcrypt hashed.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password" minlength="8" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox" style="font-weight:400;text-transform:none;letter-spacing:0;font-size:13px">
                <input type="checkbox" onclick="this.form.password.type=this.checked?'text':'password';this.form.password_confirm.type=this.checked?'text':'password'">
                Show Password
            </label>
        </div>

        <hr>
        <button type="submit" class="btn btn-success btn-lg" style="width:100%">
            <i class="fa fa-shield"></i> Install &amp; Create Super Admin
        </button>
    </form>
</div>

<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
