<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{AppHelper::getShortName($institute->name)}} | Home </title>

    <!-- Bootstrap -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('assets/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{ URL::asset('assets/css/animate.min.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
		<link href="{{ URL::asset('assets/css/custom.min.css')}}" rel="stylesheet">
  </head>

  <body class="login">
    <div class="login_wrapper">
      <div class="animate form login_form">
        <section class="login_content">
          <form name="login" method="post" action="{{URL::route('user.login')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!-- Logo -->
            <div class="text-center mb-4">
              <img src="{{ URL::asset('assets/images/logo.jpg') }}" alt="Logo" style="max-width:150px;">
            </div>
            <h4 class="text-center">Welcome to University Management System (UMS)</h4>
            <p class="text-center">Sign‑in to your account to Continue</p>
            <div class="form-group">
              <input type="text" class="form-control" name="login" placeholder="Enter your Username" required />
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="password" placeholder="Enter your password" required />
            </div>
            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input type="checkbox" onclick="this.closest('form').password.type = this.checked ? 'text' : 'password'"> Show Password
                </label>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block btn-lg">Sign in</button>
            </div>
            <div class="text-center">
              <a href="https://localhost" target="_blank">Forgot Password Click Here ?</a>
            </div>
            <div class="clearfix"></div>
            <div class="separator mt-4">
              @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
              @endif
              @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
              @endif
              @if (Session::has('warning'))
                <div class="alert alert-warning">{{ Session::get('warning') }}</div>
              @endif
              <div class="text-center mt-3">
                <h2 style="font-size:16px;"><i class="fa fa-bank"></i> {{$institute->name}}</h2>
                <p>©{{date('Y')}} All Rights Reserved.</p>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </body>
</html>
