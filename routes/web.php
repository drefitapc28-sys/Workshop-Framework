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
use App\Http\Controllers\TokoController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\NfcAbsensiController;

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
    // Modul 8 - Scan Barcode
    // PRAKTIKUM 1: Barcode Reader
    Route::get('/barang/scan', [BarangController::class, 'scan'])->name('barang.scan');
    Route::get('/barang/find-barcode', [BarangController::class, 'findByBarcode'])->name('barang.findBarcode');
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
    // PRAKTIKUM 2: QR Code Reader untuk Vendor
    Route::get('/scan-qr', [App\Http\Controllers\VendorController::class, 'scanQr'])->name('vendor.scan-qr');
    Route::get('/find-pesanan-qr', [App\Http\Controllers\VendorController::class, 'findPesananByQr'])->name('vendor.find-pesanan-qr');
 
});

// ===== MODUL 9: GEOLOCATION - Toko Management & Kunjungan =====
Route::middleware('auth')->group(function () {
    // Cetak barcode toko (PDF)
    Route::get('/toko/{id}/barcode', [App\Http\Controllers\TokoController::class, 'cetakBarcode'])->name('toko.barcode');
    // Halaman kunjungan toko
    Route::get('/kunjungan', [App\Http\Controllers\TokoController::class, 'kunjungan'])->name('toko.kunjungan');
    // AJAX: cari toko berdasarkan barcode hasil scan
    Route::get('/toko/find-barcode', [App\Http\Controllers\TokoController::class, 'findByBarcode'])->name('toko.findBarcode');
    // AJAX: simpan kunjungan
    Route::post('/kunjungan/simpan', [App\Http\Controllers\TokoController::class, 'simpanKunjungan'])->name('toko.simpanKunjungan');
    // Toko CRUD
    Route::resource('toko', App\Http\Controllers\TokoController::class);
});

// ─── MODUL 10: SSE - Sistem Antrian Real-Time ───────────────────────
// Guest (Public - tanpa auth)
Route::get('/guest', [AntrianController::class, 'guestForm'])->name('guest.form');
Route::post('/guest', [AntrianController::class, 'guestStore'])->name('guest.store');
 
// Papan antrian publik (tanpa auth)
Route::get('/papan', [AntrianController::class, 'papanAntrian'])->name('papan.index');
 

Route::middleware([])->get('/sse/antrian', [AntrianController::class, 'stream'])->name('sse.antrian');
 
// Admin antrian (dengan auth)
Route::middleware('auth')->prefix('antrian')->name('antrian.')->group(function () {
    Route::get('/admin', [AntrianController::class, 'adminDashboard'])->name('admin');
    Route::post('/panggil', [AntrianController::class, 'panggilBerikutnya'])->name('panggil');
    Route::post('/terlambat', [AntrianController::class, 'tandaiTerlambat'])->name('terlambat');
    Route::post('/panggil-terlambat', [AntrianController::class, 'panggilTerlambat'])->name('panggilTerlambat');
    Route::post('/selesai', [AntrianController::class, 'tandaiSelesai'])->name('selesai');
    Route::post('/reset', [AntrianController::class, 'resetAntrian'])->name('reset');
});

// ===== MODUL 11: Web NFC API — Sistem Absensi =====

// Scanner NFC public
Route::get('/nfc/scanner', [NfcAbsensiController::class, 'scanner'])->name('nfc.scanner');
Route::post('/nfc/scan', [NfcAbsensiController::class, 'prosesScan'])->name('nfc.scan');

// dengan auth
Route::middleware('auth')->prefix('nfc')->name('nfc.')->group(function () {
    Route::get('/kartu', [NfcAbsensiController::class, 'daftarKartu'])->name('kartu');
    Route::post('/kartu', [NfcAbsensiController::class, 'simpanKartu'])->name('kartu.simpan');
    Route::delete('/kartu/{id}', [NfcAbsensiController::class, 'hapusKartu'])->name('kartu.hapus');
    Route::patch('/kartu/{id}/toggle', [NfcAbsensiController::class, 'toggleKartu'])->name('kartu.toggle');
    Route::post('/mahasiswa', [NfcAbsensiController::class, 'simpanMahasiswa'])->name('mahasiswa.simpan');
    Route::get('/riwayat', [NfcAbsensiController::class, 'riwayat'])->name('riwayat');
    Route::delete('/absensi/{id}', [NfcAbsensiController::class, 'hapusAbsensi'])->name('absensi.hapus');
});

// Midtrans Webhook (Public - No Authentication)
Route::post('/midtrans/webhook', [App\Http\Controllers\CustomerController::class, 'midtransCallback'])
    ->name('midtrans.callback');



