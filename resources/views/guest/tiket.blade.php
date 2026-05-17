@extends('layouts.app')

@section('content')
<style>
    .antrian-wrap {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .antrian-header { text-align: center; margin-bottom: 28px; }
    .antrian-header h1 { color: #1e3a5f; font-size: 2rem; font-weight: 800; }
    .antrian-header p  { color: #64748b; font-size: 0.9rem; margin-top: 4px; }

    .tiket-card {
        background: #fff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 36px;
        width: 100%;
        max-width: 460px;
        text-align: center;
    }
    .success-icon {
        width: 56px; height: 56px;
        background: #dcfce7; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; font-size: 1.6rem;
    }
    .success-title { color: #1e293b; font-size: 1.25rem; font-weight: 700; margin-bottom: 24px; }

    .tiket-box {
        background: #fffbeb;
        border: 2px dashed #f59e0b;
        border-radius: 16px;
        padding: 28px 24px;
        margin-bottom: 24px;
    }
    .tiket-label {
        color: #d97706; font-size: 0.75rem; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px;
    }
    .nomor-antrian {
        color: #1e3a5f; font-size: 5rem; font-weight: 800;
        line-height: 1; letter-spacing: -2px; margin-bottom: 20px;
    }
    .detail-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 8px 0; border-bottom: 1px solid #f1f5f9;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-key   { color: #64748b; font-size: 0.85rem; }
    .detail-value { color: #1e293b; font-size: 0.9rem; font-weight: 600; }

    .info-text { color: #64748b; font-size: 0.82rem; line-height: 1.6; margin-bottom: 24px; }

    .btn-row { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .btn-cetak {
        padding: 11px 22px; border-radius: 10px;
        background: #16a34a; color: #fff; border: none;
        font-size: 0.875rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 6px;
        transition: opacity 0.2s;
    }
    .btn-daftar {
        padding: 11px 22px; border-radius: 10px;
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: #fff; text-decoration: none;
        font-size: 0.875rem; font-weight: 600;
        display: flex; align-items: center; gap: 6px;
        transition: opacity 0.2s;
    }
    .btn-cetak:hover, .btn-daftar:hover { opacity: 0.9; }

    .link-papan {
        display: block; color: #64748b; font-size: 0.82rem;
        margin-top: 14px; text-decoration: none; transition: color 0.2s;
    }
    .link-papan:hover { color: #2563eb; }

    @media print {
        /* Sembunyikan semua elemen layout */
        .navbar, .sidebar, .main-panel, footer,
        .navbar-menu-wrapper, #sidebar,
        .page-body-wrapper > *:not(.main-panel),
        .btn-row, .link-papan,
        .antrian-header {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .main-panel {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .antrian-wrap {
            min-height: unset !important;
            padding: 0 !important;
        }

        .tiket-card {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            padding: 20px !important;
        }

        .tiket-box {
            border: 2px dashed #f59e0b !important;
        }

        .nomor-antrian {
            color: #1e3a5f !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="antrian-wrap">
    <div class="antrian-header">
        <h1>RS Digital</h1>
        <p>Sistem Antrian Digital</p>
    </div>

    <div class="tiket-card">
        <div class="success-icon">✅</div>
        <div class="success-title">Pendaftaran Berhasil!</div>

        <div class="tiket-box">
            <div class="tiket-label">Nomor Antrian Anda</div>
            <div class="nomor-antrian">{{ $antrian->nomor_antrian }}</div>

            <div class="detail-row">
                <span class="detail-key">Nama</span>
                <span class="detail-value">{{ $antrian->nama }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Poli</span>
                <span class="detail-value">{{ $antrian->poli }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-key">Waktu Daftar</span>
                <span class="detail-value">{{ $antrian->jam_daftar->format('H.i.s') }}</span>
            </div>
        </div>

        <p class="info-text">
            Harap menunggu. Nomor akan dipanggil melalui pengeras suara dan papan antrian.
        </p>

        <div class="btn-row">
            <button class="btn-cetak" onclick="window.print()">🖨️ Cetak</button>
            <a href="{{ route('guest.form') }}" class="btn-daftar">➕ Daftar Lagi</a>
        </div>

        <a href="{{ route('papan.index') }}" target="_blank" class="link-papan">
            💬 Lihat Papan Antrian
        </a>
    </div>
</div>
@endsection