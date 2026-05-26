<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NFC Absensi Scanner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        :root {
            --navy:#0f1b3c; --navy2:#162347;
            --accent:#3b82f6; --green:#22c55e;
            --gold:#f59e0b; --red:#ef4444;
            --white:#ffffff; --gray:#94a3b8;
        }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background:var(--navy); color:var(--white);
            min-height:100vh; min-height:100dvh;
            display:flex; flex-direction:column;
        }

        /* ─── Header ─── */
        .header {
            background:var(--navy2);
            border-bottom:1px solid rgba(255,255,255,0.08);
            padding:16px 20px;
            display:flex; align-items:center; justify-content:space-between;
        }
        .header-brand { font-size:1.1rem; font-weight:800; }
        .header-brand span { color:var(--accent); }
        .header-time { font-size:0.85rem; color:var(--gray); font-variant-numeric:tabular-nums; }

        /* ─── Main ─── */
        .main { flex:1; padding:20px 16px; display:flex; flex-direction:column; gap:16px; }

        /* ─── Input mata kuliah ─── */
        .mk-row { display:flex; gap:10px; align-items:center; }
        .mk-label { font-size:0.78rem; color:var(--gray); font-weight:600; white-space:nowrap; }
        .mk-input {
            flex:1; padding:10px 14px;
            background:rgba(255,255,255,0.08);
            border:1.5px solid rgba(255,255,255,0.12);
            border-radius:10px; color:var(--white);
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:0.9rem; outline:none;
        }
        .mk-input:focus { border-color:var(--accent); }
        .mk-input::placeholder { color:rgba(255,255,255,0.3); }

        /* ─── Scanner area ─── */
        .scanner-card {
            background:var(--navy2);
            border:2px solid rgba(255,255,255,0.08);
            border-radius:20px; padding:32px 20px;
            text-align:center; flex:1;
            display:flex; flex-direction:column;
            align-items:center; justify-content:center; gap:16px;
        }
        .nfc-icon {
            width:100px; height:100px;
            border-radius:50%;
            background:rgba(59,130,246,0.1);
            border:3px solid rgba(59,130,246,0.3);
            display:flex; align-items:center; justify-content:center;
            font-size:2.8rem;
            transition:all 0.3s;
        }
        .nfc-icon.scanning {
            background:rgba(59,130,246,0.2);
            border-color:var(--accent);
            animation:ripple 1.5s infinite;
        }
        .nfc-icon.success { background:rgba(34,197,94,0.2); border-color:var(--green); }
        .nfc-icon.error   { background:rgba(239,68,68,0.2);  border-color:var(--red); }
        .nfc-icon.warning { background:rgba(245,158,11,0.2); border-color:var(--gold); }

        @keyframes ripple {
            0%   { box-shadow:0 0 0 0 rgba(59,130,246,0.4); }
            70%  { box-shadow:0 0 0 20px rgba(59,130,246,0); }
            100% { box-shadow:0 0 0 0 rgba(59,130,246,0); }
        }

        .scanner-title { font-size:1.2rem; font-weight:800; }
        .scanner-sub   { font-size:0.85rem; color:var(--gray); line-height:1.5; }

        /* ─── Tombol ─── */
        .btn-scan {
            width:100%; padding:16px;
            background:linear-gradient(135deg, #1e3a8a, #3b82f6);
            color:white; border:none; border-radius:14px;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:1rem; font-weight:800; cursor:pointer;
            transition:opacity 0.2s, transform 0.1s;
            display:flex; align-items:center; justify-content:center; gap:10px;
        }
        .btn-scan:active   { transform:scale(0.97); }
        .btn-scan:disabled { opacity:0.5; cursor:not-allowed; }
        .btn-scan.active   { background:linear-gradient(135deg, #065f46, #22c55e); }

        .btn-stop {
            width:100%; padding:13px;
            background:transparent;
            color:var(--red); border:1.5px solid var(--red);
            border-radius:14px;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:0.9rem; font-weight:700; cursor:pointer;
            transition:background 0.2s;
        }
        .btn-stop:hover { background:rgba(239,68,68,0.1); }

        /* ─── Result card ─── */
        .result-card {
            border-radius:16px; padding:20px;
            display:none; animation:fadeUp 0.4s ease;
        }
        .result-card.show { display:block; }
        .result-card.berhasil { background:rgba(34,197,94,0.12); border:1.5px solid rgba(34,197,94,0.3); }
        .result-card.gagal    { background:rgba(239,68,68,0.12);  border:1.5px solid rgba(239,68,68,0.3); }
        .result-card.warning  { background:rgba(245,158,11,0.12); border:1.5px solid rgba(245,158,11,0.3); }

        .result-status {
            font-size:0.75rem; font-weight:700;
            letter-spacing:1.5px; text-transform:uppercase;
            margin-bottom:8px;
        }
        .result-card.berhasil .result-status { color:var(--green); }
        .result-card.gagal    .result-status { color:var(--red); }
        .result-card.warning  .result-status { color:var(--gold); }

        .result-nama  { font-size:1.2rem; font-weight:800; margin-bottom:4px; }
        .result-nim   { font-size:0.85rem; color:var(--gray); margin-bottom:12px; }
        .result-detail{ display:flex; gap:8px; flex-wrap:wrap; }
        .result-chip  {
            padding:4px 12px; border-radius:20px;
            font-size:0.75rem; font-weight:700;
            background:rgba(255,255,255,0.08);
        }
        .chip-hadir    { background:rgba(34,197,94,0.2);  color:var(--green); }
        .chip-terlambat{ background:rgba(245,158,11,0.2); color:var(--gold); }

        .result-pesan { font-size:0.9rem; font-weight:600; }

        /* ─── Log scan ─── */
        .log-title {
            font-size:0.75rem; font-weight:700;
            letter-spacing:1.5px; text-transform:uppercase;
            color:var(--gray); margin-bottom:8px;
        }
        .log-list { display:flex; flex-direction:column; gap:6px; max-height:200px; overflow-y:auto; }
        .log-item {
            background:rgba(255,255,255,0.04);
            border-radius:10px; padding:10px 14px;
            display:flex; align-items:center; justify-content:space-between;
            font-size:0.82rem;
        }
        .log-nama  { font-weight:700; }
        .log-waktu { color:var(--gray); font-size:0.75rem; }

        /* ─── Tidak didukung ─── */
        .unsupported {
            background:rgba(239,68,68,0.1);
            border:1.5px solid rgba(239,68,68,0.3);
            border-radius:14px; padding:20px; text-align:center;
        }
        .unsupported h3 { color:var(--red); margin-bottom:8px; }
        .unsupported p  { color:var(--gray); font-size:0.85rem; line-height:1.6; }

        /* ─── Footer link ─── */
        .footer-links {
            padding:12px 16px;
            display:flex; gap:12px; justify-content:center;
            border-top:1px solid rgba(255,255,255,0.06);
        }
        .footer-link {
            color:var(--gray); font-size:0.8rem;
            text-decoration:none; transition:color 0.2s;
        }
        .footer-link:hover { color:var(--white); }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(10px); }
            to   { opacity:1; transform:translateY(0); }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-brand">NFC <span>Absensi</span></div>
    <div class="header-time" id="headerTime">--:--:--</div>
</div>

<div class="main">

    <div class="mk-row">
        <span class="mk-label">Mata Kuliah:</span>
        <input type="text" id="mataKuliah" class="mk-input"
            placeholder="cth: Pemrograman Web"
            value="Pemrograman Web">
    </div>

    <!-- Cek dukungan NFC -->
    @if(true)
    <!-- NFC Scanner Area -->
    <div class="scanner-card" id="scannerCard">
        <div class="nfc-icon" id="nfcIcon">📡</div>
        <div>
            <div class="scanner-title" id="scannerTitle">Siap Scan NFC</div>
            <div class="scanner-sub" id="scannerSub">
                Tekan tombol di bawah lalu dekatkan<br>kartu NFC ke belakang HP Anda
            </div>
        </div>
    </div>

    <!-- Result -->
    <div class="result-card" id="resultCard">
        <div class="result-status" id="resultStatus">—</div>
        <div class="result-nama"   id="resultNama">—</div>
        <div class="result-nim"    id="resultNim">—</div>
        <div class="result-detail" id="resultDetail"></div>
        <div class="result-pesan"  id="resultPesan" style="margin-top:8px;"></div>
    </div>

    <button class="btn-scan" id="btnScan" onclick="mulaiScan()">
        Aktifkan NFC Scanner
    </button>
    <button class="btn-stop" id="btnStop" onclick="hentikanScan()" style="display:none;">
        Hentikan Scanner
    </button>

    <div>
        <div class="log-title">Log Scan Hari Ini</div>
        <div class="log-list" id="logList">
            <div style="color:var(--gray);font-size:0.82rem;text-align:center;padding:12px;">
                Belum ada scan hari ini
            </div>
        </div>
    </div>

    @else
    <div class="unsupported">
        <h3>⚠️ Koneksi Tidak Aman</h3>
        <p>Web NFC API memerlukan koneksi <strong>HTTPS</strong> atau <strong>localhost</strong>.<br><br>
        Gunakan ngrok untuk membuat tunnel HTTPS:<br>
        <code style="background:rgba(255,255,255,0.1);padding:2px 8px;border-radius:4px;">ngrok http 8000</code><br><br>
        Lalu akses URL ngrok dari HP Android Chrome.</p>
    </div>
    @endif

</div>

<div class="footer-links">
    <a href="{{ route('nfc.kartu') }}" class="footer-link">Daftar Kartu</a>
    <a href="{{ route('nfc.riwayat') }}" class="footer-link">Riwayat</a>
</div>

<script>
// Clock
(function tick() {
    document.getElementById('headerTime').textContent =
        new Date().toLocaleTimeString('id-ID', { hour12: false });
    setTimeout(tick, 1000);
})();

// State 
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
let ndefReader = null;
let isScanning = false;
let logItems   = [];

// Mulai scan NFC 
async function mulaiScan() {
    if (!('NDEFReader' in window)) {
        tampilkanError('Browser tidak mendukung Web NFC. Gunakan Android Chrome ≥ 89.');
        return;
    }

    try {
        ndefReader = new NDEFReader();
        await ndefReader.scan();

        isScanning = true;
        updateUI('scanning');

        // Event: kartu berhasil dibaca
        ndefReader.addEventListener('reading', ({ serialNumber, message }) => {
            // Decode isi kartu (jika ada teks)
            let isiKartu = '';
            for (const record of message.records) {
                try {
                    isiKartu += new TextDecoder(record.encoding || 'utf-8').decode(record.data);
                } catch (e) {}
            }

            console.log('NFC Serial:', serialNumber);
            console.log('Isi kartu:', isiKartu);
            console.log('Jumlah record:', message.records.length);

            prosesKeServer(serialNumber, isiKartu);
        });

        // Event: error saat scanning
        ndefReader.addEventListener('readingerror', () => {
            tampilkanError('Gagal membaca kartu. Coba dekatkan ulang.');
        });

    } catch (err) {
        console.error('NFC Error:', err);
        if (err.name === 'NotAllowedError') {
            tampilkanError('Izin NFC ditolak. Berikan izin NFC di popup browser.');
        } else if (err.name === 'NotSupportedError') {
            tampilkanError('NFC tidak didukung atau dinonaktifkan di perangkat ini.');
        } else {
            tampilkanError('Error: ' + err.message);
        }
        isScanning = false;
        updateUI('idle');
    }
}

// Hentikan scan
function hentikanScan() {
    isScanning = false;
    ndefReader = null;
    updateUI('idle');
    sembunyikanResult();
}

// Kirim ke server Laravel 
async function prosesKeServer(serialNumber, isiKartu) {
    updateUI('processing');

    try {
        const res = await fetch('{{ route("nfc.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                serial_number: serialNumber,
                mata_kuliah:   document.getElementById('mataKuliah').value || 'Umum',
                isi_kartu:     isiKartu,
            }),
        });

        const data = await res.json();
        console.log('Response server:', data);

        tampilkanResult(data, serialNumber);
        tambahLog(data, serialNumber);

    } catch (err) {
        console.error('Fetch error:', err);
        tampilkanError('Gagal terhubung ke server: ' + err.message);
    } finally {
        // Kembali ke mode scanning setelah 3 detik
        if (isScanning) {
            setTimeout(() => {
                updateUI('scanning');
                sembunyikanResult();
            }, 4000);
        }
    }
}

// Update tampilan
function updateUI(mode) {
    const icon    = document.getElementById('nfcIcon');
    const title   = document.getElementById('scannerTitle');
    const sub     = document.getElementById('scannerSub');
    const btnScan = document.getElementById('btnScan');
    const btnStop = document.getElementById('btnStop');

    if (mode === 'idle') {
        icon.className  = 'nfc-icon';
        icon.textContent= '📡';
        title.textContent = 'Siap Scan NFC';
        sub.textContent   = 'Tekan tombol di bawah lalu dekatkan kartu NFC ke belakang HP Anda';
        btnScan.style.display = '';
        btnScan.className     = 'btn-scan';
        btnScan.textContent   = '📡 Aktifkan NFC Scanner';
        btnStop.style.display = 'none';
    } else if (mode === 'scanning') {
        icon.className  = 'nfc-icon scanning';
        icon.textContent= '📡';
        title.textContent = 'NFC Aktif — Dekatkan Kartu';
        sub.textContent   = 'Scanner sedang aktif. Dekatkan kartu NFC ke bagian belakang HP.';
        btnScan.style.display = 'none';
        btnStop.style.display = '';
    } else if (mode === 'processing') {
        icon.className  = 'nfc-icon scanning';
        icon.textContent= '⏳';
        title.textContent = 'Memproses...';
        sub.textContent   = 'Mengirim data ke server...';
    }
}

// Tampilkan hasil 
function tampilkanResult(data, serial) {
    const card   = document.getElementById('resultCard');
    const status = document.getElementById('resultStatus');
    const nama   = document.getElementById('resultNama');
    const nim    = document.getElementById('resultNim');
    const detail = document.getElementById('resultDetail');
    const pesan  = document.getElementById('resultPesan');
    const icon   = document.getElementById('nfcIcon');

    card.className = 'result-card show';

    if (data.status === 'berhasil') {
        card.classList.add('berhasil');
        icon.className = 'nfc-icon success';
        icon.textContent = '✅';
        status.textContent  = '✅ Absensi Berhasil';
        nama.textContent    = data.mahasiswa.nama;
        nim.textContent     = data.mahasiswa.nim + ' — ' + (data.mahasiswa.prodi || '') + ' ' + (data.mahasiswa.kelas || '');
        detail.innerHTML    = `
            <span class="result-chip chip-${data.absensi.status}">${data.absensi.status.toUpperCase()}</span>
            <span class="result-chip">${data.absensi.waktu}</span>
            <span class="result-chip">${data.absensi.mata_kuliah}</span>
        `;
        pesan.textContent = '';
    } else if (data.status === 'sudah_absen') {
        card.classList.add('warning');
        icon.className = 'nfc-icon warning';
        icon.textContent = '⚠️';
        status.textContent  = '⚠️ Sudah Absen';
        nama.textContent    = data.mahasiswa.nama;
        nim.textContent     = data.mahasiswa.nim;
        detail.innerHTML    = '';
        pesan.textContent   = 'Mahasiswa ini sudah tercatat hadir hari ini.';
    } else {
        card.classList.add('gagal');
        icon.className = 'nfc-icon error';
        icon.textContent = '❌';
        status.textContent  = '❌ Kartu Tidak Dikenal';
        nama.textContent    = 'Serial: ' + serial;
        nim.textContent     = '';
        detail.innerHTML    = '';
        pesan.textContent   = 'Kartu belum terdaftar. Daftarkan terlebih dahulu.';
    }
}

function tampilkanError(msg) {
    const card = document.getElementById('resultCard');
    card.className = 'result-card show gagal';
    document.getElementById('resultStatus').textContent = '❌ Error';
    document.getElementById('resultNama').textContent   = msg;
    document.getElementById('resultNim').textContent    = '';
    document.getElementById('resultDetail').innerHTML   = '';
    document.getElementById('resultPesan').textContent  = '';
    document.getElementById('nfcIcon').className = 'nfc-icon error';
    document.getElementById('nfcIcon').textContent = '❌';
}

function sembunyikanResult() {
    document.getElementById('resultCard').className = 'result-card';
}

// Log scan 
function tambahLog(data, serial) {
    const item = {
        nama:   data.mahasiswa ? data.mahasiswa.nama : 'Tidak dikenal',
        status: data.status,
        waktu:  new Date().toLocaleTimeString('id-ID', { hour12: false }),
        absensiStatus: data.absensi ? data.absensi.status : null,
    };
    logItems.unshift(item);

    const el = document.getElementById('logList');
    el.innerHTML = logItems.slice(0, 10).map(i => `
        <div class="log-item">
            <div>
                <div class="log-nama">${i.nama}</div>
                <div class="log-waktu">${i.waktu}</div>
            </div>
            <span class="result-chip ${i.absensiStatus === 'hadir' ? 'chip-hadir' : i.absensiStatus === 'terlambat' ? 'chip-terlambat' : ''}">
                ${i.status === 'berhasil' ? (i.absensiStatus || 'hadir') : i.status}
            </span>
        </div>
    `).join('');
}
</script>
</body>
</html>