@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 20px 28px; border-radius: 0 0 20px 20px; margin-bottom: 28px; position: relative; }
    .page-header .btn { position: absolute; right: 28px; top: 50%; transform: translateY(-50%); }
    .step-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; overflow: hidden; }
    .step-header { background: #1e3a5f; color: white; padding: 14px 20px; font-weight: 700; font-size: .95rem; }
    .step-header .step-num { background: #2563eb; color: white; width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: .82rem; margin-right: 10px; }
    .step-body { padding: 20px; background: white; }
    #reader { width: 100%; border-radius: 10px; overflow: hidden; }
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: .85rem; font-weight: 600; }
    .badge-ready   { background: #dbeafe; color: #2563eb; }
    .badge-scan    { background: #fef3c7; color: #d97706; }
    .badge-done    { background: #dcfce7; color: #16a34a; }
    .toko-info { background: #f8fafc; border-radius: 12px; padding: 16px; border: 2px solid #e2e8f0; }
    .toko-nama { font-weight: 800; font-size: 1.1rem; color: #1e293b; margin-bottom: 4px; }
    .toko-coord { font-family: monospace; font-size: .82rem; color: #64748b; }
    .btn-ambil-lokasi { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 10px; padding: 12px 24px; font-weight: 700; width: 100%; font-size: .95rem; }
    .btn-ambil-lokasi:disabled { opacity: .6; cursor: not-allowed; }
    .lokasi-info { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; }
    .lokasi-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: .88rem; }
    .lokasi-row:last-child { border-bottom: none; }
    .lokasi-label { color: #64748b; }
    .lokasi-val { font-weight: 600; color: #1e293b; font-family: monospace; font-size: .82rem; }

    /* Result card */
    .result-card { border: none; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); overflow: hidden; display: none; }
    .result-card.show { display: block; animation: fadeIn .4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .result-header-diterima { background: linear-gradient(135deg, #16a34a, #15803d); color: white; padding: 24px; text-align: center; }
    .result-header-ditolak  { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; padding: 24px; text-align: center; }
    .result-icon { font-size: 3rem; margin-bottom: 8px; }
    .result-title { font-size: 1.4rem; font-weight: 800; margin-bottom: 4px; }
    .result-body { padding: 20px; background: white; }
    .calc-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: .9rem; }
    .calc-row:last-child { border-bottom: none; }
    .calc-label { color: #64748b; }
    .calc-val { font-weight: 700; color: #1e293b; }
    .calc-val.jarak { color: #2563eb; font-size: 1rem; }
    .calc-val.threshold { color: #d97706; }
    .btn-scan-lagi { background: #2563eb; color: white; border: none; border-radius: 10px; padding: 10px 24px; font-weight: 700; width: 100%; margin-top: 12px; }

    /* Riwayat */
    .riwayat-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
    .riwayat-card .table thead th { background: #1e3a5f; color: white; border: none; padding: 12px 14px; font-size: .82rem; }
    .riwayat-card .table tbody td { padding: 10px 14px; font-size: .85rem; vertical-align: middle; }
    .badge-diterima { background: #dcfce7; color: #16a34a; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 700; }
    .badge-ditolak  { background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 700; }
</style>

<div class="page-header">
    <div>
        <h4 class="mb-1 fw-bold"><i class="mdi mdi-map-marker me-2"></i>Kunjungan Toko</h4>
        <small class="opacity-75">Scan barcode toko → Ambil lokasi → Validasi kehadiran</small>
    </div>
    <a href="{{ route('toko.index') }}" class="btn btn-outline-light btn-sm fw-semibold">
        <i class="mdi mdi-store me-1"></i> Master Toko
    </a>
</div>

<div class="container-fluid px-4">
<div class="row g-4">

    {{-- KOLOM KIRI: Langkah 1 & 2 --}}
    <div class="col-lg-6">

        {{-- STEP 1: Scan Barcode --}}
        <div class="step-card">
            <div class="step-header">
                <span class="step-num">1</span> Scan Barcode Toko
                <span class="status-badge badge-ready float-end" id="scanBadge">Siap Scan</span>
            </div>
            <div class="step-body">
                <div id="reader"></div>
                <p class="text-muted small text-center mt-2 mb-3">
                    <i class="bi bi-info-circle me-1"></i>Arahkan kamera ke barcode pada label toko
                </p>

                <!-- Fallback: Input manual jika scan gagal -->
                <div class="mt-3 mb-3">
                    <small class="text-muted d-block mb-2">📝 Atau input barcode manual:</small>
                    <input type="text" id="manualBarcode" class="form-control form-control-sm" 
                           placeholder="Contoh: TOKO00001" maxlength="15">
                    <button class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="scanManual()">
                        <i class="bi bi-arrow-return me-1"></i> Cari Barcode
                    </button>
                </div>

                {{-- Info toko setelah scan --}}
                <div id="tokoInfo" class="toko-info d-none">
                    <div class="toko-nama" id="tokoNama">-</div>
                    <div class="text-muted small mb-1" id="tokoAlamat">-</div>
                    <div class="toko-coord" id="tokoCoord">-</div>
                    <div class="mt-2">
                        <span class="badge bg-light text-dark border">
                            Akurasi Toko: <strong id="tokoAcc">-</strong>m
                        </span>
                    </div>
                </div>

                <button class="btn btn-outline-secondary btn-sm w-100 mt-2 d-none" id="btnScanLagi" onclick="resetScan()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Scan Toko Lain
                </button>
            </div>
        </div>

        {{-- STEP 2: Ambil Lokasi --}}
        <div class="step-card">
            <div class="step-header">
                <span class="step-num">2</span> Ambil Lokasi Sales
                <span class="status-badge badge-ready float-end" id="lokasiBadge">Belum Diambil</span>
            </div>
            <div class="step-body">
                <button class="btn-ambil-lokasi mb-3" id="btnAmbilLokasi" onclick="ambilLokasi()" disabled>
                    <i class="bi bi-crosshair me-2"></i>Ambil Lokasi Saya Sekarang
                </button>

                <div id="lokasiInfo" class="lokasi-info d-none">
                    <div class="lokasi-row">
                        <span class="lokasi-label">Latitude</span>
                        <span class="lokasi-val" id="salesLat">-</span>
                    </div>
                    <div class="lokasi-row">
                        <span class="lokasi-label">Longitude</span>
                        <span class="lokasi-val" id="salesLng">-</span>
                    </div>
                    <div class="lokasi-row">
                        <span class="lokasi-label">Akurasi GPS</span>
                        <span class="lokasi-val" id="salesAcc">-</span>
                    </div>
                </div>

                <button class="btn btn-success w-100 mt-3 fw-bold d-none" id="btnSubmit" onclick="submitKunjungan()"
                        style="border-radius:10px;padding:12px;">
                    <i class="bi bi-check-circle me-2"></i>Validasi Kunjungan
                </button>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Hasil & Riwayat --}}
    <div class="col-lg-6">

        {{-- Hasil Validasi --}}
        <div class="result-card" id="resultCard">
            <div id="resultHeader">
                <div class="result-icon" id="resultIcon">✅</div>
                <div class="result-title" id="resultTitle">Kunjungan Diterima</div>
                <div id="resultSubtitle" style="opacity:.85;font-size:.9rem;"></div>
            </div>
            <div class="result-body">
                <h6 class="fw-bold text-muted mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;">
                    Detail Perhitungan
                </h6>
                <div class="calc-row">
                    <span class="calc-label">Toko</span>
                    <span class="calc-val" id="calcToko">-</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Jarak Aktual</span>
                    <span class="calc-val jarak" id="calcJarak">-</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Radius Threshold</span>
                    <span class="calc-val">300 meter</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Akurasi Toko</span>
                    <span class="calc-val" id="calcAccToko">-</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Akurasi Sales</span>
                    <span class="calc-val" id="calcAccSales">-</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label"><strong>Threshold Efektif</strong></span>
                    <span class="calc-val threshold" id="calcThreshold">-</span>
                </div>
                <div class="mt-3 p-3 rounded-3" id="calcFormula"
                     style="background:#f8fafc;font-size:.82rem;color:#475569;font-family:monospace;"></div>
                <button class="btn-scan-lagi" onclick="resetSemua()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Kunjungan Berikutnya
                </button>
            </div>
        </div>

        {{-- Riwayat Kunjungan --}}
        <div class="riwayat-card">
            <div class="step-header">
                <span class="step-num" style="background:#475569;">📋</span> Riwayat Kunjungan (10 Terakhir)
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Toko</th>
                            <th>Jarak</th>
                            <th>Threshold</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatBody">
                        @forelse($riwayat as $r)
                        <tr>
                            <td class="fw-semibold">{{ $r->toko->nama_toko ?? '-' }}</td>
                            <td>{{ $r->jarak_meter }}m</td>
                            <td>{{ $r->threshold_efektif }}m</td>
                            <td>
                                @if($r->status === 'diterima')
                                    <span class="badge-diterima">✅ Diterima</span>
                                @else
                                    <span class="badge-ditolak">❌ Ditolak</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:.78rem;">{{ $r->created_at->format('d/m H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada riwayat kunjungan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</div>

{{-- Audio beep --}}
<audio id="beepSound" preload="auto">
    <source src="{{ asset('sounds/u_edtmwfwu7c-beep-329314.mp3') }}" type="audio/mpeg">
</audio>

{{-- html5-qrcode CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isScanning  = false;
    let scanDone    = false;
    let tokoData    = null;   // data toko hasil scan
    let salesLat    = null;
    let salesLng    = null;
    let salesAcc    = null;

    // ===== BEEP SOUND =====
    // Tone generator jika file audio tidak bisa diplay
    function playBeep() {
        const audioEl = document.getElementById('beepSound');
        if (audioEl) {
            audioEl.currentTime = 0;
            audioEl.volume = 1.0;
            const playPromise = audioEl.play();
            if (playPromise !== undefined) {
                playPromise.catch((error) => {
                    console.log('Audio play failed, using tone:', error);
                    playTone(800, 200); // fallback: generate tone 800Hz untuk 200ms
                });
            }
        } else {
            playTone(800, 200);
        }
    }

    // Generate tone menggunakan Web Audio API (fallback)
    function playTone(frequency = 800, duration = 200) {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = frequency;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration / 1000);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + duration / 1000);
        } catch (e) {
            console.log('Web Audio API not supported:', e);
        }
    }

    // ===== STEP 1: SCANNER =====
    function startScanner() {
        html5QrCode = new Html5Qrcode('reader');
        const config = {
            fps: 20,
            qrbox: { width: 380, height: 220 },
            aspectRatio: 1.77,
            disableFlip: false,
            rememberLastUsedCamera: true,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
            ],
        };
        html5QrCode.start(
            { facingMode: 'environment' }, config, onScanSuccess, () => {}
        ).then(() => {
            isScanning = true;
            document.getElementById('scanBadge').className = 'status-badge badge-scan float-end';
            document.getElementById('scanBadge').textContent = 'Sedang Scan...';
        }).catch(() => {
            html5QrCode.start({ facingMode: 'user' }, config, onScanSuccess, () => {})
                .then(() => { isScanning = true; });
        });
    }

    function onScanSuccess(decodedText) {
        if (scanDone) return;
        
        console.log('✓ Barcode scanned:', decodedText, 'Length:', decodedText.length);
        
        scanDone = true;

        // Play beep sound
        playBeep();

        // Stop scanner
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => isScanning = false);
        }

        document.getElementById('scanBadge').className = 'status-badge badge-done float-end';
        document.getElementById('scanBadge').textContent = 'Berhasil';

        // Cari toko
        setTimeout(() => {
            fetch(`/toko/find-barcode?barcode=${encodeURIComponent(decodedText)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    tokoData = data;
                    tampilkanInfoToko(data);
                    // Aktifkan tombol ambil lokasi
                    document.getElementById('btnAmbilLokasi').disabled = false;
                    document.getElementById('btnScanLagi').classList.remove('d-none');
                } else {
                    alert('Toko tidak ditemukan: ' + decodedText);
                    resetScan();
                }
            })
            .catch((err) => { 
                console.error('Error:', err); 
                alert('Gagal mencari toko.'); 
                resetScan(); 
            });
        }, 300);
    }

    function tampilkanInfoToko(data) {
        document.getElementById('tokoNama').textContent  = data.nama_toko;
        document.getElementById('tokoAlamat').textContent= data.alamat;
        document.getElementById('tokoCoord').textContent = data.latitude + ', ' + data.longitude;
        document.getElementById('tokoAcc').textContent   = data.accuracy;
        document.getElementById('tokoInfo').classList.remove('d-none');
    }

    function resetScan() {
        scanDone = false;
        tokoData = null;
        document.getElementById('tokoInfo').classList.add('d-none');
        document.getElementById('btnScanLagi').classList.add('d-none');
        document.getElementById('btnAmbilLokasi').disabled = true;
        document.getElementById('scanBadge').className = 'status-badge badge-ready float-end';
        document.getElementById('scanBadge').textContent = 'Siap Scan';
        document.getElementById('manualBarcode').value = '';
        if (!isScanning) startScanner();
    }

    // Input manual barcode
    function scanManual() {
        const barcode = document.getElementById('manualBarcode').value.trim();
        if (!barcode) {
            alert('Masukkan barcode terlebih dahulu');
            return;
        }
        
        console.log('Manual input barcode:', barcode);
        
        fetch(`/toko/find-barcode?barcode=${encodeURIComponent(barcode)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            console.log('Manual search response:', data);
            if (data.success) {
                tokoData = data;
                tampilkanInfoToko(data);
                document.getElementById('btnAmbilLokasi').disabled = false;
                document.getElementById('btnScanLagi').classList.remove('d-none');
                document.getElementById('scanBadge').className = 'status-badge badge-done float-end';
                document.getElementById('scanBadge').textContent = 'Berhasil';
                if (html5QrCode && isScanning) html5QrCode.stop().then(() => isScanning = false);
                scanDone = true;
            } else {
                alert('Toko tidak ditemukan: ' + barcode);
            }
        })
        .catch((err) => { 
            console.error('Error:', err); 
            alert('Gagal mencari toko.'); 
        });
    }

    // ===== STEP 2: AMBIL LOKASI =====
    function ambilLokasi() {
        if (!navigator.geolocation) { alert('Browser tidak mendukung Geolocation.'); return; }

        const badge = document.getElementById('lokasiBadge');
        badge.className = 'status-badge badge-scan float-end';
        badge.textContent = 'Mengambil...';
        document.getElementById('btnAmbilLokasi').disabled = true;

        // getAccuratePosition: ambil beberapa kali, pilih yang paling akurat
        let bestPosition = null;
        let attempts = 0;
        const maxAttempts = 5;

        function tryGetPosition() {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    attempts++;
                    if (!bestPosition || pos.coords.accuracy < bestPosition.coords.accuracy) {
                        bestPosition = pos;
                    }
                    if (attempts < maxAttempts && bestPosition.coords.accuracy > 20) {
                        setTimeout(tryGetPosition, 1000);
                    } else {
                        onLokasiDidapat(bestPosition);
                    }
                },
                function(err) {
                    let msg = 'Gagal mengambil lokasi.';
                    if (err.code === 1) msg = 'Akses lokasi ditolak. Izinkan di browser.';
                    badge.className = 'status-badge float-end';
                    badge.style.background = '#fee2e2';
                    badge.style.color = '#dc2626';
                    badge.textContent = 'Gagal';
                    document.getElementById('btnAmbilLokasi').disabled = false;
                    alert(msg);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
        tryGetPosition();
    }

    function onLokasiDidapat(position) {
        salesLat = position.coords.latitude;
        salesLng = position.coords.longitude;
        salesAcc = Math.round(position.coords.accuracy);

        document.getElementById('salesLat').textContent = salesLat.toFixed(7);
        document.getElementById('salesLng').textContent = salesLng.toFixed(7);
        document.getElementById('salesAcc').textContent = '±' + salesAcc + ' meter';
        document.getElementById('lokasiInfo').classList.remove('d-none');
        document.getElementById('btnSubmit').classList.remove('d-none');
        document.getElementById('btnAmbilLokasi').disabled = false;

        const badge = document.getElementById('lokasiBadge');
        badge.className = 'status-badge badge-done float-end';
        badge.textContent = 'Berhasil';
    }

    // ===== STEP 3: SUBMIT KUNJUNGAN =====
    function submitKunjungan() {
        if (!tokoData || salesLat === null) {
            alert('Lengkapi scan barcode dan ambil lokasi terlebih dahulu.');
            return;
        }

        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memvalidasi...';

        fetch('/kunjungan/simpan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                toko_id:         tokoData.id,
                latitude_sales:  salesLat,
                longitude_sales: salesLng,
                accuracy_sales:  salesAcc,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tampilkanHasil(data);
                // Refresh riwayat
                setTimeout(() => location.reload(), 5000);
            } else {
                alert('Gagal: ' + data.message);
            }
            document.getElementById('btnSubmit').disabled = false;
            document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-check-circle me-2"></i>Validasi Kunjungan';
        })
        .catch(() => {
            alert('Terjadi kesalahan.');
            document.getElementById('btnSubmit').disabled = false;
        });
    }

    function tampilkanHasil(data) {
        const isDiterima = data.status === 'diterima';
        const header = document.getElementById('resultHeader');
        header.className = isDiterima ? 'result-header-diterima' : 'result-header-ditolak';
        document.getElementById('resultIcon').textContent  = isDiterima ? '✅' : '❌';
        document.getElementById('resultTitle').textContent = isDiterima ? 'Kunjungan DITERIMA' : 'Kunjungan DITOLAK';
        document.getElementById('resultSubtitle').textContent = data.pesan;

        document.getElementById('calcToko').textContent      = data.nama_toko;
        document.getElementById('calcJarak').textContent     = data.jarak_meter + ' meter';
        document.getElementById('calcAccToko').textContent   = data.accuracy_toko + ' meter';
        document.getElementById('calcAccSales').textContent  = data.accuracy_sales + ' meter';
        document.getElementById('calcThreshold').textContent = data.threshold_efektif + ' meter';
        document.getElementById('calcFormula').textContent   =
            'Threshold Efektif = ' + data.radius_threshold + ' (radius) + ' +
            data.accuracy_toko + ' (acc toko) + ' +
            data.accuracy_sales + ' (acc sales) = ' +
            data.threshold_efektif + 'm\n' +
            'Jarak Aktual = ' + data.jarak_meter + 'm\n' +
            'Status: ' + data.jarak_meter + 'm ' + (isDiterima ? '≤' : '>') + ' ' + data.threshold_efektif + 'm → ' + data.status.toUpperCase();

        document.getElementById('resultCard').classList.add('show');
        document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth' });
    }

    function resetSemua() {
        salesLat = null; salesLng = null; salesAcc = null;
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('lokasiInfo').classList.add('d-none');
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('lokasiBadge').className = 'status-badge badge-ready float-end';
        document.getElementById('lokasiBadge').textContent = 'Belum Diambil';
        resetScan();
    }

    // Mulai scanner saat halaman load
    document.addEventListener('DOMContentLoaded', startScanner);
    window.addEventListener('beforeunload', () => {
        if (html5QrCode && isScanning) html5QrCode.stop().catch(() => {});
    });
</script>
@endsection