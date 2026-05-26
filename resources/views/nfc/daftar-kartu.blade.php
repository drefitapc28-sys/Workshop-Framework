@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; padding: 20px 28px;
        border-radius: 0 0 20px 20px; margin-bottom: 28px;
    }
    .page-header h4 { font-weight: 800; margin-bottom: 2px; }

    .section-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        margin-bottom: 24px; overflow: hidden;
    }
    .section-head {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 700; font-size: 0.9rem; color: #1e293b;
        display: flex; align-items: center; gap: 8px;
    }

    /* Form tambah mahasiswa */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; padding: 20px; }
    @media(max-width:600px){ .form-grid{ grid-template-columns:1fr; } }
    .form-group label { display:block; font-size:0.8rem; font-weight:600; color:#64748b; margin-bottom:6px; }
    .form-group input, .form-group select {
        width:100%; padding:10px 14px;
        border:1.5px solid #e2e8f0; border-radius:10px;
        font-size:0.9rem; outline:none; transition:border-color 0.2s;
        background:#f8fafc; color:#1e293b;
    }
    .form-group input:focus, .form-group select:focus { border-color:#2563eb; background:#fff; }
    .form-group.full { grid-column: 1 / -1; }

    .btn-primary {
        padding: 10px 22px; background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; border: none; border-radius: 10px;
        font-weight: 700; font-size: 0.875rem; cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-primary:hover { opacity: 0.9; }

    .btn-danger {
        padding: 6px 14px; background: #fee2e2;
        color: #dc2626; border: none; border-radius: 8px;
        font-size: 0.78rem; font-weight: 700; cursor: pointer;
        transition: background 0.2s;
    }
    .btn-danger:hover { background: #fecaca; }

    .btn-toggle {
        padding: 6px 14px; border: none; border-radius: 8px;
        font-size: 0.78rem; font-weight: 700; cursor: pointer;
        transition: background 0.2s;
    }
    .btn-toggle.aktif    { background: #dcfce7; color: #16a34a; }
    .btn-toggle.nonaktif { background: #f1f5f9; color: #64748b; }

    /* NFC scan for registration */
    .nfc-scan-box {
        background: #eff6ff; border: 2px dashed #bfdbfe;
        border-radius: 12px; padding: 16px;
        text-align: center; cursor: pointer;
        transition: background 0.2s;
    }
    .nfc-scan-box:hover { background: #dbeafe; }
    .nfc-scan-box.scanning { background: #dbeafe; border-color: #2563eb; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }
    .nfc-scan-box p { font-size: 0.85rem; color: #2563eb; font-weight: 600; margin-top: 6px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
    td { padding: 14px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.875rem; color: #1e293b; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }

    .badge-aktif    { background: #dcfce7; color: #16a34a; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }

    .serial-code {
        font-family: monospace; font-size: 0.82rem;
        background: #f1f5f9; padding: 3px 8px; border-radius: 6px;
        color: #475569;
    }

    /* Toast */
    #toast {
        position: fixed; bottom: 24px; right: 24px;
        background: #16a34a; color: white;
        padding: 12px 20px; border-radius: 10px;
        font-size: 0.875rem; font-weight: 600;
        opacity: 0; transform: translateY(10px);
        transition: all 0.3s; z-index: 9999;
    }
    #toast.show  { opacity: 1; transform: translateY(0); }
    #toast.error { background: #dc2626; }

    .action-btns { display: flex; gap: 6px; }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Page Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Manajemen Kartu NFC</h4>
            <small class="opacity-75">Daftarkan kartu NFC mahasiswa untuk sistem absensi</small>
        </div>
        <a href="{{ route('nfc.scanner') }}" target="_blank"
           style="background:rgba(255,255,255,0.15);color:white;padding:8px 16px;border-radius:10px;text-decoration:none;font-weight:600;font-size:0.85rem;">
            Buka Scanner
        </a>
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Form Tambah Mahasiswa --}}
    <div class="section-card">
        <div class="section-head">Tambah Mahasiswa Baru</div>
        <div class="form-grid">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" id="nimInput" placeholder="cth: 2024001001">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" id="namaInput" placeholder="Nama mahasiswa">
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" id="prodiInput" placeholder="cth: Teknik Informatika">
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <input type="text" id="kelasInput" placeholder="cth: TI-3A">
            </div>
            <div class="form-group full" style="padding:0 0 4px;">
                <button class="btn-primary" onclick="simpanMahasiswa()">Tambah Mahasiswa</button>
            </div>
        </div>
    </div>

    {{-- Form Daftarkan Kartu NFC --}}
    <div class="section-card">
        <div class="section-head">Daftarkan Kartu NFC</div>
        <div style="padding:20px;">
            <div class="form-grid" style="padding:0;margin-bottom:16px;">
                <div class="form-group">
                    <label>Pilih Mahasiswa</label>
                    <select id="mahasiswaSelect">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($mahasiswas as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->nim }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Label Kartu (opsional)</label>
                    <input type="text" id="labelInput" placeholder="cth: Kartu Utama">
                </div>
            </div>

            {{-- Scan atau input manual --}}
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:0.8rem;font-weight:600;color:#64748b;margin-bottom:8px;">
                    Serial Number Kartu NFC
                </label>
                <div>
                    <input type="text" id="serialInput"
                        placeholder="Ketik serial number kartu NFC"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.9rem;outline:none;background:#f8fafc;font-family:monospace;">
                    <small style="color:#64748b;font-size:0.78rem;margin-top:6px;display:block;">
                        Dapatkan serial number dengan scan kartu di halaman Scanner NFC (HP Android)
                    </small>
                </div>
                <div id="scanStatus" style="font-size:0.8rem;color:#2563eb;margin-top:6px;display:none;">
                    Scanner aktif — dekatkan kartu NFC ke HP...
                </div>
            </div>

            <button class="btn-primary" onclick="daftarkanKartu()">Daftarkan Kartu</button>
        </div>
    </div>

    {{-- Daftar Kartu --}}
    <div class="section-card">
        <div class="section-head">Daftar Kartu Terdaftar</div>
        <table>
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Serial Number</th>
                    <th>Label</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kartuTable">
                @forelse($kartuList as $k)
                <tr id="row-{{ $k->id }}">
                    <td style="font-weight:600;">{{ $k->mahasiswa->nama ?? '-' }}</td>
                    <td style="color:#64748b;">{{ $k->mahasiswa->nim ?? '-' }}</td>
                    <td><span class="serial-code">{{ $k->serial_number }}</span></td>
                    <td>{{ $k->label ?? '-' }}</td>
                    <td>
                        <span class="badge-{{ $k->aktif ? 'aktif' : 'nonaktif' }}" id="badge-{{ $k->id }}">
                            {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-toggle {{ $k->aktif ? 'aktif' : 'nonaktif' }}"
                                id="toggle-{{ $k->id }}"
                                onclick="toggleKartu({{ $k->id }})">
                                {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                            <button class="btn-danger" onclick="hapusKartu({{ $k->id }})">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        Belum ada kartu terdaftar
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

// Simpan mahasiswa
async function simpanMahasiswa() {
    const body = {
        nim:   document.getElementById('nimInput').value,
        nama:  document.getElementById('namaInput').value,
        prodi: document.getElementById('prodiInput').value,
        kelas: document.getElementById('kelasInput').value,
    };
    if (!body.nim || !body.nama) { showToast('NIM dan Nama wajib diisi', true); return; }

    const res  = await post('{{ route("nfc.mahasiswa.simpan") }}', body);
    if (res.mahasiswa) {
        showToast('Mahasiswa berhasil ditambahkan');
        // Tambah ke dropdown
        const opt = document.createElement('option');
        opt.value = res.mahasiswa.id;
        opt.text  = res.mahasiswa.nama + ' (' + res.mahasiswa.nim + ')';
        document.getElementById('mahasiswaSelect').appendChild(opt);
        // Reset form
        ['nimInput','namaInput','prodiInput','kelasInput'].forEach(id => document.getElementById(id).value = '');
    } else {
        showToast(res.message || 'Gagal menambahkan', true);
    }
}

// Scan NFC untuk pendaftaran 
let regReader = null;

async function scanUntukDaftar() {
    if (!('NDEFReader' in window)) {
        showToast('Web NFC tidak didukung di browser/perangkat ini', true);
        return;
    }
    try {
        regReader = new NDEFReader();
        await regReader.scan();

        document.getElementById('scanStatus').style.display = 'block';
        document.getElementById('btnScanReg').disabled = true;

        regReader.addEventListener('reading', ({ serialNumber }) => {
            document.getElementById('serialInput').value = serialNumber.toUpperCase();
            document.getElementById('scanStatus').style.display = 'none';
            document.getElementById('btnScanReg').disabled = false;
            showToast('Serial number berhasil dibaca: ' + serialNumber);
        });
    } catch (err) {
        showToast('Error NFC: ' + err.message, true);
        document.getElementById('scanStatus').style.display = 'none';
        document.getElementById('btnScanReg').disabled = false;
    }
}

// Daftarkan kartu
async function daftarkanKartu() {
    const body = {
        mahasiswa_id:  document.getElementById('mahasiswaSelect').value,
        serial_number: document.getElementById('serialInput').value,
        label:         document.getElementById('labelInput').value,
    };
    if (!body.mahasiswa_id || !body.serial_number) {
        showToast('Pilih mahasiswa dan isi serial number', true);
        return;
    }

    const res = await post('{{ route("nfc.kartu.simpan") }}', body);
    if (res.message && !res.errors) {
        showToast(res.message);
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast(res.message || 'Gagal mendaftarkan kartu', true);
    }
}

// Toggle kartu 
async function toggleKartu(id) {
    const res = await fetch(`/nfc/kartu/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    const badge  = document.getElementById('badge-' + id);
    const toggle = document.getElementById('toggle-' + id);
    if (data.aktif) {
        badge.className  = 'badge-aktif';
        badge.textContent= 'Aktif';
        toggle.className = 'btn-toggle aktif';
        toggle.textContent= '✅ Aktif';
    } else {
        badge.className  = 'badge-nonaktif';
        badge.textContent= 'Nonaktif';
        toggle.className = 'btn-toggle nonaktif';
        toggle.textContent= '⛔ Nonaktif';
    }
    showToast('Status kartu diperbarui');
}

// Hapus kartu
async function hapusKartu(id) {
    if (!confirm('Hapus kartu ini? Data absensi terkait akan ikut terhapus.')) return;
    const res = await fetch(`/nfc/kartu/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    showToast(data.message);
    document.getElementById('row-' + id)?.remove();
}

// Helper 
async function post(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });
    return res.json();
}

function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show' + (isError ? ' error' : '');
    setTimeout(() => t.className = '', 3000);
}
</script>
@endsection