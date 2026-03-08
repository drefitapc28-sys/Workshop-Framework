@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Buku Baru</h5>
                </div>
                <div class="card-body">

                    {{-- Form: id ditambahkan, tanpa button submit di dalamnya --}}
                    <form id="formBarang" action="{{ route('barang.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Buku</label>
                            <input type="text"
                                class="form-control @error('nama') is-invalid @enderror"
                                name="nama"
                                placeholder="Masukkan nama buku"
                                required> 
                            @error('nama') 
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                    class="form-control @error('harga') is-invalid @enderror"
                                    name="harga"
                                    placeholder="0"
                                    min="0"
                                    required>
                            </div>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </form>
                    {{-- Form ditutup di sini, sebelum button --}}

                    {{-- Button di luar form, trigger submit via JS --}}
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="button" id="btnSimpan" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () { 

    $('#btnSimpan').on('click', function () {
        const form = document.getElementById('formBarang');

        // Step 1: Cek semua input required menggunakan HTML5 checkValidity
        if (!form.checkValidity()) {
            // Step 2: Tampilkan pesan error bawaan browser pada field yang kosong
            form.reportValidity();
            return;
        }

        const $btn = $(this);

        // Step 3: Ubah button menjadi spinner (mencegah double submit)
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...'
        );

        // Step 4: Submit form via JavaScript
        form.submit();
    });

});
//catatan:
//Atribut required — menandai field yang wajib diisi sebelum form disubmit
</script>
@endpush