<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OSUMS | Forgot Password</title>
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/custom.min.css')}}" rel="stylesheet">
    <style>
      html, body {
        height: 100vh !important; max-height: 100vh !important;
        margin: 0 !important; padding: 0 !important;
        overflow: hidden !important;
        background-color: #F7F7F7 !important;
        -webkit-font-smoothing: antialiased;
      }
      .login-split-container { display: flex; height: 100vh; width: 100vw; }
      .login-slide-side {
        flex: 0 0 62%; width: 62%; height: 100vh; padding: 0 !important;
        background-color: #2A3F54; position: relative;
      }
      .slide-inner-frame { width: 100%; height: 100%; border-radius: 0 !important; overflow: hidden; position: relative; }
      .carousel-wrapper-box, .carousel-wrapper-box .carousel, .carousel-wrapper-box .carousel-inner { height: 100% !important; width: 100% !important; position: relative; }
      .carousel-wrapper-box .carousel-item { position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; display: block !important; opacity: 0; z-index: 1; transition: opacity 1.3s ease-in-out !important; }
      .carousel-wrapper-box .carousel-item.active { opacity: 1 !important; z-index: 2; }
      .carousel-wrapper-box .carousel-item img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
      .hero-gradient-overlay {
        position: absolute; bottom: 0; left: 0; width: 100%; height: 40%;
        background: linear-gradient(to top, rgba(42,63,84,0.98) 0%, rgba(42,63,84,0.6) 60%, rgba(42,63,84,0) 100%);
        display: flex; align-items: flex-end; padding: 50px; z-index: 10; pointer-events: none;
      }
      .hero-overlay-text h3 { color: #fff; font-weight: 700; font-size: 2.2rem; margin-bottom: 10px; letter-spacing: -0.5px; }
      .hero-overlay-text p { color: rgba(255,255,255,0.85); font-size: 1.25rem; margin: 0; line-height: 1.5; max-width: 90%; }
      .login-form-side {
        flex: 0 0 38%; width: 38%; height: 100vh; display: flex;
        align-items: center; justify-content: center; padding: 5%;
        background-color: #F7F7F7 !important; border-left: 1px solid #E6E9ED;
      }
      .isolated-login-box { width: 100%; max-width: 420px; background: transparent !important; }
      .isolated-login-box h4 { color: #2A3F54; font-weight: 700; font-size: 1.9rem !important; line-height: 1.35; margin-top: 25px; margin-bottom: 8px; letter-spacing: -0.5px; }
      .isolated-login-box p.subtitle { color: #73879C; font-size: 1.25rem !important; margin-bottom: 35px; font-weight: 500; }
      .form-group-custom { position: relative; margin-bottom: 22px; }
      .form-control {
        height: 50px !important; padding: 2px 14px !important;
        border: 1.5px solid #CBD5E1 !important; border-radius: 4px !important;
        background-color: #fff !important; box-shadow: none !important;
        font-size: 1.35rem !important; color: #2A3F54 !important;
        line-height: 44px !important; font-weight: 600 !important;
        transition: all 0.2s ease-in-out !important;
      }
      .form-control:focus { border-color: #337ab7 !important; background-color: #fff !important; box-shadow: 0 0 0 3px rgba(51,122,183,0.15) !important; }
      .form-control::placeholder { color: #94A3B8 !important; font-size: 1.35rem !important; font-weight: 500; }
      .btn-primary {
        background-color: #337ab7 !important; border-color: #337ab7 !important;
        height: 52px; font-size: 1.4rem !important; font-weight: 700;
        border-radius: 4px !important; box-shadow: none !important;
        transition: all 0.2s ease; margin-top: 5px;
        text-transform: uppercase; letter-spacing: 0.5px; color: #fff !important;
      }
      .btn-primary:hover { background-color: #286090 !important; border-color: #204d74 !important; transform: translateY(-1px); }
      .btn-primary:active { transform: translateY(0); }
      a.forgot-link { font-size: 1.25rem !important; color: #337ab7; text-decoration: none; font-weight: 700; transition: color 0.2s ease; }
      a.forgot-link:hover { color: #286090; text-decoration: underline; }
      .alert { border-radius: 4px; font-weight: 600; padding: 12px 16px; margin-bottom: 18px; }
      .alert-danger { background-color: #E74C3C !important; border-color: #E74C3C !important; color: #fff !important; }
      .alert-success { background-color: #337ab7 !important; border-color: #337ab7 !important; color: #fff !important; }
      @media (max-width: 991px) {
        html, body { overflow: auto !important; height: auto !important; max-height: none !important; }
        .login-split-container { flex-direction: column; height: auto; }
        .login-slide-side { width: 100%; flex: 0 0 100%; height: 40vh; }
        .login-form-side { width: 100%; flex: 0 0 100%; height: auto; padding: 40px 20px; border-left: none; }
        .hero-gradient-overlay { height: 65%; padding: 25px; }
      }
    </style>
</head>
<body>
<div class="login-split-container">

    <!-- LEFT: Brand Panel -->
    <div class="login-slide-side">
        <div class="slide-inner-frame">
            <div class="carousel-wrapper-box">
                <div id="loginBannerCarousel" class="carousel slide" data-ride="carousel" data-interval="5000" data-pause="false">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ URL::asset('assets/images/img1.jpg') }}" alt="Campus">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ URL::asset('assets/images/img2.jpg') }}" alt="Academic">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ URL::asset('assets/images/img3.jpg') }}" alt="Student Life">
                        </div>
                    </div>
                </div>
                <div class="hero-gradient-overlay">
                    <div class="hero-overlay-text">
                        <h3>Password Recovery</h3>
                        <p>Reset your account password securely. A new password will be sent to your registered email.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form Panel -->
    <div class="login-form-side">
        <div class="isolated-login-box">

            <form method="post" action="{{URL::route('user.forgot.password.send')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="text-center">
                    <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="OSUMS Logo" style="max-width:180px;width:180px;height:auto;">
                </div>

                <div class="text-center">
                    <h4>Reset Your Password</h4>
                    <p class="subtitle">Enter your registered email address to receive a new password</p>
                </div>

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success')['body'] ?? Session::get('success') }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error')['body'] ?? Session::get('error') }}</div>
                @endif

                <div class="form-group-custom">
                    <input type="email" class="form-control" name="email" placeholder="Enter your registered email" required autocomplete="email" />
                </div>

                <div class="form-group-custom">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fa fa-envelope"></i> Send New Password
                    </button>
                </div>

                <div class="text-center" style="margin-top:22px;">
                    <a href="{{URL::route('home')}}" class="forgot-link"><i class="fa fa-arrow-left"></i> Back to Login</a>
                </div>
            </form>

        </div>
    </div>

</div>

<script src="{{ URL::asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/bootstrap.min.js')}}"></script>
</body>
</html>
