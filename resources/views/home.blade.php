<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{AppHelper::getShortName($institute->name)}} | Home </title>

    <!-- Bootstrap & Essentials -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/animate.min.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ URL::asset('assets/css/custom.min.css')}}" rel="stylesheet">

    <style>
      /* 1. Global Layout Initialization */
      html, body {
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        background-color: #F7F7F7 !important; /* System Panel Matte Tone */
        -webkit-font-smoothing: antialiased;
      }

      /* 2. Seamless Asymmetric Split Grid */
      .login-split-container {
        display: flex;
        height: 100vh;
        width: 100vw;
      }
      
      /* 3. Left Panel: Deep Charcoal Brand Media Frame */
      .login-slide-side {
        flex: 0 0 62%;
        width: 62%;
        height: 100vh;
        padding: 0 !important; 
        background-color: #2A3F54; /* Dominant Sidebar Base */
        position: relative;
      }

      .slide-inner-frame {
        width: 100%;
        height: 100%; 
        border-radius: 0px !important; 
        overflow: hidden;
        position: relative;
      }

      /* Zero-Flicker Continuous Blend Engine */
      .carousel-wrapper-box,
      .carousel-wrapper-box .carousel,
      .carousel-wrapper-box .carousel-inner {
        height: 100% !important;
        width: 100% !important;
        position: relative;
      }

      .carousel-wrapper-box .carousel-item {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        display: block !important;
        opacity: 0;
        z-index: 1;
        transition: opacity 1.3s ease-in-out !important; 
      }

      .carousel-wrapper-box .carousel-item.active {
        opacity: 1 !important;
        z-index: 2;
      }

      .carousel-wrapper-box .carousel-item img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
      }

      /* Vignette Overlay Anchoring System Typography */
      .hero-gradient-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40%;
        background: linear-gradient(to top, rgba(42, 63, 84, 0.98) 0%, rgba(42, 63, 84, 0.6) 60%, rgba(42, 63, 84, 0) 100%);
        display: flex;
        align-items: flex-end;
        padding: 50px;
        z-index: 10;
        pointer-events: none;
      }

      .hero-overlay-text h3 {
        color: #ffffff;
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
      }

      .hero-overlay-text p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.25rem;
        margin: 0;
        line-height: 1.5;
        max-width: 90%;
      }

      /* 4. Right Panel: Workspace Form Wrapper Base */
      .login-form-side {
        flex: 0 0 38%;
        width: 38%;
        height: 100vh;
        display: flex;
        align-items: center; 
        justify-content: center;
        padding: 5%;
        background-color: #F7F7F7 !important; 
        border-left: 1px solid #E6E9ED;
      }

      .isolated-login-box {
        width: 100%;
        max-width: 420px;
        background: transparent !important;
      }

      .isolated-login-box h4 {
        color: #2A3F54; 
        font-weight: 700;
        font-size: 1.9rem !important; 
        line-height: 1.35;
        margin-top: 25px;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
      }

      .isolated-login-box p.subtitle {
        color: #73879C;
        font-size: 1.25rem !important; 
        margin-bottom: 35px;
        font-weight: 500;
      }

      .form-group-custom {
        position: relative;
        margin-bottom: 22px;
      }

      /* ======================================================================
         STAND-UP TYPOGRAPHY CONFIGURATION RESTORED WITH ORIGINAL BLUE COLOR
         ====================================================================== */
      .form-control {
        height: 50px !important; 
        padding: 2px 14px !important; 
        border: 1.5px solid #CBD5E1 !important;
        border-radius: 4px !important; /* Elegant modern micro-radius */
        background-color: #ffffff !important;
        box-shadow: none !important;
        font-size: 1.35rem !important; /* Tall profile text font sizing */
        color: #2A3F54 !important;
        line-height: 44px !important; 
        font-weight: 600 !important;
        transition: all 0.2s ease-in-out !important;
      }

      /* Focus Ring Switches Back to Original Pale Blue Accent */
      .form-control:focus {
        border-color: #337ab7 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(51, 122, 183, 0.15) !important; 
      }

      .form-control::placeholder {
        color: #94A3B8 !important;
        font-size: 1.35rem !important;
        font-weight: 500;
      }

      /* Utilities Styles Configuration */
      .checkbox label {
        font-size: 1.25rem !important; 
        color: #2A3F54;
        font-weight: 600;
        user-select: none;
      }

      .checkbox input[type="checkbox"] {
        border-radius: 3px !important;
        accent-color: #337ab7; 
      }

      /* Submit Engine Configured with Core Blue Accent */
      .btn-primary {
        background-color: #337ab7 !important;
        border-color: #337ab7 !important;
        height: 52px; 
        font-size: 1.4rem !important; 
        font-weight: 700;
        border-radius: 4px !important; 
        box-shadow: none !important; 
        transition: all 0.2s ease;
        margin-top: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #ffffff !important;
      }

      .btn-primary:hover {
        background-color: #286090 !important;
        border-color: #204d74 !important;
        transform: translateY(-1px);
      }
      
      .btn-primary:active {
        transform: translateY(0);
      }

      .isolated-login-box a.forgot-link {
        font-size: 1.25rem !important; 
        color: #337ab7;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s ease;
      }

      .isolated-login-box a.forgot-link:hover {
        color: #286090;
        text-decoration: underline;
      }

      .custom-separator {
        border-top: 1px solid #D3D6DA;
        margin-top: 35px;
        padding-top: 25px;
      }

      .custom-separator h2 {
        font-size: 1.45rem !important; 
        font-weight: 700;
        color: #2A3F54;
        margin-bottom: 6px;
      }

      .custom-separator p {
        font-size: 1.15rem !important; 
        color: #73879C;
        margin: 0;
      }

      /* Dynamic Alerts styled with matching context palettes */
      .alert-danger {
        background-color: #E74C3C !important;
        border-color: #E74C3C !important;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 4px;
      }

      .alert-success {
        background-color: #337ab7 !important;
        border-color: #337ab7 !important;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 4px;
      }

      /* Responsive Core Adaptations */
      @media (max-width: 991px) {
        html, body { overflow: auto !important; height: auto !important; max-height: none !important; }
        .login-split-container { flex-direction: column; height: auto; }
        .login-slide-side { width: 100%; flex: 0 0 100%; height: 40vh; }
        .login-form-side { width: 100%; flex: 0 0 100%; height: auto; padding: 40px 20px; border-left: none; }
        .hero-gradient-overlay { height: 65%; padding: 25px; }
      }
    </style>
  </head>

  <body class="login">
    <div class="login-split-container">
      
      <!-- LEFT SIDE: CHARCOAL SLATE MULTI-SLIDER (ZERO FLICKER CROSSFADE) -->
      <div class="login-slide-side">
        <div class="slide-inner-frame">
          <div class="carousel-wrapper-box">
            
            <div id="loginBannerCarousel" class="carousel slide" data-ride="carousel" data-interval="5000" data-pause="false">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="{{ URL::asset('assets/images/img1.jpg') }}" alt="Campus Infrastructure">
                </div>
                <div class="carousel-item">
                  <img src="{{ URL::asset('assets/images/img2.jpg') }}" alt="Academic Excellence">
                </div>
                <div class="carousel-item">
                  <img src="{{ URL::asset('assets/images/img3.jpg') }}" alt="Student Life">
                </div>
              </div>
            </div>

            <!-- Balanced Text Vignette Overlay -->
            <div class="hero-gradient-overlay">
              <div class="hero-overlay-text">
                <h3>University Management Portal</h3>
                <p>Authorized personnel access node. Connect to live operations dashboard.</p>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- RIGHT SIDE: APPLICATION LOGIC DISCIPLINED APP CONTROL INTERFACE -->
      <div class="login-form-side">
        <div class="isolated-login-box">
          
          <form name="login" method="post" action="{{URL::route('user.login')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <!-- Standard Logo Aspect Layout -->
            <div class="text-center">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="Institution Logo" style="max-width: 180px; width: 180px; height: auto;">
            </div>
            
            <!-- Structural Slate Titles -->
            <div class="text-center">
              <h4>Welcome to University Management System (UMS)</h4>
              <p class="subtitle">Sign-in to your account to continue</p>
            </div>
            
            <!-- Custom Dynamic Tall-Font Input Fields -->
            <div class="form-group-custom">
              <input type="text" class="form-control" name="login" placeholder="Enter your Username" required autocomplete="username" />
            </div>
            
            <div class="form-group-custom">
              <input type="password" class="form-control" name="password" placeholder="Enter your Password" required autocomplete="current-password" />
            </div>
            
            <div class="form-group-custom d-flex justify-content-between align-items-center" style="margin-bottom: 25px;">
              <div class="checkbox style-none" style="margin: 0;">
                <label style="cursor: pointer;">
                  <input type="checkbox" style="margin-top: 3px;" onclick="this.closest('form').password.type = this.checked ? 'text' : 'password'"> Show Password
                </label>
              </div>
            </div>
            
            <div class="form-group-custom">
              <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In to Dashboard</button>
            </div>
            
            <div class="text-center" style="margin-top: 22px;">
              <a href="{{URL::route('user.forgot.password')}}" class="forgot-link">Forgot Password System Access?</a>
            </div>
            
            <!-- Runtime Session Context Feedback Alert Containers -->
            <div class="custom-separator text-center">
              @if (Session::has('success'))
                <div class="alert alert-success py-2">{{ Session::get('success') }}</div>
              @endif
              @if (Session::has('error'))
                <div class="alert alert-danger py-2">{{ Session::get('error') }}</div>
              @endif
              @if (Session::has('warning'))
                <div class="alert alert-warning py-2" style="background-color: #E74C3C; border-color: #E74C3C; color: #fff;">{{ Session::get('warning') }}</div>
              @endif
              
              <!-- Core Dynamic Bottom Institutional Signature Flag -->
              <div class="mt-2">
                <h2><i class="fa fa-bank"></i> {{$institute->name}}</h2>
                <p>© {{date('Y')}} All Rights Reserved.</p>
              </div>
            </div>
            
          </form>

        </div>
      </div>

    </div>

    <!-- REQUIRED SCRIPT FOOTER DEPENDENCIES FOR SLIDER ENGINE -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  </body>
</html>