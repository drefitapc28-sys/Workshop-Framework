@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="col-lg-8 col-md-10 mx-auto">
    <div class="auth-form-light text-left" style="padding: 60px 40px;">
        <div class="brand-logo text-center mb-4">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" style="width: 150px;">
        </div>
        <h4 class="mb-3" style="font-size: 32px; font-weight: 600;">Verifikasi OTP</h4>
        <h6 class="font-weight-light mb-5" style="font-size: 18px; color: #666;">Masukkan kode 6 digit yang dikirim ke email Anda.</h6>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 15px;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 15px;">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form class="pt-4" method="POST" action="{{ route('otp.verify') }}">
            @csrf
            
            <div class="form-group mb-5">
                <label for="otp" style="font-size: 18px; font-weight: 600; margin-bottom: 15px; display: block;">Kode OTP</label>
                <input type="text" 
                       class="form-control form-control-lg text-center @error('otp') is-invalid @enderror" 
                       id="otp" 
                       name="otp" 
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       style="font-size: 40px; letter-spacing: 15px; font-weight: bold; padding: 25px 20px; height: 80px; border-radius: 8px;"
                       required 
                       autofocus>
                @error('otp')
                    <span class="invalid-feedback" role="alert" style="font-size: 14px;">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mt-5">
                <center> <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" style="padding: 18px; font-size: 18px; font-weight: 600;">
                    VERIFIKASI
                </button> </center>
            </div>
            
            <div class="text-center mt-5">
                <p class="text-muted" style="font-size: 16px; margin-bottom: 12px;">Tidak menerima kode?</p>
                <a href="{{ route('otp.resend') }}" class="btn btn-link" style="font-size: 16px; text-decoration: none;">Kirim Ulang OTP</a>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="auth-link text-black" style="font-size: 16px; text-decoration: none;">Kembali ke Login</a>
            </div>
        </form>
    </div>
</div>
@endsection