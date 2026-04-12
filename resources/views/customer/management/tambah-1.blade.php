@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h3 mb-4">
                Tambah Customer 1 - BLOB Storage
            </h1>

            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Alert Info -->
                    <div class="alert alert-info mb-4">
                        <i class="mdi mdi-information"></i>
                        <strong>Metode Penyimpanan:</strong> Foto akan disimpan sebagai BLOB langsung di database
                    </div>

                    <!-- Form -->
                    <form id="formCustomer1" onsubmit="submitForm(event)">
                        @csrf

                        <!-- Data Pribadi -->
                        <h5 class="mb-3">
                            <i class="mdi mdi-account"></i> Data Pribadi
                        </h5>

                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" name="provinsi" class="form-control" placeholder="e.g. Jawa Barat" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota <span class="text-danger">*</span></label>
                                <input type="text" name="kota" class="form-control" placeholder="e.g. Bandung" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" name="kecamatan" class="form-control" placeholder="e.g. Bandung Wetan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kodepos <span class="text-danger">*</span></label>
                                <input type="text" name="kodepos_keluarahan" class="form-control" placeholder="e.g. 40100" required>
                            </div>
                        </div>

                        <hr>

                        <!-- Camera Section -->
                        <h5 class="mb-3">
                            <i class="mdi mdi-camera"></i> Ambil Foto
                        </h5>

                        <div class="mb-3">
                            <div class="ratio ratio-16x9 mb-3 border rounded bg-dark">
                                <video id="webcam1" autoplay playsinline></video>
                            </div>

                            <canvas id="canvas1" width="640" height="480" style="display:none;"></canvas>

                            <div class="btn-group d-flex gap-2 mb-3">
                                <button type="button" id="startCamera1" class="btn btn-primary flex-grow-1">
                                    <i class="mdi mdi-play"></i> Mulai Kamera
                                </button>
                                <button type="button" id="capturePhoto1" class="btn btn-success flex-grow-1">
                                    <i class="mdi mdi-camera"></i> Ambil Foto
                                </button>
                                <button type="button" id="stopCamera1" class="btn btn-danger flex-grow-1">
                                    <i class="mdi mdi-stop"></i> Stop Kamera
                                </button>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div id="previewSection" style="display:none;" class="mb-3">
                            <label class="form-label">Preview Foto</label>
                            <div class="text-center">
                                <img id="photoPreview1" class="img-thumbnail rounded" style="max-width: 100%; max-height: 300px;">
                            </div>
                            <small class="text-success">
                                <i class="mdi mdi-check-circle"></i> Foto berhasil ditangkap
                            </small>
                        </div>

                        <!-- Hidden input untuk base64 -->
                        <input type="hidden" name="foto_blob" id="fotoBlobInput1">

                        <hr>

                        <!-- Submit Section -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-lg btn-primary" id="submitBtn">
                                <i class="mdi mdi-content-save"></i> Simpan Customer (BLOB)
                            </button>
                        </div>

                        <a href="{{ route('customer.management.index') }}" class="btn btn-secondary w-100">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4">
                <i class="mdi mdi-lightbulb"></i>
                <strong>Tips:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan kamera sudah diizinkan mengakses device Anda</li>
                    <li>Klik "Mulai Kamera" terlebih dahulu sebelum mengambil foto</li>
                    <li>Posisikan wajah di depan kamera dan klik "Ambil Foto"</li>
                    <li>Preview akan muncul jika foto berhasil ditangkap</li>
                    <li>Isi semua data pribadi sebelum submit</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" style="display:none;" class="position-fixed top-50 start-50 translate-middle">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Menyimpan...</span>
    </div>
</div>

<script src="{{ asset('js/camera-blob.js') }}"></script>

<script>
async function submitForm(event) {
    event.preventDefault();

    const fotoBlobInput = document.getElementById('fotoBlobInput1').value;
    if (!fotoBlobInput) {
        alert('Foto belum diambil! Silakan klik "Ambil Foto" terlebih dahulu.');
        return;
    }

    const formData = new FormData(document.getElementById('formCustomer1'));

    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('submitBtn').disabled = true;

    try {
        const response = await fetch('{{ route("customer.management.store1") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        // Debug: log response status
        console.log('Response Status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, errorText);
            throw new Error('HTTP Error: ' + response.status);
        }

        const data = await response.json();
        console.log('Response Data:', data);
        
        document.getElementById('loadingSpinner').style.display = 'none';

        if (data.success) {
            alert('✅ ' + data.message);
            window.location.href = data.redirect;
        } else {
            alert('❌ ' + (data.message || 'Gagal menyimpan data'));
            document.getElementById('submitBtn').disabled = false;
        }
    } catch (error) {
        console.error('Error Details:', error);
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('submitBtn').disabled = false;
        alert('❌ Terjadi kesalahan:\n' + error.message + '\n\nLihat browser console (F12) untuk detail lengkap.');
    }
}
</script>
@endsection
