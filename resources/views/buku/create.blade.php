@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-plus-box"></i>
                </span> Tambah Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Tambah Buku</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Ada kesalahan!</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('buku.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Buku <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" placeholder="Contoh: BK-001" value="{{ old('kode') }}" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" placeholder="Contoh: Home Sweet Loan" value="{{ old('judul') }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="pengarang" class="form-label">Pengarang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pengarang') is-invalid @enderror" id="pengarang" name="pengarang" placeholder="Contoh: Almira Bastari" value="{{ old('pengarang') }}" required>
                                @error('pengarang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="idkategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control @error('idkategori') is-invalid @enderror" id="idkategori" name="idkategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->idkategori }}" @selected(old('idkategori') == $kategori->idkategori)>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idkategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save me-2"></i>Simpan
                                </button>
                                <a href="{{ route('buku.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
