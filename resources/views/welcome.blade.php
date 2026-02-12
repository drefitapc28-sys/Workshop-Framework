<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Koleksi Buku - Purple Admin</title>

<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
<div class="container-scroller">
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card" style="width: 400px;">
            <div class="card-body">
                <div class="text-center">
                    <h3 class="card-title mb-4">
                        <i class="mdi mdi-library text-primary" style="font-size: 48px;"></i>
                    </h3>
                    <h3 class="card-title mb-2">Selamat Datang</h3>
                    <p class="text-muted mb-4">Sistem Manajemen Koleksi Buku</p>
                </div>
                
                @auth
                    <a href="{{ route('home') }}" class="btn btn-primary btn-block w-100" style="width: 100%;">
                        <i class="mdi mdi-arrow-right me-2"></i>Ke Dashboard
                    </a>
                @else
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email Address" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="mdi mdi-login me-2"></i>Login
                        </button>
                    </form>

                    @if (Route::has('register'))
                        <p class="text-center text-muted mb-0">
                            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
                        </p>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>

</body>
</html>
