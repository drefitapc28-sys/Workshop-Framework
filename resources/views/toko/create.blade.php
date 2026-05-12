@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .form-wrap { max-width: 680px; margin: 30px auto; }
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; }
    .form-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .form-card .card-body { padding: 28px; }
    .form-label { font-weight: 600; font-size: .88rem; color: #374151; margin-bottom: 6px; }
    .form-control { border-radius: 10px; border: 2px solid #e2e8f0; padding: 10px 14px; font-size: .9rem; transition: border-color .2s; }
    .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    .btn-save { background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none; border-radius: 10px; padding: 11px 28px; font-weight: 700; }
    .btn-save:hover { opacity: .9; color: white; }
    .btn-cancel { border: 2px solid #e2e8f0; background: white; color: #475569; border-radius: 10px; padding: 9px 20px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }
    .btn-ambil { background: #2563eb; color: white; border: none; border-radius: 10px; padding: 10px 18px; font-weight: 600; font-size: .88rem; }
    .coord-display { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; font-family: monospace; font-size: .9rem; color: #1e293b; }
    .acc-badge { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: .85rem; font-weight: 600; display: inline-block; }
</style>

<div class="form-wrap px-3">
    <div class="page-header">
        <h5 class="mb-1 fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Toko Baru</h5>
        <small class="opacity-75">Isi data toko dan koordinat lokasi</small>
    </div>

    <div class="card form-card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('toko.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                    <input type="text" name="nama_toko" class="form-control"
                           value="{{ old('nama_toko') }}" placeholder="Contoh: Toko Maju Jaya" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"
                              placeholder="Alamat lengkap toko (opsional)">{{ old('alamat') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Koordinat Lokasi Toko <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn-ambil" onclick="ambilLokasi()">
                            <i class="bi bi-geo-alt me-1"></i> Ambil Lokasi Sekarang
                        </button>
                        <span id="lokasiStatus" class="text-muted d-flex align-items-center" style="font-size:.85rem;"></span>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Latitude</label>
                            <input type="number" name="latitude" id="latitude" class="form-control"
                                   value="{{ old('latitude') }}" step="any" placeholder="-7.2575" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Longitude</label>
                            <input type="number" name="longitude" id="longitude" class="form-control"
                                   value="{{ old('longitude') }}" step="any" placeholder="112.7521" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-muted small">Akurasi GPS (meter)</label>
                        <input type="number" name="accuracy" id="accuracy" class="form-control"
                               value="{{ old('accuracy') }}" step="any" placeholder="Otomatis dari GPS" required>
                        <div class="form-text">Nilai akurasi GPS saat mengambil koordinat. Semakin kecil semakin akurat.</div>
                    </div>
                </div>

                <div id="coordPreview" class="mb-4 d-none">
                    <div class="coord-display">
                        <i class="bi bi-geo-fill text-primary me-2"></i>
                        <span id="coordText">-</span>
                        <span class="ms-2" id="accText"></span>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg me-1"></i> Simpan Toko
                    </button>
                    <a href="{{ route('toko.index') }}" class="btn-cancel">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function ambilLokasi() {
    if (!navigator.geolocation) { alert('Browser tidak mendukung Geolocation.'); return; }
    const status = document.getElementById('lokasiStatus');
    status.innerHTML = '<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span>Mengambil lokasi...</span>';
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude, lng = pos.coords.longitude, acc = pos.coords.accuracy;
            document.getElementById('latitude').value  = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('accuracy').value  = Math.round(acc);
            document.getElementById('coordText').textContent = lat.toFixed(7) + ', ' + lng.toFixed(7);
            document.getElementById('accText').innerHTML = '<span class="acc-badge">±' + Math.round(acc) + 'm</span>';
            document.getElementById('coordPreview').classList.remove('d-none');
            status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Lokasi berhasil diambil</span>';
        },
        function(err) {
            let msg = 'Gagal mengambil lokasi.';
            if (err.code === 1) msg = 'Akses lokasi ditolak.';
            status.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>' + msg + '</span>';
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
</script>
@endsection