@extends('layouts.app')

@section('content')
<style>
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 20px 28px; border-radius: 0 0 20px 20px; margin-bottom: 28px; position: relative; }
    .page-header .btn-tambah { position: absolute; right: 28px; top: 50%; transform: translateY(-50%); }
    .table-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
    .table thead th { background: #1e3a5f; color: white; border: none; font-weight: 600; font-size: .85rem; padding: 14px 16px; }
    .table tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f1f5f9; }
    .table tbody tr:hover { background: #f8fafc; }
    .btn-tambah { background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none; border-radius: 10px; padding: 9px 20px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-tambah:hover { opacity: .9; color: white; }
    .badge-acc { background: #dbeafe; color: #2563eb; padding: 4px 10px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .coord-text { font-family: monospace; font-size: .82rem; color: #475569; }
</style>

<div class="page-header">
    <div>
        <h4 class="mb-1 fw-bold"><i class="mdi mdi-store me-2"></i>Master Toko</h4>
        <small class="opacity-75">Kelola data toko untuk kunjungan sales</small>
    </div>
    <a href="{{ route('toko.create') }}" class="btn-tambah">
        <i class="mdi mdi-plus-circle me-1"></i> Tambah Toko
    </a>
</div>

<div class="container-fluid px-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-body p-0">
            @if($tokos->count())
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Toko</th>
                            <th>Alamat</th>
                            <th>Koordinat</th>
                            <th>Akurasi</th>
                            <th>Barcode</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tokos as $i => $toko)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $toko->nama_toko }}</td>
                            <td><small class="text-muted">{{ $toko->alamat ?? '-' }}</small></td>
                            <td>
                                <span class="coord-text">{{ $toko->latitude }}, {{ $toko->longitude }}</span>
                            </td>
                            <td>
                                <span class="badge-acc">±{{ $toko->accuracy }}m</span>
                            </td>
                            <td><code class="small">{{ $toko->barcode }}</code></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('toko.barcode', $toko->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Cetak Barcode" target="_blank">
                                        <i class="mdi mdi-barcode"></i>
                                    </a>
                                    <a href="{{ route('toko.edit', $toko->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('toko.destroy', $toko->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus toko ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="mdi mdi-store-outline fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-3">Belum ada data toko.</p>
                <a href="{{ route('toko.create') }}" class="btn-tambah mx-auto">
                    <i class="mdi mdi-plus-circle me-1"></i> Tambah Toko Pertama
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4 text-end">
        <a href="{{ route('toko.kunjungan') }}" class="btn btn-primary">
            <i class="mdi mdi-map-marker me-1"></i> Halaman Kunjungan Toko
        </a>
    </div>
</div>
@endsection