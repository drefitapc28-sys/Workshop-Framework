@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .detail-wrap {
         max-width: 760px; 
         margin: 30px auto; 
        }
    .page-header { 
        background: linear-gradient(135deg, #1e3a5f, #2563eb); 
        color: white; 
        border-radius: 14px; 
        padding: 20px 24px; 
        margin-bottom: 20px; 
    }
    .info-card { 
        border: none; 
        border-radius: 14px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        margin-bottom: 16px; }
    .info-card .card-header { 
        background: #f8fafc; 
        border-bottom: 1px solid #e2e8f0; 
        border-radius: 14px 14px 0 0 !important; 
        font-weight: 700; 
        color: #1e293b; 
        padding: 14px 20px; }
    .info-card .card-body { padding: 16px 20px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: .88rem; }
    .info-value { font-weight: 600; font-size: .88rem; color: #1e293b; }
    .badge-lunas { background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .badge-pending { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .badge-va { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .badge-qris { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
    .item-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .item-row:last-child { border-bottom: none; }
    .item-nama { font-weight: 600; font-size: .9rem; color: #1e293b; }
    .item-qty { font-size: .82rem; color: #64748b; }
    .item-catatan { font-size: .78rem; color: #94a3b8; }
    .item-subtotal { font-weight: 700; color: #1e293b; }
    .total-box { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
    .btn-back { border: 2px solid #e2e8f0; background: white; color: #475569; border-radius: 10px; padding: 9px 20px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-back:hover { background: #f8fafc; color: #1e293b; }
    .btn-print { background: #2563eb; color: white; border: none; border-radius: 10px; padding: 9px 20px; font-weight: 600; }
</style>

<div class="detail-wrap px-3">

    {{-- Header --}}
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1 fw-bold"><i class="bi bi-receipt me-2"></i>Detail Pesanan</h5>
                <small class="opacity-75">Order #{{ $pesanan->idpesanan }}</small>
            </div>
            <span class="badge-lunas" style="background:rgba(255,255,255,.2);color:white;padding:6px 14px;border-radius:20px;font-weight:600;">
                <i class="bi bi-check-circle me-1"></i>Lunas
            </span>
        </div>
    </div>

    {{-- Info Pesanan --}}
    <div class="card info-card">
        <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pesanan</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Nama Pembeli</span>
                <span class="info-value">{{ $pesanan->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Midtrans Order ID</span>
                <code style="font-size:.82rem;color:#2563eb;">{{ $pesanan->midtrans_order_id ?? '-' }}</code>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Pembayaran</span>
                <span>
                    @if($pesanan->metode_bayar == 'virtual_account')
                        <span class="badge-va"><i class="bi bi-bank me-1"></i>Virtual Account</span>
                    @elseif($pesanan->metode_bayar == 'qris')
                        <span class="badge-qris"><i class="bi bi-qr-code me-1"></i>QRIS</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status Pembayaran</span>
                <span>
                    @if($pesanan->status_bayar == 'lunas')
                        <span class="badge-lunas"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                    @else
                        <span class="badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pesanan</span>
                <span class="info-value">{{ $pesanan->created_at->format('d M Y, H:i:s') }}</span>
            </div>
        </div>
    </div>

    {{-- Rincian Item --}}
    <div class="card info-card">
        <div class="card-header"><i class="bi bi-list-ul me-2 text-primary"></i>Rincian Item Pesanan</div>
        <div class="card-body">
            @foreach($pesanan->detailPesanans as $key => $detail)
            <div class="item-row">
                <div>
                    <div class="item-nama">{{ $detail->menu->nama_menu ?? '-' }}</div>
                    <div class="item-qty">{{ $detail->jumlah }} x Rp {{ number_format($detail->harga, 0, ',', '.') }}</div>
                    @if($detail->catatan)
                        <div class="item-catatan">📝 {{ $detail->catatan }}</div>
                    @endif
                </div>
                <div class="item-subtotal">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Total --}}
    <div class="total-box mb-4">
        <span class="fw-semibold">Total Pesanan</span>
        <span class="fw-bold fs-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-2 mb-5">
        <a href="{{ route('vendor.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
    </div>

</div>
@endsection