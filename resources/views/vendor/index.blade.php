@extends('layouts.app')

@section('content')
<style>
    .vendor-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 24px 28px; border-radius: 0 0 20px 20px; margin-bottom: 28px; }
    .stat-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .table-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
    .table thead th { background: #1e3a5f; color: white; border: none; font-weight: 600; font-size: .85rem; padding: 14px 16px; }
    .table tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f1f5f9; }
    .table tbody tr:hover { background: #f8fafc; }
    .badge-lunas { background: #dcfce7; color: #16a34a; padding: 5px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .badge-qris { background: #dbeafe; color: #2563eb; padding: 5px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .badge-va { background: #fef3c7; color: #d97706; padding: 5px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .btn-detail { background: #2563eb; color: white; border: none; border-radius: 8px; padding: 5px 14px; font-size: .82rem; font-weight: 600; }
    .btn-detail:hover { background: #1d4ed8; color: white; }
    .nav-pills .nav-link { border-radius: 10px; color: #64748b; font-weight: 600; }
    .nav-pills .nav-link.active { background: #2563eb; color: white; }
</style>

{{-- Header --}}
<div class="vendor-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-shop me-2"></i>{{ $vendor->nama_vendor }}</h4>
            <small class="opacity-75">{{ $vendor->deskripsi ?? 'Vendor Kantin Online' }}</small>
        </div>
        <form action="{{ route('vendor.logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-light btn-sm fw-semibold">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10">
                        <i class="bi bi-receipt-cutoff text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pesanan Lunas</div>
                        <div class="fw-bold fs-4">{{ $pesanans->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10">
                        <i class="bi bi-cash-coin text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Pendapatan</div>
                        <div class="fw-bold fs-6">Rp {{ number_format($pesanans->sum('total'), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10">
                        <i class="bi bi-menu-button-wide text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Menu</div>
                        <div class="fw-bold fs-4">{{ $totalMenu }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link active px-4" href="{{ route('vendor.dashboard') }}">
                <i class="bi bi-receipt me-1"></i> Pesanan Lunas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4" href="{{ route('vendor.menus') }}">
                <i class="bi bi-menu-button-wide me-1"></i> Kelola Menu
            </a>
        </li>
    </ul>

    {{-- Tabel Pesanan --}}
    <div class="card table-card">
        <div class="card-body p-0">
            @if($pesanans->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>No Pesanan</th>
                            <th>Nama Pembeli</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanans as $pesanan)
                        <tr>
                            <td><code>#{{ $pesanan->idpesanan }}</code></td>
                            <td class="fw-semibold">{{ $pesanan->nama }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $pesanan->detailPesanans->count() }} item
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($pesanan->metode_bayar == 'virtual_account')
                                    <span class="badge-va"><i class="bi bi-bank me-1"></i>Virtual Account</span>
                                @elseif($pesanan->metode_bayar == 'qris')
                                    <span class="badge-qris"><i class="bi bi-qr-code me-1"></i>QRIS</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-lunas"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                            </td>
                            <td class="text-muted small">{{ $pesanan->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('vendor.pesanan.detail', $pesanan->idpesanan) }}" class="btn-detail">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $pesanans->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted">Belum ada pesanan yang lunas.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection