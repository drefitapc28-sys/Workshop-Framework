@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-12">
            <h2>Pilih Kota</h2>
            <p class="text-muted">Tugas 4 - Select & Select2</p>
        </div>
    </div>

    <div class="row g-4">

        {{-- CARD 1: SELECT HTML BIASA --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select</h5>
                </div>
                <div class="card-body">

                    {{-- Input tambah kota --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tambah Kota</label>
                        <div class="input-group">
                            <input type="text" id="inputKota1" class="form-control"
                                   placeholder="Nama kota...">
                            <button type="button" id="btnTambahKota1" class="btn btn-primary">
                                Tambah
                            </button>
                        </div>
                        <div id="errorKota1" class="text-danger small mt-1 d-none">
                            Nama kota tidak boleh kosong.
                        </div>
                    </div>

                    {{-- Element select biasa --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Form Select Kota</label>
                        <select id="selectKota1" class="form-select">
                            <option value="">-- Pilih Kota --</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Bandung">Bandung</option>
                        </select>
                    </div>

                    {{-- Kota terpilih --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kota Terpilih</label>
                        <div id="kotaTerpilih1"
                             class="form-control bg-light text-muted"
                             style="min-height: 42px;">
                            Belum ada kota dipilih
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- CARD 2: SELECT2 --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select 2</h5>
                </div>
                <div class="card-body">

                    {{-- Input tambah kota --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tambah Kota</label>
                        <div class="input-group">
                            <input type="text" id="inputKota2" class="form-control"
                                   placeholder="Nama kota...">
                            <button type="button" id="btnTambahKota2" class="btn btn-primary">
                                Tambah
                            </button>
                        </div>
                        <div id="errorKota2" class="text-danger small mt-1 d-none">
                            Nama kota tidak boleh kosong.
                        </div>
                    </div>

                    {{-- Element select2 --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Form Select Kota</label>
                        <select id="selectKota2" class="form-select" style="width: 100%;">
                            <option value="">-- Pilih Kota --</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Bandung">Bandung</option>
                        </select>
                    </div>

                    {{-- Kota terpilih --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kota Terpilih</label>
                        <div id="kotaTerpilih2"
                             class="form-control bg-light text-muted"
                             style="min-height: 42px;">
                            Belum ada kota dipilih
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
{{-- CSS Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"> 
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@push('scripts')
{{-- JS Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    // Inisialisasi Select2 pada Card 2
    $('#selectKota2').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Kota --',
        allowClear: true,
        width: '100%'
    });

    // CARD 1: Tambah kota ke select biasa
    $('#btnTambahKota1').on('click', function () {
        const kota = $('#inputKota1').val().trim();

        // Validasi input tidak boleh kosong
        if (kota === '') {
            $('#errorKota1').removeClass('d-none');
            return;
        }
        $('#errorKota1').addClass('d-none');

        // Cek duplikat
        let duplikat = false;
        $('#selectKota1 option').each(function () {
            if ($(this).val() === kota) { duplikat = true; }
        });
        if (duplikat) {
            alert('Kota "' + kota + '" sudah ada dalam daftar.');
            return;
        }

        // Tambahkan option baru ke select
        // Nama kota sebagai value dan teks tampil (sesuai ketentuan soal)
        $('#selectKota1').append(
            `<option value="${kota}">${kota}</option>`
        );

        $('#inputKota1').val(''); // kosongkan input
    });

    // Sembunyikan error saat user mengetik
    $('#inputKota1').on('input', function () {
        $('#errorKota1').addClass('d-none');
    });

    // CARD 1: onchange select biasa → tampilkan kota terpilih
    $('#selectKota1').on('change', function () {
        const val = $(this).val();

        if (val) {
            $('#kotaTerpilih1')
                .removeClass('text-muted')
                .addClass('text-dark fw-bold')
                .text(val);
        } else {
            $('#kotaTerpilih1')
                .removeClass('text-dark fw-bold')
                .addClass('text-muted')
                .text('Belum ada kota dipilih');
        }
    });

    // CARD 2: Tambah kota ke Select2
    $('#btnTambahKota2').on('click', function () {
        const kota = $('#inputKota2').val().trim();

        // Validasi input tidak boleh kosong
        if (kota === '') {
            $('#errorKota2').removeClass('d-none');
            return;
        }
        $('#errorKota2').addClass('d-none');

        // Cek duplikat
        let duplikat = false;
        $('#selectKota2 option').each(function () {
            if ($(this).val() === kota) { duplikat = true; }
        });
        if (duplikat) {
            alert('Kota "' + kota + '" sudah ada dalam daftar.');
            return;
        }

        // Tambahkan option baru ke select2
        // Nama kota sebagai value dan teks tampil (sesuai ketentuan soal)
        const newOption = new Option(kota, kota, false, false);
        $('#selectKota2').append(newOption).trigger('change');

        $('#inputKota2').val(''); // kosongkan input
    });

    // Sembunyikan error saat user mengetik
    $('#inputKota2').on('input', function () {
        $('#errorKota2').addClass('d-none');
    });

    // CARD 2: onchange Select2 → tampilkan kota terpilih
    $('#selectKota2').on('change', function () {
        const val = $(this).val();

        if (val) {
            $('#kotaTerpilih2')
                .removeClass('text-muted')
                .addClass('text-dark fw-bold')
                .text(val);
        } else {
            $('#kotaTerpilih2')
                .removeClass('text-dark fw-bold')
                .addClass('text-muted')
                .text('Belum ada kota dipilih');
        }
    });

});
</script>
@endpush