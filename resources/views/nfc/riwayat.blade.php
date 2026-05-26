@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; padding: 20px 28px;
        border-radius: 0 0 20px 20px; margin-bottom: 28px;
    }
    .page-header h4 { font-weight: 800; margin-bottom: 2px; }

    /* Filter bar */
    .filter-bar {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        padding: 16px 20px; margin-bottom: 20px;
        display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 6px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #64748b; }
    .filter-group input, .filter-group select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0;
        border-radius: 10px; font-size: 0.875rem;
        outline: none; background: #f8fafc; color: #1e293b;
        transition: border-color 0.2s;
    }
    .filter-group input:focus, .filter-group select:focus { border-color: #2563eb; background: #fff; }
    .btn-filter {
        padding: 10px 20px; background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; border: none; border-radius: 10px;
        font-weight: 700; font-size: 0.875rem; cursor: pointer;
    }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
    @media(max-width:500px){ .stats-row{ grid-template-columns:1fr; } }
    .stat-card {
        background: #fff; border-radius: 14px; padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon { font-size: 1.6rem; }
    .stat-val  { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-lbl  { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
    .stat-blue  .stat-val { color: #2563eb; }
    .stat-green .stat-val { color: #16a34a; }
    .stat-gold  .stat-val { color: #d97706; }

    /* Table */
    .table-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9; overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
    td { padding: 13px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.875rem; color: #1e293b; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fafc; }

    .badge {
        display: inline-flex; padding: 3px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700;
    }
    .badge-hadir     { background: #dcfce7; color: #16a34a; }
    .badge-terlambat { background: #fef3c7; color: #d97706; }

    .serial-code {
        font-family: monospace; font-size: 0.78rem;
        background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569;
    }

    .btn-hapus {
        padding: 5px 12px; background: #fee2e2; color: #dc2626;
        border: none; border-radius: 6px; font-size: 0.75rem;
        font-weight: 700; cursor: pointer;
    }
    .btn-hapus:hover { background: #fecaca; }

    #toast {
        position: fixed; bottom: 24px; right: 24px;
        background: #16a34a; color: white;
        padding: 12px 20px; border-radius: 10px;
        font-size: 0.875rem; font-weight: 600;
        opacity: 0; transform: translateY(10px);
        transition: all 0.3s; z-index: 9999;
    }
    #toast.show { opacity: 1; transform: translateY(0); }
    #toast.error { background: #dc2626; }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Page Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Riwayat Absensi NFC</h4>
            <small class="opacity-75">Data kehadiran mahasiswa via scan kartu NFC</small>
        </div>
        <a href="{{ route('nfc.scanner') }}" target="_blank"
           style="background:rgba(255,255,255,0.15);color:white;padding:8px 16px;border-radius:10px;text-decoration:none;font-weight:600;font-size:0.85rem;">
            Buka Scanner
        </a>
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Filter --}}
    <form method="GET" action="{{ route('nfc.riwayat') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal"
                    value="{{ request('tanggal', today()->format('Y-m-d')) }}">
            </div>
            <div class="filter-group">
                <label>Mata Kuliah</label>
                <select name="mata_kuliah">
                    <option value="">Semua</option>
                    @foreach($mataKuliahs as $mk)
                        <option value="{{ $mk }}" {{ request('mata_kuliah') == $mk ? 'selected' : '' }}>
                            {{ $mk }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </div>
    </form>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card stat-blue">
            <div><div class="stat-val">{{ $stats['total'] }}</div><div class="stat-lbl">Total Hadir</div></div>
        </div>
        <div class="stat-card stat-green">
            <div><div class="stat-val">{{ $stats['hadir'] }}</div><div class="stat-lbl">Tepat Waktu</div></div>
        </div>
        <div class="stat-card stat-gold">
            <div><div class="stat-val">{{ $stats['terlambat'] }}</div><div class="stat-lbl">Terlambat</div></div>
        </div>
    </div>

    {{-- Tabel riwayat --}}
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Waktu Scan</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Mata Kuliah</th>
                    <th>Serial Kartu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $a)
                <tr id="absen-{{ $a->id }}">
                    <td style="font-variant-numeric:tabular-nums;">
                        {{ $a->waktu_scan->format('H:i:s') }}
                    </td>
                    <td style="font-weight:600;">{{ $a->mahasiswa->nama ?? '-' }}</td>
                    <td style="color:#64748b;">{{ $a->mahasiswa->nim ?? '-' }}</td>
                    <td>{{ $a->mata_kuliah ?? '-' }}</td>
                    <td><span class="serial-code">{{ $a->serial_number }}</span></td>
                    <td>
                        <span class="badge badge-{{ $a->status }}">
                            {{ strtoupper($a->status) }}
                        </span>
                    </td>
                    <td>
                        <button class="btn-hapus" onclick="hapusAbsensi({{ $a->id }})">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                        Tidak ada data absensi untuk filter ini
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

async function hapusAbsensi(id) {
    if (!confirm('Hapus data absensi ini?')) return;
    const res  = await fetch(`/nfc/absensi/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    showToast(data.message);
    document.getElementById('absen-' + id)?.remove();
}

function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'show' + (isError ? ' error' : '');
    setTimeout(() => t.className = '', 3000);
}
</script>
@endsection