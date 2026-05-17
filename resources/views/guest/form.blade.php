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
    .antrian-header { text-align: center; margin-bottom: 32px; }
    .antrian-header h1 { color: #1e3a5f; font-size: 2rem; font-weight: 800; }
    .antrian-header p  { color: #64748b; font-size: 0.9rem; margin-top: 4px; }

    .antrian-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 40px;
        width: 100%;
        max-width: 480px;
    }
    .antrian-card .card-title   { color: #1e293b; font-size: 1.3rem; font-weight: 700; text-align: center; margin-bottom: 6px; }
    .antrian-card .card-subtitle{ color: #64748b; font-size: 0.85rem; text-align: center; margin-bottom: 28px; }

    .antrian-card label {
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    .antrian-card input[type="text"],
    .antrian-card select {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
        background: #f8fafc;
        color: #1e293b;
        appearance: none;
        -webkit-appearance: none;
    }
    .antrian-card input[type="text"]:focus,
    .antrian-card select:focus {
        border-color: #2563eb;
        background: #fff;
    }
    .antrian-card .select-wrap { position: relative; }
    .antrian-card .select-wrap::after {
        content: '▾'; position: absolute;
        right: 16px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; pointer-events: none;
    }
    .antrian-card .form-group { margin-bottom: 20px; }
    .antrian-card .error-msg  { color: #ef4444; font-size: 0.8rem; margin-top: 6px; }

    .btn-ambil {
        width: 100%; padding: 14px;
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: #fff; border: none; border-radius: 12px;
        font-size: 1rem; font-weight: 700; cursor: pointer;
        transition: opacity 0.2s, transform 0.1s;
        margin-top: 8px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-ambil:hover  { opacity: 0.92; }
    .btn-ambil:active { transform: scale(0.98); }

    .link-papan {
        display: block; text-align: center;
        color: #64748b; font-size: 0.85rem;
        margin-top: 16px; text-decoration: none;
        transition: color 0.2s;
    }
    .link-papan:hover { color: #2563eb; }
</style>

<div class="antrian-wrap">
    <div class="antrian-header">
        <h1>RS Digital</h1>
        <p>Sistem Antrian Digital</p>
    </div>

    <div class="antrian-card">
        <div class="card-title">Ambil Nomor Antrian</div>
        <div class="card-subtitle">Isi data diri Anda untuk mendapatkan nomor antrian</div>

        <form action="{{ route('guest.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>👤 Nama Lengkap</label>
                <input type="text" name="nama"
                    placeholder="Masukkan nama lengkap Anda"
                    value="{{ old('nama') }}" autocomplete="off">
                @error('nama') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Pilih Poli / Layanan</label>
                <div class="select-wrap">
                    <select name="poli">
                        @foreach($daftarPoli as $poli)
                            <option value="{{ $poli }}" {{ old('poli') == $poli ? 'selected' : '' }}>
                                {{ $poli }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('poli') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-ambil">
                Ambil Nomor Antrian
            </button>
        </form>

        <a href="{{ route('papan.index') }}" target="_blank" class="link-papan">
            Lihat Papan Antrian
        </a>
    </div>
</div>
@endsection