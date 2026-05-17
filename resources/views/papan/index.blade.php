<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian — RS Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --navy:#0a1628; --navy2:#0f1e3a; --navy3:#162347;
            --accent:#3b82f6; --gold:#f59e0b; --green:#22c55e;
            --white:#ffffff; --gray:#94a3b8;
            --border:rgba(255,255,255,0.07);
        }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background:var(--navy); color:var(--white);
            min-height:100vh; display:flex; flex-direction:column; overflow:hidden;
        }

        /* ─── Header ─── */
        .header {
            background:var(--navy2); border-bottom:1px solid var(--border);
            padding:16px 32px; display:flex; justify-content:space-between; align-items:center;
            flex-shrink:0;
        }
        .brand-name { font-size:1.5rem; font-weight:900; letter-spacing:-0.5px; }
        .brand-sub  { color:var(--gray); font-size:0.78rem; margin-top:2px; }
        .clock      { font-size:2rem; font-weight:800; letter-spacing:2px; text-align:right; font-variant-numeric:tabular-nums; }
        .date-str   { color:var(--gray); font-size:0.8rem; margin-top:2px; text-align:right; }
        .live-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(34,197,94,0.15); color:var(--green);
            border:1px solid rgba(34,197,94,0.3);
            padding:3px 10px; border-radius:20px;
            font-size:0.72rem; font-weight:700; margin-top:4px;
        }
        .live-dot {
            width:7px; height:7px; background:var(--green);
            border-radius:50%; animation:pulse 1.5s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ─── Layout ─── */
        .content {
            flex:1; display:grid;
            grid-template-columns:1fr 360px;
            overflow:hidden;
        }

        /* ─── Left Panel ─── */
        .left-panel {
            padding:40px; display:flex; flex-direction:column;
            align-items:center; justify-content:center; text-align:center;
            background:var(--navy2); border-right:1px solid var(--border);
            position:relative; overflow:hidden;
        }
        .left-panel::before {
            content:''; position:absolute;
            width:600px; height:600px; border-radius:50%;
            background:radial-gradient(circle,rgba(59,130,246,0.07) 0%,transparent 65%);
            top:50%; left:50%; transform:translate(-50%,-50%);
            pointer-events:none;
        }
        .dipanggil-label {
            font-size:0.75rem; font-weight:700; letter-spacing:3px;
            text-transform:uppercase; color:var(--gray); margin-bottom:20px;
            position:relative; z-index:1;
        }
        .nomor-box {
            background:var(--navy3);
            border:2px solid rgba(59,130,246,0.2);
            border-radius:28px; padding:36px 64px; margin-bottom:24px;
            position:relative; z-index:1;
            transition:transform 0.4s;
        }
        .nomor-besar {
            font-size:9rem; font-weight:900; color:var(--gold);
            line-height:1; letter-spacing:-4px;
        }
        .nama-besar {
            font-size:2.2rem; font-weight:800; letter-spacing:-0.5px;
            margin-bottom:8px; position:relative; z-index:1;
        }
        .poli-besar { color:var(--gray); font-size:1rem; position:relative; z-index:1; }
        .silakan-btn {
            background:var(--gold); color:#1a1a1a;
            padding:14px 36px; border-radius:14px;
            font-weight:800; font-size:1rem; margin-top:28px;
            display:inline-flex; align-items:center; gap:8px;
            position:relative; z-index:1;
        }
        .kosong-state { color:var(--gray); font-size:1.2rem; position:relative; z-index:1; }

        @keyframes highlight {
            0%  { transform:scale(0.95); }
            50% { transform:scale(1.05); }
            100%{ transform:scale(1); }
        }
        .flash { animation:highlight 0.7s ease; }

        /* ─── Right Panel ─── */
        .right-panel { padding:28px; overflow-y:auto; }
        .tunggu-title {
            font-size:0.75rem; font-weight:700; letter-spacing:2px;
            text-transform:uppercase; color:var(--gray);
            margin-bottom:16px; display:flex; align-items:center; gap:8px;
        }
        .tunggu-count {
            background:var(--accent); color:white;
            font-size:0.7rem; font-weight:700;
            padding:2px 8px; border-radius:10px;
        }
        .antrian-item {
            display:flex; align-items:center; gap:14px;
            padding:14px 4px; border-bottom:1px solid var(--border);
            transition:background 0.2s;
        }
        .antrian-nomor { font-size:1.35rem; font-weight:800; color:var(--accent); min-width:56px; }
        .antrian-nama  { font-size:0.9rem; font-weight:600; }
        .antrian-poli  { font-size:0.75rem; color:var(--gray); margin-top:2px; }
        .empty-list    { color:var(--gray); font-size:0.85rem; text-align:center; padding:40px 0; }

        /* ─── Overlay aktivasi audio ─── */
        #audioOverlay {
            position:fixed; inset:0;
            background:rgba(10,22,40,0.96);
            display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            z-index:1000; cursor:pointer; text-align:center; gap:12px;
        }
        #audioOverlay .ov-icon { font-size:3.5rem; }
        #audioOverlay h2 { font-size:1.6rem; font-weight:800; }
        #audioOverlay p  { color:var(--gray); font-size:0.9rem; max-width:360px; line-height:1.6; }
        .start-btn {
            background:var(--accent); color:white;
            padding:15px 40px; border-radius:14px;
            font-weight:700; font-size:1rem; margin-top:12px;
            cursor:pointer; border:none;
            font-family:'Plus Jakarta Sans',sans-serif;
            transition:background 0.2s;
        }
        .start-btn:hover { background:#2563eb; }
    </style>
</head>
<body>

<div id="audioOverlay">
    <div class="ov-icon">🔊</div>
    <h2>Papan Antrian RS Digital</h2>
    <p>Klik tombol di bawah untuk mengaktifkan tampilan dan notifikasi suara otomatis</p>
    <button class="start-btn" onclick="activateAudio()">▶ Mulai & Aktifkan Suara</button>
</div>

<!-- Audio dingdong -->
<audio id="audioNotif" preload="auto">
    <source src="{{ asset('sounds/freesound_community-news-ting-6832.mp3') }}" type="audio/mpeg">
</audio>

<!-- Header -->
<div class="header">
    <div>
        <div class="brand-name">RS Digital</div>
        <div class="brand-sub">Sistem Antrian Digital</div>
    </div>
    <div>
        <div class="clock" id="clock">--:--:--</div>
        <div class="date-str" id="dateStr">--</div>
        <div style="text-align:right;">
            <span class="live-badge">
                <span class="live-dot"></span>
                <span id="liveText">Menghubungkan...</span>
            </span>
        </div>
    </div>
</div>

<!-- Content -->
<div class="content">

    <!-- Kiri: nomor dipanggil -->
    <div class="left-panel">
        <div class="dipanggil-label">Nomor Dipanggil</div>
        <div id="mainDisplay">
            <div class="kosong-state">Belum ada yang dipanggil</div>
        </div>
    </div>

    <!-- Kanan: daftar menunggu -->
    <div class="right-panel">
        <div class="tunggu-title">
            ⏳ Antrian Menunggu
            <span class="tunggu-count" id="tungguCount">0</span>
        </div>
        <div id="tungguList">
            <div class="empty-list">Tidak ada antrian menunggu</div>
        </div>
    </div>

</div>

<script>
// ─── Clock ───
(function tickClock() {
    const now  = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('dateStr').textContent = now.toLocaleDateString('id-ID', {
        weekday:'long', day:'numeric', month:'long', year:'numeric'
    });
    setTimeout(tickClock, 1000);
})();

// ─── State ───
let audioEnabled = false;
let lastNomor    = null;

// ─── Aktivasi audio (user gesture required) ─── ..
function activateAudio() {
    document.getElementById('audioOverlay').style.display = 'none';
    audioEnabled = true;
    // Unlock audio context dengan silent play
    const audio = document.getElementById('audioNotif');
    audio.volume = 0;
    audio.play()
        .then(() => { audio.pause(); audio.currentTime = 0; audio.volume = 1; })
        .catch(() => {});
}

// ─── SSE ────
const source = new EventSource('{{ route("sse.antrian") }}');

source.addEventListener('queue-update', (e) => {
    const data = JSON.parse(e.data);
    document.getElementById('liveText').textContent = 'Live · ' + data.timestamp;
    renderDipanggil(data.dipanggil);
    renderTunggu(data.menunggu);
});

source.onerror = () => {
    document.getElementById('liveText').textContent = 'Reconnecting...';
};

// ─── Cleanup SSE saat navigasi atau halaman ditutup ───
window.addEventListener('beforeunload', () => {
    if (source) source.close();
});

// ─── Render dipanggil ──── ..
function renderDipanggil(d) {
    const el = document.getElementById('mainDisplay');

    if (!d) {
        el.innerHTML = `<div class="kosong-state">Belum ada yang dipanggil</div>`;
        lastNomor = null;
        return;
    }

    const isNew = d.nomor_antrian !== lastNomor;
    lastNomor   = d.nomor_antrian;

    el.innerHTML = `
        <div class="nomor-box${isNew ? ' flash' : ''}">
            <div class="nomor-besar">${d.nomor_antrian}</div>
        </div>
        <div class="nama-besar">${d.nama}</div>
        <div class="poli-besar">${d.poli}</div>
        <div class="silakan-btn">📢 Silakan Menuju Poli</div>
    `;

    if (isNew && audioEnabled) {
        bunyikanPanggilan(d.nomor_antrian, d.nama, d.poli);
    }
}

// ─── Render daftar tunggu ────
function renderTunggu(list) {
    const el    = document.getElementById('tungguList');
    const count = document.getElementById('tungguCount');
    count.textContent = list ? list.length : 0;

    if (!list || !list.length) {
        el.innerHTML = `<div class="empty-list">Tidak ada antrian menunggu</div>`;
        return;
    }

    el.innerHTML = list.map(a => `
        <div class="antrian-item">
            <div class="antrian-nomor">${a.nomor_antrian}</div>
            <div>
                <div class="antrian-nama">${a.nama}</div>
                <div class="antrian-poli">${a.poli}</div>
            </div>
        </div>
    `).join('');
}

// ─── Suara: dingdong + Web Speech API ─── ..
function bunyikanPanggilan(nomor, nama, poli) {
    if (!audioEnabled) return;

    const audio = document.getElementById('audioNotif');
    window.speechSynthesis.cancel();

    const nomorKata = nomor.split('').join(' ');
    const pesan = new SpeechSynthesisUtterance(
        `Nomor antrian ${nomorKata}. ${nama}, silakan menuju ${poli}.`
    );
    pesan.lang   = 'id-ID';
    pesan.rate   = 0.88;
    pesan.pitch  = 1.0;
    pesan.volume = 1.0;

    const voices = window.speechSynthesis.getVoices();
    const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang.startsWith('id'));
    if (idVoice) pesan.voice = idVoice;

    audio.currentTime = 0;
    const playPromise = audio.play();
    if (playPromise !== undefined) {
        playPromise
            .then(() => { audio.onended = () => window.speechSynthesis.speak(pesan); })
            .catch(() => { window.speechSynthesis.speak(pesan); });
    }
}

window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
</script>
</body>
</html>