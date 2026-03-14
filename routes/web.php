<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PosController;

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
    
    // Barang routes - custom routes harus sebelum resource
    Route::get('/barang/form-cetak', [BarangController::class, 'formCetak'])->name('barang.form-cetak');
    Route::post('/barang/cetak', [BarangController::class, 'cetak'])->name('barang.cetak');
    Route::get('/barang/tabel-biasa', function () {
        return view('barang.tabel-biasa');
    })->name('barang.tabel-biasa');
    Route::get('/barang/tabel-dt', function () {
        return view('barang.tabel-dt');
    })->name('barang.tabel-dt');
    Route::resource('barang', BarangController::class);

    Route::get('/kota', function () {
        return view('kota.index');
    })->name('kota.index');

    // Wilayah routes
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah/provinsi', [WilayahController::class, 'getProvinsi'])->name('wilayah.provinsi');
    Route::get('/wilayah/kota/{province_id}', [WilayahController::class, 'getKota'])->name('wilayah.kota');
    Route::get('/wilayah/kecamatan/{regency_id}', [WilayahController::class, 'getKecamatan'])->name('wilayah.kecamatan');
    Route::get('/wilayah/kelurahan/{district_id}', [WilayahController::class, 'getKelurahan'])->name('wilayah.kelurahan');

    // POS routes
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/barang/{kode}', [PosController::class, 'getBarang'])->name('pos.getBarang');
    Route::post('/pos/bayar', [PosController::class, 'bayar'])->name('pos.bayar');
});

