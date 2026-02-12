@extends('layouts.app')

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <!-- Greeting Section -->
    <div class="row mb-5">
      <div class="col-12">
        <div class="greeting-section">
          <h1 class="greeting-title mb-2">Selamat Datang, {{ Auth::user()->name }}!</h1>
          <p class="greeting-subtitle text-muted">Dashboard Sistem Koleksi Buku</p>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
      <!-- Total Buku Card -->
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-0 shadow text-white" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 10px; min-height: 160px;">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h6 class="card-title text-light text-uppercase" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px; opacity: 0.9;">Total Buku</h6>
              <h2 class="mb-0" style="font-size: 40px; font-weight: 700;">{{ $totalBuku }}</h2>
            </div>
            <small style="opacity: 0.85;">Koleksi perpustakaan</small>
          </div>
          <div style="position: absolute; top: 15px; right: 20px; font-size: 32px; opacity: 0.2;">
            <i class="mdi mdi-book-open-page-variant"></i>
          </div>
        </div>
      </div>

      <!-- Total Kategori Card -->
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-0 shadow text-white" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 10px; min-height: 160px; position: relative;">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h6 class="card-title text-light text-uppercase" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px; opacity: 0.9;">Total Kategori</h6>
              <h2 class="mb-0" style="font-size: 40px; font-weight: 700;">{{ $totalKategori }}</h2>
            </div>
            <small style="opacity: 0.85;">Jenis kategori buku</small>
          </div>
          <div style="position: absolute; top: 15px; right: 20px; font-size: 32px; opacity: 0.2;">
            <i class="mdi mdi-tag-multiple"></i>
          </div>
        </div>
      </div>

      <!-- Total Pengguna Card -->
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-0 shadow text-white" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 10px; min-height: 160px; position: relative;">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h6 class="card-title text-light text-uppercase" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px; opacity: 0.9;">Total Pengguna</h6>
              <h2 class="mb-0" style="font-size: 40px; font-weight: 700;">{{ $totalPengguna }}</h2>
            </div>
            <small style="opacity: 0.85;">Pengguna terdaftar</small>
          </div>
          <div style="position: absolute; top: 15px; right: 20px; font-size: 32px; opacity: 0.2;">
            <i class="mdi mdi-account-multiple"></i>
          </div>
        </div>
      </div>

      <!-- Hari Ini Card -->
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-0 shadow text-white" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 10px; min-height: 160px; position: relative;">
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h6 class="card-title text-light text-uppercase" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px; opacity: 0.9;">Hari Ini</h6>
              <h2 class="mb-0" style="font-size: 32px; font-weight: 700;">{{ date('d M Y') }}</h2>
            </div>
            <small style="opacity: 0.85;">Tanggal hari ini</small>
          </div>
          <div style="position: absolute; top: 15px; right: 20px; font-size: 32px; opacity: 0.2;">
            <i class="mdi mdi-calendar-today"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row">
      <div class="col-12">
        <h5 class="mb-4 font-weight-600">Aksi Cepat</h5>
      </div>
    </div>

    <div class="row">
      <!-- Tambah Buku -->
      <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('buku.create') }}" class="card border-0 shadow-sm h-100 text-decoration-none" style="border-left: 4px solid #3b82f6; border-radius: 8px; transition: all 0.3s ease;">
          <div class="card-body text-center py-5">
            <div style="font-size: 48px; color: #3b82f6; margin-bottom: 15px;">
              <i class="mdi mdi-plus-circle"></i>
            </div>
            <h6 style="color: #1e40af; font-weight: 600; margin: 0;">Tambah Buku</h6>
          </div>
        </a>
      </div>

      <!-- Tambah Kategori -->
      <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('kategori.create') }}" class="card border-0 shadow-sm h-100 text-decoration-none" style="border-left: 4px solid #06b6d4; border-radius: 8px; transition: all 0.3s ease;">
          <div class="card-body text-center py-5">
            <div style="font-size: 48px; color: #06b6d4; margin-bottom: 15px;">
              <i class="mdi mdi-plus-circle"></i>
            </div>
            <h6 style="color: #0891b2; font-weight: 600; margin: 0;">Tambah Kategori</h6>
          </div>
        </a>
      </div>

      <!-- Lihat Buku -->
      <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('buku.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none" style="border-left: 4px solid #22c55e; border-radius: 8px; transition: all 0.3s ease;">
          <div class="card-body text-center py-5">
            <div style="font-size: 48px; color: #22c55e; margin-bottom: 15px;">
              <i class="mdi mdi-book-open"></i>
            </div>
            <h6 style="color: #16a34a; font-weight: 600; margin: 0;">Lihat Buku</h6>
          </div>
        </a>
      </div>

      <!-- Lihat Kategori -->
      <div class="col-md-6 col-lg-3 mb-3">
        <a href="{{ route('kategori.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none" style="border-left: 4px solid #f97316; border-radius: 8px; transition: all 0.3s ease;">
          <div class="card-body text-center py-5">
            <div style="font-size: 48px; color: #f97316; margin-bottom: 15px;">
              <i class="mdi mdi-tag-multiple"></i>
            </div>
            <h6 style="color: #ea580c; font-weight: 600; margin: 0;">Lihat Kategori</h6>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection