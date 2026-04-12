@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .status-wrap { max-width: 580px; margin: 30px auto; }
    .status-header { border-radius: 16px 16px 0 0; padding: 32px 28px; text-align: center; }
    .status-header.lunas   { background: linear-gradient(135deg, #16a34a, #15803d); }
    .status-header.pending { background: linear-gradient(135deg, #d97706, #b45309); }
    .status-icon  { font-size: 3.5rem; color: white; margin-bottom: 10px; }
    .status-title { color: white; font-weight: 700; font-size: 1.4rem; margin-bottom: 4px; }
    .status-sub   { color: rgba(255,255,255,.8); font-size: .9rem; }
    .status-body  { background: white; border-radius: 0 0 16px 16px; box-shadow: 0 4px 24px rgba(0,0,0,.1); padding: 28px; }
    .section-title { font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 12px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: .85rem; }
    .info-value { font-weight: 600; font-size: .88rem; color: #1e293b; }
    .item-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .item-row:last-child { border-bottom: none; }
    .item-nama    { font-weight: 600; font-size: .88rem; color: #1e293b; }
    .item-qty     { font-size: .82rem; color: #64748b; }
    .item-catatan { font-size: .78rem; color: #94a3b8; }
    .item-subtotal { font-weight: 700; font-size: .88rem; color: #1e293b; }
    .total-box { background: #eff6ff; border-radius: 10px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; }
    .total-label { font-weight: 600; color: #1e293b; }
    .total-value { font-weight: 800; font-size: 1.2rem; color: #2563eb; }
    .badge-lunas   { background: #dcfce7; color: #16a34a; font-size: .82rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-pending { background: #fef3c7; color: #d97706; font-size: .82rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-va      { background: #fef3c7; color: #d97706; font-size: .82rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-qris    { background: #dbeafe; color: #2563eb; font-size: .82rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .btn-pesan { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 700; width: 100%; text-decoration: none; display: block; text-align: center; }
    .btn-cetak { border: 2px solid #e2e8f0; background: white; color: #475569; border-radius: 10px; padding: 10px; font-weight: 600; width: 100%; }
    .qrcode-box { background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
    .qrcode-box img { max-width: 200px; height: auto; }
    .qrcode-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-bottom: 8px; font-weight: 700; }
    .qrcode-desc { font-size: .82rem; color: #64748b; margin-top: 12px; }
</style>

<div class="status-wrap px-3">

    {{-- Header Status --}}
    <div class="status-header {{ $pesanan->status_bayar == 'lunas' ? 'lunas' : 'pending' }}">
        <div class="status-icon">
            @if($pesanan->status_bayar == 'lunas')
                <i class="bi bi-check-circle-fill"></i>
            @else
                <i class="bi bi-clock-history"></i>
            @endif
        </div>
        <div class="status-title">
            {{ $pesanan->status_bayar == 'lunas' ? 'Pembayaran Berhasil!' : 'Menunggu Pembayaran' }}
        </div>
        <div class="status-sub">
            {{ $pesanan->status_bayar == 'lunas'
                ? 'Pesanan Anda telah lunas dan sedang diproses'
                : 'Selesaikan pembayaran Anda secepatnya' }}
        </div>
    </div>

    <div class="status-body">

        {{-- Info Pesanan --}}
        <div class="section-title">Detail Pesanan</div>
        <div class="mb-4">
            <div class="info-row">
                <span class="info-label">Order ID</span>
                <code style="font-size:.82rem;color:#2563eb;">{{ $pesanan->midtrans_order_id }}</code>
            </div>
            <div class="info-row">
                <span class="info-label">Nama Customer</span>
                <span class="info-value">{{ $pesanan->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kantin</span>
                <span class="info-value">{{ $pesanan->vendor->nama_vendor ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu Pesanan</span>
                <span class="info-value">{{ $pesanan->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Bayar</span>
                <span>
                    @if($pesanan->metode_bayar == 'virtual_account')
                        <span class="badge-va"><i class="bi bi-bank me-1"></i>Virtual Account</span>
                    @elseif($pesanan->metode_bayar == 'qris')
                        <span class="badge-qris"><i class="bi bi-qr-code me-1"></i>QRIS</span>
                    @else
                        <span class="badge-pending">-</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span>
                    @if($pesanan->status_bayar == 'lunas')
                        <span class="badge-lunas"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                    @else
                        <span class="badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    @endif
                </span>
            </div>
        </div>

        {{-- Item --}}
        <div class="section-title">Item yang Dipesan</div>
        <div class="mb-4">
            @foreach($pesanan->detailPesanans as $detail)
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

        {{-- QR Code (tampil hanya jika pembayaran lunas) --}}
        @if($pesanan->status_bayar == 'lunas' && $qrCode)
        <div class="qrcode-box">
            <div class="qrcode-label">📱 QR Code Pesanan</div>
            <img src="{{ $qrCode }}" alt="QR Code {{ $pesanan->idpesanan }}" title="ID Pesanan: {{ $pesanan->idpesanan }}">
            <div class="qrcode-desc">
                Scan untuk verifikasi order ID: <strong>{{ $pesanan->idpesanan }}</strong>
            </div>
        </div>
        @endif

        {{-- Total --}}
        <div class="total-box mb-4">
            <span class="total-label">Total Pembayaran</span>
            <span class="total-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
        </div>

        {{-- Actions --}}
        <div class="d-grid gap-2">
            <a href="/customer/order" class="btn-pesan">
                <i class="bi bi-bag-plus me-2"></i>Pesan Lagi
            </a>
            <button onclick="window.print()" class="btn-cetak">
                <i class="bi bi-printer me-2"></i>Cetak Nota
            </button>
        </div>

        <p class="text-center text-muted mt-3" style="font-size:.78rem;">
            Simpan screenshot ini sebagai bukti pesanan Anda
        </p>
    </div>
</div>
@endsection