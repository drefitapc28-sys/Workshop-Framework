@extends('layouts.app')

@section('content')
<style>
    /* ─── Page Header ─── */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; padding: 20px 28px;
        border-radius: 0 0 20px 20px; margin-bottom: 28px;
    }
    .page-header h4 { font-weight: 800; margin-bottom: 2px; }

    /* ─── SSE Status ─── */
    .sse-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.78rem; font-weight: 600; color: white;
    }
    .sse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #94a3b8; transition: background 0.3s;
    }
    .sse-dot.live { background: #4ade80; box-shadow: 0 0 6px #4ade80; }

    /* ─── Action Buttons ─── */
    .action-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; padding: 0 4px; }
    .btn-panggil {
        flex: 1; min-width: 200px;
        padding: 14px 24px;
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #1a1a1a; border: none; border-radius: 12px;
        font-size: 1rem; font-weight: 800; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: opacity 0.2s, transform 0.1s;
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }
    .btn-panggil:hover  { opacity: 0.92; }
    .btn-panggil:active { transform: scale(0.98); }

    .btn-reset {
        padding: 13px 20px;
        background: #fff; color: #64748b;
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        font-weight: 600; font-size: 0.9rem; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: border-color 0.2s, color 0.2s;
    }
    .btn-reset:hover { border-color: #ef4444; color: #ef4444; }

    .btn-papan {
        padding: 13px 20px;
        background: #eff6ff; color: #2563eb;
        border: 1px solid #bfdbfe; border-radius: 12px;
        font-weight: 600; font-size: 0.9rem; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        text-decoration: none; transition: background 0.2s;
    }
    .btn-papan:hover { background: #dbeafe; color: #1d4ed8; }

    /* ─── Dipanggil Card ─── */
    .dipanggil-card {
        background: #fffbeb;
        border: 2px solid #fcd34d;
        border-radius: 16px; padding: 22px 28px;
        margin-bottom: 24px;
        display: flex; align-items: center; gap: 20px; min-height: 90px;
        box-shadow: 0 2px 12px rgba(245,158,11,0.1);
    }
    .dipanggil-nomor { font-size: 3rem; font-weight: 800; color: #d97706; line-height: 1; min-width: 90px; }
    .dipanggil-nama  { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
    .dipanggil-poli  { color: #64748b; font-size: 0.85rem; margin-top: 2px; }
    .badge-dp {
        background: #fef3c7; color: #d97706;
        font-size: 0.72rem; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
        display: inline-block; margin-top: 6px;
        border: 1px solid #fcd34d;
    }
    .dipanggil-empty { color: #94a3b8; font-size: 0.95rem; }

    /* ─── Stats ─── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4,1fr);
        gap: 14px; margin-bottom: 28px;
    }
    @media(max-width:700px){ .stats-grid{ grid-template-columns: repeat(2,1fr); } }
    .stat-card {
        background: #fff; border-radius: 14px; padding: 18px 20px;
        display: flex; align-items: center; gap: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
    }
    .stat-icon { font-size: 1.6rem; }
    .stat-val  { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-lbl  { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
    .stat-cyan  .stat-val { color: #0891b2; }
    .stat-gold  .stat-val { color: #d97706; }
    .stat-red   .stat-val { color: #dc2626; }
    .stat-green .stat-val { color: #16a34a; }

    /* ─── Section title ─── */
    .section-title {
        font-size: 0.8rem; font-weight: 700; letter-spacing: 1.5px;
        text-transform: uppercase; color: #94a3b8;
        margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
    }
    .count-badge {
        background: #2563eb; color: white;
        font-size: 0.7rem; font-weight: 700;
        padding: 2px 8px; border-radius: 10px;
    }

    /* ─── Table ─── */
    .table-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9; overflow: hidden;
        margin-bottom: 28px;
    }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card th {
        background: #f8fafc; padding: 12px 16px;
        text-align: left; font-size: 0.78rem; font-weight: 700;
        color: #64748b; letter-spacing: 0.5px; text-transform: uppercase;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-card td { padding: 14px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.875rem; color: #1e293b; }
    .table-card tr:last-child td { border-bottom: none; }
    .table-card tr:hover td { background: #f8fafc; }

    /* ─── Status Badges ─── */
    .badge {
        display: inline-flex; align-items: center;
        padding: 4px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700;
    }
    .badge-menunggu  { background: #f1f5f9; color: #64748b; }
    .badge-dipanggil { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
    .badge-terlambat { background: #fee2e2; color: #dc2626; }
    .badge-selesai   { background: #dcfce7; color: #16a34a; }

    /* ─── Table action buttons ─── */
    .tbl-btn {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        cursor: pointer; display: inline-flex; align-items: center;
        justify-content: center; font-size: 0.9rem;
        transition: opacity 0.2s, transform 0.1s;
    }
    .tbl-btn:active { transform: scale(0.9); }
    .tbl-btn:hover  { opacity: 0.75; }
    .tbl-gold  { background: #fef3c7; }
    .tbl-red   { background: #fee2e2; }
    .tbl-green { background: #dcfce7; }
    .tbl-green { background: #dcfce7; }

    /* ─── Toast ─── */
    #toast {
        position: fixed; bottom: 24px; right: 24px;
        background: #16a34a; color: white;
        padding: 12px 20px; border-radius: 10px;
        font-size: 0.875rem; font-weight: 600;
        opacity: 0; transform: translateY(10px);
        transition: all 0.3s; z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    #toast.show  { opacity: 1; transform: translateY(0); }
    #toast.error { background: #dc2626; }
</style>

{{-- Page Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-ticket-confirmation me-2"></i>Admin Antrian</h4>
            <small class="opacity-75">Kelola antrian RS Digital secara real-time</small>
        </div>
        <div class="sse-badge">
            <span class="sse-dot" id="sseDot"></span>
            <span id="sseStatus">Menghubungkan...</span>
        </div>
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Action Buttons --}}
    <div class="action-row">
        <button class="btn-panggil" onclick="panggilBerikutnya()">
            📢 Panggil Berikutnya
        </button>
        <button class="btn-reset" onclick="resetAntrian()">
            🔄 Reset Antrian Hari Ini
        </button>
        <a href="{{ route('papan.index') }}" target="_blank" class="btn-papan">
            💬 Buka Papan Antrian
        </a>
    </div>

    {{-- Sedang Dipanggil --}}
    <div class="section-title" style="margin-bottom:10px;">📢 Sedang Dipanggil</div>
    <div class="dipanggil-card" id="dipanggilCard">
        @if($dipanggil)
            <div class="dipanggil-nomor">{{ $dipanggil->nomor_antrian }}</div>
            <div>
                <div class="dipanggil-nama">{{ $dipanggil->nama }}</div>
                <div class="dipanggil-poli">{{ $dipanggil->poli }}</div>
                <span class="badge-dp">Dipanggil</span>
            </div>
        @else
            <div class="dipanggil-empty">Belum ada nomor yang dipanggil</div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card stat-cyan">
            <span class="stat-icon">⏳</span>
            <div><div class="stat-val" id="statMenunggu">{{ $stats['menunggu'] }}</div><div class="stat-lbl">Menunggu</div></div>
        </div>
        <div class="stat-card stat-gold">
            <span class="stat-icon">📢</span>
            <div><div class="stat-val" id="statDipanggil">{{ $stats['dipanggil'] }}</div><div class="stat-lbl">Dipanggil</div></div>
        </div>
        <div class="stat-card stat-red">
            <span class="stat-icon">⏰</span>
            <div><div class="stat-val" id="statTerlambat">{{ $stats['terlambat'] }}</div><div class="stat-lbl">Terlambat</div></div>
        </div>
        <div class="stat-card stat-green">
            <span class="stat-icon">✅</span>
            <div><div class="stat-val" id="statSelesai">{{ $stats['selesai'] }}</div><div class="stat-lbl">Selesai</div></div>
        </div>
    </div>

    {{-- Daftar Antrian --}}
    <div class="section-title">
        📋 Daftar Antrian Hari Ini
        <span class="count-badge" id="totalBadge">{{ $antrians->count() }}</span> antrian
    </div>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Pasien</th>
                    <th>Poli / Layanan</th>
                    <th>Jam Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($antrians as $a)
                <tr>
                    <td style="font-weight:800;color:#2563eb;">{{ $a->nomor_antrian }}</td>
                    <td>{{ $a->nama }}</td>
                    <td>{{ $a->poli }}</td>
                    <td>{{ $a->jam_daftar->format('H.i') }}</td>
                    <td>
                        @if($a->status==='menunggu')   <span class="badge badge-menunggu">Menunggu</span>
                        @elseif($a->status==='dipanggil') <span class="badge badge-dipanggil">Dipanggil</span>
                        @elseif($a->status==='terlambat') <span class="badge badge-terlambat">Terlambat</span>
                        @else <span class="badge badge-selesai">Selesai</span>
                        @endif
                    </td>
                    <td>
                        @if($a->status==='dipanggil')
                            {{-- Putar ulang suara --}}
                            <button class="tbl-btn tbl-gold" title="Putar Ulang Suara" onclick="putarUlang('{{ $a->nomor_antrian }}','{{ $a->nama }}','{{ $a->poli }}')">🔊</button>
                            {{-- Selesai --}}
                            <button class="tbl-btn tbl-green" title="Selesai" onclick="tandaiSelesai({{ $a->id }})">✅</button>
                            {{-- Terlambat --}}
                            <button class="tbl-btn tbl-red" title="Tandai Terlambat" onclick="tandaiTerlambat({{ $a->id }})">⏰</button>
                        @elseif($a->status==='terlambat')
                            <button class="tbl-btn tbl-gold" title="Panggil Ulang" onclick="panggilTerlambat({{ $a->id }})">📢</button>
                        @elseif($a->status==='menunggu')
                            <button class="tbl-btn tbl-gold" title="Panggil Langsung" onclick="panggilLangsung({{ $a->id }})">📢</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        Belum ada antrian hari ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<div id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;


window.addEventListener('beforeunload', () => {
    if (source) source.close();
});

const source = new EventSource('{{ route("sse.antrian") }}');

source.addEventListener('queue-update', (e) => {
    try {
        const data = JSON.parse(e.data);
        updateDipanggil(data.dipanggil);
        updateStats(data.stats);
        updateTable(data.semua);
        
        // Update status badge
        document.getElementById('sseDot').classList.add('live');
        const now = new Date();
        const jamSekarang = String(now.getHours()).padStart(2,'0') + ':' + 
                           String(now.getMinutes()).padStart(2,'0') + ':' + 
                           String(now.getSeconds()).padStart(2,'0');
        document.getElementById('sseStatus').textContent = 'Live · ' + jamSekarang;
    } catch (err) {
        console.error('SSE Parse Error:', err);
    }
});

source.onerror = (err) => {
    // Browser otomatis reconnect, kita hanya update UI
    document.getElementById('sseDot').classList.remove('live');
    document.getElementById('sseStatus').textContent = 'Menghubungkan...';
};

// ─── Cleanup SSE saat navigasi ───────────────────────────
window.addEventListener('beforeunload', () => {
    if (source) source.close();
});

// ─── UI Updates ──────────────────────────────────────────
function updateDipanggil(d) {
    const el = document.getElementById('dipanggilCard');
    if (d) {
        el.innerHTML = `
            <div class="dipanggil-nomor">${d.nomor_antrian}</div>
            <div>
                <div class="dipanggil-nama">${d.nama}</div>
                <div class="dipanggil-poli">${d.poli}</div>
                <span class="badge-dp">Dipanggil</span>
            </div>`;
    } else {
        el.innerHTML = `<div class="dipanggil-empty">Belum ada nomor yang dipanggil</div>`;
    }
}

function updateStats(s) {
    document.getElementById('statMenunggu').textContent  = s.menunggu;
    document.getElementById('statDipanggil').textContent = s.dipanggil;
    document.getElementById('statTerlambat').textContent  = s.terlambat;
    document.getElementById('statSelesai').textContent   = s.selesai;
}

function updateTable(list) {
    const tbody = document.getElementById('tableBody');
    document.getElementById('totalBadge').textContent = list.length;
    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada antrian hari ini</td></tr>`;
        return;
    }
    tbody.innerHTML = list.map(a => {
        let badge = '', aksi = '';
        if (a.status === 'menunggu') {
            badge = `<span class="badge badge-menunggu">Menunggu</span>`;
            aksi  = `<button class="tbl-btn tbl-gold" title="Panggil Langsung" onclick="panggilLangsung(${a.id})">📢</button>`;
        } else if (a.status === 'dipanggil') {
            badge = `<span class="badge badge-dipanggil">Dipanggil</span>`;
            aksi  = `
                <button class="tbl-btn tbl-gold" title="Putar Ulang" onclick="putarUlang('${a.nomor_antrian}','${a.nama}','${a.poli}')">🔊</button>
                <button class="tbl-btn tbl-green" title="Selesai" onclick="tandaiSelesai(${a.id})">✅</button>
                <button class="tbl-btn tbl-red" title="Terlambat" onclick="tandaiTerlambat(${a.id})">⏰</button>
            `;
        } else if (a.status === 'terlambat') {
            badge = `<span class="badge badge-terlambat">Terlambat</span>`;
            aksi  = `<button class="tbl-btn tbl-gold" title="Double-click panggil ulang" ondblclick="panggilTerlambat(${a.id})">📢</button>`;
        } else {
            badge = `<span class="badge badge-selesai">Selesai</span>`;
        }
        // Format jam dengan benar dari ISO datetime string
        const jam = formatJam(a.jam_daftar);
        return `<tr>
            <td style="font-weight:800;color:#2563eb;">${a.nomor_antrian}</td>
            <td>${a.nama}</td><td>${a.poli}</td><td>${jam}</td>
            <td>${badge}</td><td>${aksi}</td>
        </tr>`;
    }).join('');
}

// Helper function untuk format jam
function formatJam(isoString) {
    if (!isoString) return '-';
    try {
        const date = new Date(isoString);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${hours}.${minutes}`;
    } catch (e) {
        return '-';
    }
}

// ─── Actions ────────────────────────────────────────────
async function post(url, body = {}) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body),
    });
    return res.json();
}

async function panggilBerikutnya() {
    const data = await post('{{ route("antrian.panggil") }}');
    showToast(data.message, !data.antrian);
}
async function panggilLangsung(id) {
    const data = await post('{{ route("antrian.panggilTerlambat") }}', { id });
    showToast(data.message);
}
async function tandaiTerlambat(id) {
    const data = await post('{{ route("antrian.terlambat") }}', { id });
    showToast(data.message);
}
async function panggilTerlambat(id) {
    const data = await post('{{ route("antrian.panggilTerlambat") }}', { id });
    showToast(data.message);
}
async function resetAntrian() {
    if (!confirm('Reset semua antrian hari ini? Tidak bisa dibatalkan.')) return;
    const data = await post('{{ route("antrian.reset") }}');
    showToast(data.message);
}

function putarUlang(nomor, nama, poli) {
    // Putar suara panggilan di tab admin
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const nomorKata = nomor.split('').join(' ');
        const pesan = new SpeechSynthesisUtterance(
            `Nomor antrian ${nomorKata}. ${nama}, silakan menuju ${poli}.`
        );
        pesan.lang = 'id-ID';
        pesan.rate = 0.88;
        window.speechSynthesis.speak(pesan);
    }
    showToast('Memutar ulang suara panggilan');
}

async function tandaiSelesai(id) {
    const data = await post('{{ route("antrian.selesai") }}', { id });
    showToast(data.message);
}

function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show' + (isError ? ' error' : '');
    setTimeout(() => t.className = '', 3000);
}
</script>
@endsection