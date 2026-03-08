@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Data Barang</h2>
            <p class="text-muted">Halaman 1 - Tabel HTML Biasa</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('barang.tabel-dt') }}" class="btn btn-outline-primary">
                Versi DataTables →
            </a>
        </div>
    </div>

    {{-- FORM INPUT BARANG (Tugas 2)
         - Nama dan harga adalah required
         - Data TIDAK disimpan ke database
         - Submit menambah row ke tabel --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">➕ Tambah Barang</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" id="inputNama" class="form-control"
                           placeholder="Masukkan nama barang" required>
                    <div class="invalid-feedback">Nama barang wajib diisi.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="inputHarga" class="form-control"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="invalid-feedback">Harga wajib diisi.</div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    {{-- Button di luar form, trigger via JS (Tugas 1) --}}
                    <button type="button" id="btnTambah" class="btn btn-primary w-100">
                        Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL HTML BIASA (Tugas 2) --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">📋 Tabel HTML Biasa</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover" id="tabelBarang">
                <thead class="table-dark">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody id="bodyTabel">
                    <tr id="emptyRow">
                        <td colspan="3" class="text-center py-3 text-muted">
                            Belum ada data. Silakan tambahkan barang.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- TUGAS 3: Modal Edit & Hapus --}}
<div class="modal fade" id="modalBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">✏️ Edit / Hapus Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">ID Barang</label>
                    {{-- TUGAS 3: readonly, tidak bisa diubah --}}
                    <input type="text" id="modalId" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" id="modalNama" class="form-control"
                           placeholder="Masukkan nama barang" required>
                    <div class="invalid-feedback">Nama barang wajib diisi.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="modalHarga" class="form-control"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="invalid-feedback">Harga wajib diisi.</div>
                </div>
            </div>

            <div class="modal-footer">
                {{--
                    TUGAS 3:
                    - Hapus → row terhapus, modal tertutup
                    - Ubah → data row berubah, modal tertutup
                    - Keduanya pakai spinner (Tugas 1)
                --}}
                <button type="button" id="btnHapus" class="btn btn-danger">
                    🗑️ Hapus
                </button>
                <button type="button" id="btnUbah" class="btn btn-primary">
                    💾 Ubah
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let idCounter  = 1;
    let $rowDipilih = null;

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Reset styling validasi saat user mengetik
    $('#inputNama, #inputHarga').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
    });
    $('#modalNama, #modalHarga').on('input', function () {
        $(this).removeClass('is-invalid is-valid');
    });

    // TUGAS 2: Tambah barang ke tabel
    $('#btnTambah').on('click', function () {
        const namaEl  = document.getElementById('inputNama');
        const hargaEl = document.getElementById('inputHarga');
        let valid = true;

        // Cek validitas (Tugas 1)
        [namaEl, hargaEl].forEach(function (el) {
            if (!el.checkValidity() || el.value.trim() === '') {
                el.classList.add('is-invalid');
                el.reportValidity();
                valid = false;
            } else {
                el.classList.remove('is-invalid');
                el.classList.add('is-valid');
            }
        });
        if (!valid) return;

        const $btn  = $(this);

        // Ubah button jadi spinner (Tugas 1)
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menambah...'
        );

        setTimeout(function () {
            const id    = 'BRG-' + String(idCounter).padStart(3, '0');
            const nama  = $('#inputNama').val().trim();
            const harga = parseInt($('#inputHarga').val());

            // Hapus empty row jika masih ada
            $('#emptyRow').remove();

            // Tambah row baru ke tabel
            // TUGAS 3: cursor pointer + data-* untuk modal
            $('#bodyTabel').append(
                `<tr style="cursor: pointer;"
                     data-id="${id}"
                     data-nama="${nama}"
                     data-harga="${harga}">
                    <td><strong>${id}</strong></td>
                    <td>${nama}</td>
                    <td>${formatRupiah(harga)}</td>
                </tr>`
            );

            idCounter++;

            // Reset input setelah submit (Tugas 2)
            $('#inputNama, #inputHarga').val('').removeClass('is-valid is-invalid');
            $btn.prop('disabled', false).html('Tambah');

        }, 700);
    });

    // TUGAS 3: Klik row -> buka modal
    $('#tabelBarang tbody').on('click', 'tr[data-id]', function () {
        $rowDipilih = $(this);

        $('#modalId').val($rowDipilih.data('id'));
        $('#modalNama').val($rowDipilih.data('nama')).removeClass('is-invalid is-valid');
        $('#modalHarga').val($rowDipilih.data('harga')).removeClass('is-invalid is-valid');

        $('#modalBarang').modal('show');
    });

    // TUGAS 3: Tombol Ubah
    $('#btnUbah').on('click', function () {
        const namaEl  = document.getElementById('modalNama');
        const hargaEl = document.getElementById('modalHarga');
        let valid = true;

        // Validasi input modal (Tugas 1)
        [namaEl, hargaEl].forEach(function (el) {
            if (!el.checkValidity() || el.value.trim() === '') {
                el.classList.add('is-invalid');
                el.reportValidity();
                valid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });
        if (!valid) return;

        const $btn  = $(this);
        const nama  = $('#modalNama').val().trim();
        const harga = parseInt($('#modalHarga').val());

        // Ubah button jadi spinner (Tugas 1)
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...'
        );

        setTimeout(function () {
            // Update data-* pada row
            $rowDipilih.data('nama', nama).data('harga', harga);
            // Update tampilan teks di row
            $rowDipilih.find('td:nth-child(2)').text(nama);
            $rowDipilih.find('td:nth-child(3)').text(formatRupiah(harga));

            $btn.prop('disabled', false).html('💾 Ubah');
            $('#modalBarang').modal('hide'); // Tutup modal (Tugas 3)
        }, 600);
    });

    // TUGAS 3: Tombol Hapus
    $('#btnHapus').on('click', function () {
        if (!confirm('Yakin ingin menghapus data ini?')) return;

        const $btn = $(this);

        // Ubah button jadi spinner (Tugas 1)
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menghapus...'
        );

        setTimeout(function () {
            $rowDipilih.remove();

            // Jika tabel kosong, tampilkan kembali empty row
            if ($('#bodyTabel tr').length === 0) {
                $('#bodyTabel').append(
                    '<tr id="emptyRow"><td colspan="3" class="text-center py-3 text-muted">Belum ada data. Silakan tambahkan barang.</td></tr>'
                );
            }

            $btn.prop('disabled', false).html('🗑️ Hapus');
            $('#modalBarang').modal('hide'); // Tutup modal (Tugas 3)
        }, 600);
    });

});
</script>
@endpush