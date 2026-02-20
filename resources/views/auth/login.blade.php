<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Purple Admin | Login</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>
<body>
<div class="container-scroller">
  <div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="content-wrapper d-flex align-items-center auth">
      <div class="row w-100">
        <div class="col-lg-4 mx-auto">
          <div class="auth-form-light text-left p-5">
            
            <!-- Logo -->
            <div class="brand-logo">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
            </div>

            <h4 class="font-weight-bold">Hello! let's get started</h4>
            <p class="text-muted">Sign in to continue.</p>

            <!-- Form Login -->
            <form class="pt-4" method="POST" action="{{ route('login') }}">
              @csrf
              
              <div class="form-group mb-3">
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group mb-3">
                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                       name="password" placeholder="Password" required>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Remember Me & Forgot Password -->
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="remember" name="remember" 
                         {{ old('remember') ? 'checked' : '' }}>
                  <label class="form-check-label text-muted" for="remember">
                    Keep me signed in
                  </label>
                </div>
                <a href="{{ route('password.request') }}" class="text-decoration-none text-primary small">Forgot password?</a>
              </div>

              <!-- Sign In Button -->
              <button type="submit" class="btn btn-primary btn-lg w-100 btn-gradient-primary font-weight-bold mb-3 auth-form-btn">
                SIGN IN
              </button>

              @if($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
              @endif
            </form>

            <!-- Divider -->
            <div class="text-center mb-3 text-muted">
              <small>or</small>
            </div>

            <!-- Social Login -->
            <a href="{{url('auth/google')}}" class="btn btn-danger w-100 mb-3">
              <i class="fa fa-google"></i> Sign in with Google </a>

            <!-- Register Link -->
            <div class="text-center">
              <p class="text-muted">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-decoration-none text-primary font-weight-bold">Create</a>
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/todolist.js') }}"></script>
<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
</body>
</html>
