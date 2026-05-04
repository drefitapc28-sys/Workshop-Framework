@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 20px 28px; border-radius: 0 0 20px 20px; margin-bottom: 28px; }
    .scanner-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }
    #reader { width: 100%; border-radius: 12px; overflow: hidden; }
    .result-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: none; }
    .result-card.show { display: block; animation: fadeIn .3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .result-header { padding: 20px 24px; }
    .result-header.lunas { background: linear-gradient(135deg, #16a34a, #15803d); color: white; }
    .result-header.pending { background: linear-gradient(135deg, #d97706, #b45309); color: white; }
    .result-body { padding: 24px; }
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; align-items: center; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: .88rem; }
    .info-value { font-weight: 700; color: #1e293b; font-size: .9rem; }
    .badge-lunas { background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: .82rem; font-weight: 700; }
    .badge-pending { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: .82rem; font-weight: 700; }
    .item-row { background: #f8fafc; border-radius: 10px; padding: 12px 16px; margin-bottom: 8px; }
    .item-nama { font-weight: 700; font-size: .9rem; color: #1e293b; }
    .item-detail { font-size: .82rem; color: #64748b; }
    .item-subtotal { font-weight: 800; color: #2563eb; font-size: .95rem; }
    .total-box { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; border-radius: 12px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }
    .btn-scan-again { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 10px; padding: 12px 28px; font-weight: 700; width: 100%; margin-top: 16px; }
    .status-badge { background: #dbeafe; color: #2563eb; padding: 6px 14px; border-radius: 20px; font-size: .85rem; font-weight: 600; }
    .nav-pills .nav-link { border-radius: 10px; color: #64748b; font-weight: 600; }
    .nav-pills .nav-link.active { background: #2563eb; color: white; }
</style>

{{-- Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-qr-code-scan me-2"></i>QR Code Scanner</h4>
            <small class="opacity-75">Scan QR Code customer untuk verifikasi pesanan</small>
        </div>
        <!-- <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-light btn-sm fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a> -->
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Nav --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link px-4" href="{{ route('vendor.dashboard') }}">
                <i class="bi bi-receipt me-1"></i> Pesanan Lunas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4" href="{{ route('vendor.menus') }}">
                <i class="bi bi-menu-button-wide me-1"></i> Kelola Menu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active px-4" href="{{ route('vendor.scan-qr') }}">
                <i class="bi bi-qr-code-scan me-1"></i> Scan QR Code
            </a>
        </li>
    </ul>

    <div class="row g-4 justify-content-center">

        {{-- Scanner --}}
        <div class="col-lg-5" id="scannerSection">
            <div class="card scanner-card">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <span class="status-badge" id="statusBadge">
                            <i class="bi bi-camera me-1"></i> Siap Scan
                        </span>
                    </div>
                    <div id="reader"></div>
                    <p class="text-center text-muted small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Arahkan kamera ke QR Code customer
                    </p>
                </div>
            </div>
        </div>

        {{-- Hasil Scan --}}
        <div class="col-lg-7">

            {{-- Placeholder --}}
            <div class="card scanner-card text-center py-5" id="placeholderCard">
                <i class="bi bi-qr-code fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-0">Hasil scan akan muncul di sini</p>
                <small class="text-muted">Scan QR Code customer untuk melihat detail pesanan</small>
            </div>

            {{-- Hasil --}}
            <div class="card result-card" id="resultCard">
                <div class="result-header" id="resultHeader">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-check-circle-fill fs-4" id="resultIcon"></i>
                        <h5 class="mb-0 fw-bold" id="resultTitle">Pesanan Ditemukan</h5>
                    </div>
                    <small class="opacity-75" id="resultSubtitle">QR Code berhasil dibaca</small>
                </div>
                <div class="result-body">

                    {{-- Info Pesanan --}}
                    <div class="info-row">
                        <span class="info-label">ID Pesanan</span>
                        <span class="info-value" id="resultIdPesanan">-</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama Customer</span>
                        <span class="info-value" id="resultNama">-</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Metode Bayar</span>
                        <span class="info-value" id="resultMetode">-</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Pembayaran</span>
                        <span id="resultStatus">-</span>
                    </div>

                    {{-- Item Pesanan --}}
                    <h6 class="fw-bold mt-4 mb-3 text-muted" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;">
                        Menu yang Dipesan
                    </h6>
                    <div id="resultItems"></div>

                    {{-- Total --}}
                    <div class="total-box">
                        <span class="fw-semibold">Total Pembayaran</span>
                        <span class="fw-bold fs-5" id="resultTotal">-</span>
                    </div>

                    <button class="btn-scan-again" onclick="scanLagi()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Scan QR Code Lain
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Suara beep --}}
<audio id="beepSound" preload="auto" volume="1.0">
    <source src="{{ asset('sounds/u_edtmwfwu7c-beep-329314.mp3') }}" type="audio/mpeg">
</audio>

{{-- html5-qrcode CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isScanning  = false;
    let scanDone    = false;

    function startScanner() {
        html5QrCode = new Html5Qrcode('reader');

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
        };

        html5QrCode.start(
            { facingMode: 'environment' },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            document.getElementById('statusBadge').innerHTML =
                '<i class="bi bi-record-circle text-danger me-1"></i> Sedang Scan...';
        }).catch(() => {
            // Fallback ke kamera depan
            html5QrCode.start({ facingMode: 'user' }, config, onScanSuccess, onScanError)
                .then(() => { isScanning = true; });
        });
    }

    function onScanSuccess(decodedText) {
        if (scanDone) return;
        scanDone = true;

        // STEP 1: Bunyikan beep DULU sebelum stop scanner
        const beep = document.getElementById('beepSound');
        beep.currentTime = 0;
        beep.volume = 1.0;
        
        const playPromise = beep.play();
        
        if (playPromise !== undefined) {
            playPromise.then(() => {
                // STEP 2: Setelah beep siap main, baru hentikan scanner
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                    }).catch(() => {});
                }
            }).catch(err => {
                // Jika audio blocked, langsung stop scanner
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                    }).catch(() => {});
                }
                console.log('Audio blocked:', err);
            });
        }

        // STEP 3: Update status
        document.getElementById('statusBadge').innerHTML =
            '<i class="bi bi-check-circle text-success me-1"></i> Berhasil Dibaca';

        // STEP 4: Cari pesanan ke server (dengan sedikit delay untuk UX)
        setTimeout(() => {
            fetch(`/vendor/find-pesanan-qr?idpesanan=${encodeURIComponent(decodedText)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    tampilkanHasil(data);
                } else {
                    alert('Pesanan tidak ditemukan: ' + decodedText);
                    scanLagi();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat mencari pesanan.');
                scanLagi();
            });
        }, 300); // Delay 300ms untuk beep terdengar jelas
    }

    function onScanError(error) {
        // Abaikan error scan biasa
    }

    function tampilkanHasil(data) {
        const isLunas = data.status_bayar === 'lunas';

        const header = document.getElementById('resultHeader');
        header.className = 'result-header ' + (isLunas ? 'lunas' : 'pending');

        document.getElementById('resultIcon').className    = isLunas ? 'bi bi-check-circle-fill fs-4' : 'bi bi-hourglass-split fs-4';
        document.getElementById('resultTitle').textContent = isLunas ? 'Pembayaran Lunas' : 'Belum Dibayar';
        document.getElementById('resultSubtitle').textContent = 'ID Pesanan: #' + data.idpesanan;

        // Info pesanan
        document.getElementById('resultIdPesanan').textContent = '#' + data.idpesanan;
        document.getElementById('resultNama').textContent      = data.nama;
        document.getElementById('resultTotal').textContent     = data.total_format;

        // Metode bayar
        let metodeText = '-';
        if (data.metode_bayar === 'virtual_account') metodeText = '🏦 Virtual Account';
        else if (data.metode_bayar === 'qris') metodeText = '📱 QRIS';
        document.getElementById('resultMetode').textContent = metodeText;

        // Status badge
        document.getElementById('resultStatus').innerHTML = isLunas
            ? '<span class="badge-lunas"><i class="bi bi-check-circle me-1"></i>Lunas</span>'
            : '<span class="badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';

        // Items
        const itemsContainer = document.getElementById('resultItems');
        itemsContainer.innerHTML = data.items.map(item => `
            <div class="item-row">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="item-nama">${item.nama_menu}</div>
                        <div class="item-detail">${item.jumlah} x ${item.harga_format}
                            ${item.catatan !== '-' ? '<br>📝 ' + item.catatan : ''}
                        </div>
                    </div>
                    <div class="item-subtotal">${item.subtotal_format}</div>
                </div>
            </div>
        `).join('');

        // Tampilkan result card
        document.getElementById('placeholderCard').style.display = 'none';
        document.getElementById('resultCard').classList.add('show');
    }

    function scanLagi() {
        scanDone = false;
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('placeholderCard').style.display = 'block';
        document.getElementById('statusBadge').innerHTML =
            '<i class="bi bi-camera me-1"></i> Siap Scan';

        if (!isScanning) {
            startScanner();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        startScanner();
    });

    window.addEventListener('beforeunload', function () {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().catch(() => {});
        }
    });
</script>
@endsection