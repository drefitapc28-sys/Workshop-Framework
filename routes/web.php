<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// OTP Routes
Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/otp/verify', [OtpController::class, 'verify']);
Route::get('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
  
    Route::resource('kategori', KategoriController::class);
    Route::resource('buku', BukuController::class);
    Route::get('/pdf/sertifikat', [PdfController::class, 'generateSertifikat'])->name('pdf.sertifikat');
    Route::get('/pdf/undangan', [PdfController::class, 'generateUndangan'])->name('pdf.undangan');
});

