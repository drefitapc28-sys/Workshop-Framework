@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if($pesanan->status_bayar == 'lunas')
                <div class="alert alert-success">
                    <h4>✅ Pembayaran Dikonfirmasi!</h4>
                    <p>Pesanan Anda sudah dibayar dan akan segera diproses oleh vendor.</p>
                </div>
            @else
                <div class="alert alert-warning">
                    <h4>⏳ Menunggu Pembayaran</h4>
                    <p>Pesanan belum dibayar.</p>
                </div>
            @endif
            
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Detail Pesanan #{{ $pesanan->idpesanan }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td><strong>Nama Pembeli</strong></td>
                            <td>{{ $pesanan->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>Vendor</strong></td>
                            <td>{{ $pesanan->vendor->nama_vendor }}</td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pembayaran</strong></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pesanan->metode_bayar)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran</strong></td>
                            <td>
                                <span class="badge bg-{{ $pesanan->status_bayar == 'lunas' ? 'success' : 'warning' }}">
                                    {{ ucfirst($pesanan->status_bayar) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesanan</strong></td>
                            <td>{{ $pesanan->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Rincian Item</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->detailPesanans as $detail)
                            <tr>
                                <td>{{ $detail->menu->nama_menu }}</td>
                                <td class="text-center">{{ $detail->jumlah }}</td>
                                <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td>{{ $detail->catatan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="text-end">
                        Total: <span class="text-success">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </h4>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="/customer/order" class="btn btn-primary">Pesan Lagi</a>
                <button onclick="window.print()" class="btn btn-secondary">Cetak Nota</button>
            </div>
        </div>
    </div>
</div>
@endsection
