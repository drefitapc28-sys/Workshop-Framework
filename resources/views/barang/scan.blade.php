@extends('layouts.app')

@section('content')
<style>
    body { 
    background: #f0f2f5; 
    }
    .page-header { 
        background: linear-gradient(135deg, #1e3a5f, #2563eb); 
        color: white; 
        padding: 20px 28px; 
        border-radius: 0 0 20px 20px; 
        margin-bottom: 28px; 
    }
    .scanner-card { 
        border: none; 
        border-radius: 16px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        overflow: hidden; 
    }
    #reader { 
        width: 100%; 
        border-radius: 12px; 
        overflow: hidden; 
    }
    .result-card { 
        border: none; 
        border-radius: 16px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        display: none; 
    }
    .result-card.show { 
        display: block; 
        animation: fadeIn .3s ease; 
    }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } 
    }
    .result-header { 
        background: linear-gradient(135deg, #16a34a, #15803d); 
        color: white; 
        padding: 20px 24px; 
    }
    .result-body { 
        padding: 24px; 
    }
    .info-row { 
        display: flex; 
        justify-content: space-between; 
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9; 
    }
    .info-row:last-child { 
        border-bottom: none; 
    }
    .info-label { 
        color: #64748b; 
        font-size: .9rem; 
    }
    .info-value { 
        font-weight: 700; 
        color: #1e293b; 
        font-size: .95rem; 
    }
    .harga-value { 
        font-weight: 800; 
        color: #16a34a; 
        font-size: 1.3rem; 
    }
    .btn-scan-again { 
        background: linear-gradient(135deg, #2563eb, #1d4ed8); 
        color: white; 
        border: none; 
        border-radius: 10px; 
        padding: 12px 28px;
        font-weight: 700; }
    .status-badge { 
        background: #dbeafe; color: #2563eb; 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: .85rem; 
        font-weight: 600; 
    }
    #scannerSection { 
        transition: all .3s; 
    }
</style>

{{-- Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content: space-between">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-upc-scan me-2"></i>Barcode Scanner</h4>
            <small class="opacity-75">Scan barcode label tag harga barang</small>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="row g-4 justify-content-center">

        {{-- Scanner --}}
        <div class="col-lg-6" id="scannerSection">
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
                        Arahkan kamera ke barcode label tag harga
                    </p>
                </div>
            </div>
        </div>

        {{-- Hasil Scan --}}
        <div class="col-lg-6">
            <div class="card result-card" id="resultCard">
                <div class="result-header">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                        <h5 class="mb-0 fw-bold">Barang Ditemukan!</h5>
                    </div>
                    <small class="opacity-75">Barcode berhasil dibaca</small>
                </div>
                <div class="result-body">
                    <div class="info-row">
                        <span class="info-label">ID Barang</span>
                        <span class="info-value" id="resultId">-</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nama Barang</span>
                        <span class="info-value" id="resultNama">-</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Harga</span>
                        <span class="harga-value" id="resultHarga">-</span>
                    </div>
                    <div class="mt-4 d-grid gap-2">
                        <button class="btn-scan-again" onclick="scanLagi()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Scan Lagi
                        </button>
                    </div>
                </div>
            </div>

            {{-- Placeholder saat belum scan --}}
            <div class="card scanner-card text-center py-5" id="placeholderCard">
                <i class="bi bi-upc fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-0">Hasil scan akan muncul di sini</p>
                <small class="text-muted">Arahkan kamera ke barcode untuk memulai</small>
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
            qrbox: { width: 300, height: 150 }, // kotak panjang untuk barcode
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
            ],
        };

        html5QrCode.start(
            { facingMode: 'environment' }, // kamera belakang
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            document.getElementById('statusBadge').innerHTML = '<i class="bi bi-record-circle text-danger me-1"></i> Sedang Scan...';
        }).catch(err => {
            console.error('Start scanner error:', err);
            // Coba kamera depan jika kamera belakang tidak ada
            html5QrCode.start(
                { facingMode: 'user' },
                config,
                onScanSuccess,
                onScanError
            );
        });
    }

    function onScanSuccess(decodedText) {
        if (scanDone) return; // Cegah multiple scan
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
        document.getElementById('statusBadge').innerHTML = '<i class="bi bi-check-circle text-success me-1"></i> Berhasil Dibaca';

        // STEP 4: Cari barang ke server (dengan sedikit delay untuk UX)
        setTimeout(() => {
            fetch(`/barang/find-barcode?id_barang=${encodeURIComponent(decodedText)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('resultId').textContent    = data.id_barang;
                    document.getElementById('resultNama').textContent  = data.nama;
                    document.getElementById('resultHarga').textContent = data.harga_format;
                    document.getElementById('placeholderCard').style.display = 'none';
                    document.getElementById('resultCard').classList.add('show');
                } else {
                    alert('Barang tidak ditemukan: ' + decodedText);
                    scanLagi();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat mencari barang.');
                scanLagi();
            });
        }, 300); // Delay 300ms untuk beep terdengar jelas
    }

    function onScanError(error) {
        // Abaikan error scan biasa (terjadi terus menerus saat tidak ada barcode)
    }

    function scanLagi() {
        scanDone = false;
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('placeholderCard').style.display = 'block';
        document.getElementById('statusBadge').innerHTML = '<i class="bi bi-camera me-1"></i> Siap Scan';

        if (!isScanning) {
            startScanner();
        }
    }

    // Mulai scanner saat halaman load
    document.addEventListener('DOMContentLoaded', function () {
        startScanner();
    });

    // Hentikan kamera saat meninggalkan halaman
    window.addEventListener('beforeunload', function () {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().catch(() => {});
        }
    });
</script>
@endsection