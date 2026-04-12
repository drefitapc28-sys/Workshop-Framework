@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-4">
                <i class="mdi mdi-account-multiple"></i> Customer Management
            </h1>
        </div>
    </div>

    <!-- Statistik Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Customer</h6>
                    <h2 class="text-primary">{{ $totalCustomers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Foto BLOB</h6>
                    <h2 class="text-info">{{ $totalBlob }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Foto FILE</h6>
                    <h2 class="text-success">{{ $totalFile }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="mdi mdi-table" style="font-size: 2rem; color: #4e73df;"></i>
                    </h5>
                    <h6 class="card-title">Data Customer</h6>
                    <p class="card-text text-muted small">Lihat tabel semua customer</p>
                    <a href="{{ route('customer.management.data') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-arrow-right"></i> Buka
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="mdi mdi-camera" style="font-size: 2rem; color: #17a2b8;"></i>
                    </h5>
                    <h6 class="card-title">Tambah Customer 1</h6>
                    <p class="card-text text-muted small">Ambil foto + Simpan BLOB ke Database</p>
                    <a href="{{ route('customer.management.create1') }}" class="btn btn-info btn-sm">
                        <i class="mdi mdi-arrow-right"></i> Tambah
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="mdi mdi-camera" style="font-size: 2rem; color: #28a745;"></i>
                    </h5>
                    <h6 class="card-title">Tambah Customer 2</h6>
                    <p class="card-text text-muted small">Ambil foto + Simpan FILE ke Storage</p>
                    <a href="{{ route('customer.management.create2') }}" class="btn btn-success btn-sm">
                        <i class="mdi mdi-arrow-right"></i> Tambah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                <h6 class="alert-heading">
                    <i class="mdi mdi-information-outline"></i> Perbedaan Penyimpanan
                </h6>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Customer 1 (BLOB):</strong>
                        <ul class="small mb-0 mt-2">
                            <li>Foto disimpan langsung ke database (BLOB)</li>
                            <li>Ukuran database membesar</li>
                            <li>Lebih aman (backup otomatis dengan DB)</li>
                            <li>Query lebih lambat untuk data besar</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer 2 (FILE):</strong>
                        <ul class="small mb-0 mt-2">
                            <li>Foto disimpan ke file storage publik</li>
                            <li>Database hanya menyimpan path/URL</li>
                            <li>Query lebih cepat</li>
                            <li>Lebih scalable untuk production</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
</style>
@endsection
