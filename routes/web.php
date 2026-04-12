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

// ===== MODUL 6: CANTEEN ORDERING SYSTEM (Sistem Pemesanan Kantin) =====

// Customer Routes (Public - No Authentication Required)
Route::prefix('customer')->group(function () {
    Route::get('/order', [App\Http\Controllers\CustomerController::class, 'index'])->name('customer.order');
    Route::get('/menu/{idvendor}', [App\Http\Controllers\CustomerController::class, 'getMenuByVendor'])->name('customer.menu');
    Route::post('/store', [App\Http\Controllers\CustomerController::class, 'store'])->name('customer.store');
    Route::get('/payment/{idpesanan}', [App\Http\Controllers\CustomerController::class, 'paymentStatus'])->name('customer.payment');
});

// ===== STUDI KASUS 3: CUSTOMER MANAGEMENT (Camera + Blob/File Storage) =====

// Customer Management Routes (Authenticated)
Route::middleware('auth')->prefix('customer/management')->name('customer.management.')->group(function () {
    Route::get('/', [App\Http\Controllers\CustomerManagementController::class, 'index'])->name('index');
    Route::get('/data', [App\Http\Controllers\CustomerManagementController::class, 'data'])->name('data');
    
    // Tambah Customer 1 (Blob Storage)
    Route::get('/tambah-1', [App\Http\Controllers\CustomerManagementController::class, 'create1'])->name('create1');
    Route::post('/store-1', [App\Http\Controllers\CustomerManagementController::class, 'store1'])->name('store1');
    
    // Tambah Customer 2 (File Storage)
    Route::get('/tambah-2', [App\Http\Controllers\CustomerManagementController::class, 'create2'])->name('create2');
    Route::post('/store-2', [App\Http\Controllers\CustomerManagementController::class, 'store2'])->name('store2');
    
    // Delete Customer
    Route::delete('/{id}', [App\Http\Controllers\CustomerManagementController::class, 'destroy'])->name('destroy');
});

// Vendor Authentication Routes (Public - accessible without login)
Route::prefix('vendor')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\VendorAuthController::class, 'showLoginForm'])->name('vendor.login');
    Route::post('/login', [App\Http\Controllers\Auth\VendorAuthController::class, 'login'])->name('vendor.login.submit');
    Route::get('/register', [App\Http\Controllers\Auth\VendorAuthController::class, 'showRegisterForm'])->name('vendor.register');
    Route::post('/register', [App\Http\Controllers\Auth\VendorAuthController::class, 'register'])->name('vendor.register.submit');
});

// Vendor Dashboard Routes (Authenticated + Verified Vendor)
Route::middleware(['auth', 'verify.vendor'])->prefix('vendor')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\VendorAuthController::class, 'logout'])->name('vendor.logout');
    Route::get('/dashboard', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor.dashboard');
    Route::get('/menus', [App\Http\Controllers\VendorController::class, 'listMenu'])->name('vendor.menus');
    Route::get('/menu/create', [App\Http\Controllers\VendorController::class, 'createMenu'])->name('vendor.menu.create');
    Route::post('/menu/store', [App\Http\Controllers\VendorController::class, 'storeMenu'])->name('vendor.menu.store');
    Route::get('/menu/edit/{idmenu}', [App\Http\Controllers\VendorController::class, 'editMenu'])->name('vendor.menu.edit');
    Route::put('/menu/update/{idmenu}', [App\Http\Controllers\VendorController::class, 'updateMenu'])->name('vendor.menu.update');
    Route::delete('/menu/delete/{idmenu}', [App\Http\Controllers\VendorController::class, 'deleteMenu'])->name('vendor.menu.delete');
    Route::get('/pesanan/{idpesanan}', [App\Http\Controllers\VendorController::class, 'detailPesanan'])->name('vendor.pesanan.detail');
});

// Midtrans Webhook (Public - No Authentication)
Route::post('/midtrans/webhook', [App\Http\Controllers\CustomerController::class, 'midtransCallback'])
    ->name('midtrans.callback');

