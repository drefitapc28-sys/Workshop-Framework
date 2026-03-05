@extends('layouts.app')

@section('content')
<style>
    .container {
        padding: 20px 0;
    }

    .page-title {
        padding: 20px 0;
        margin-bottom: 20px;
    }

    .page-title h2 {
        margin: 0;
        color: #333;
        font-weight: 600;
        font-size: 24px;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
        font-size: 16px;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 6px;
        font-size: 14px;
    }

    /* Tabel Styling */
    .barang-table {
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .barang-table table {
        margin: 0;
    }

    table th {
        background-color: #f8f9fa;
        color: #333;
        padding: 12px 15px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 14px;
    }

    table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
        font-size: 14px;
    }

    table tbody tr {
        transition: background-color 0.2s;
    }

    table tbody tr:hover {
        background-color: #f8f9fa;
    }

    table tbody tr:last-child td {
        border-bottom: none;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    #selectAllCheckbox {
        cursor: pointer;
    }

    .btn-group {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .btn {
        border-radius: 4px;
        padding: 8px 16px;
        font-weight: 500;
        font-size: 14px;
    }

    .text-muted {
        color: #999 !important;
    }
</style>

<div class="container">
    <div class="page-title">
        <h2>Cetak Label Harga</h2>
    </div>

    <form action="{{ route('barang.cetak') }}" method="POST">
        @csrf

        <!-- Pengaturan Posisi -->
        <div class="form-section">
            <h5>Posisi Label di Kertas</h5>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Kolom Mulai (1-5)</label>
                    <input type="number" name="start_x" min="1" max="5" value="1" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Baris Mulai (1-8)</label>
                    <input type="number" name="start_y" min="1" max="8" value="1" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Tabel Barang -->
        <div class="form-section">
            <h5>Pilih Buku</h5>
            <div class="barang-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th style="width: 70px;">ID</th>
                            <th>Nama Buku</th>
                            <th style="width: 130px;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barang as $b)
                            <tr>
                                <td>
                                    <input class="form-check-input barang-checkbox" type="checkbox" name="barang[]" 
                                           value="{{ $b->id_barang }}">
                                </td>
                                <td><strong>{{ $b->id_barang }}</strong></td>
                                <td>{{ $b->nama }}</td>
                                <td>Rp {{ number_format($b->harga,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                                    Belum ada data buku
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                Cetak PDF
            </button>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox'); // Mengambil elemen checkbox "Select All" dari DOM
        const barangCheckboxes = document.querySelectorAll('.barang-checkbox'); // Mengambil semua checkbox barang dengan kelas "barang-checkbox" dari DOM

        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            barangCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            }); // Ketika checkbox "Select All" berubah, semua checkbox barang akan disesuaikan dengan status "Select All" (checked atau unchecked)
        });

        // Handle individual checkboxes to update select all status
        barangCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(barangCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(barangCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }); // Ketika salah satu checkbox barang berubah, status checkbox "Select All" akan diperbarui. Jika semua checkbox barang dicentang, "Select All" akan dicentang. Jika beberapa dicentang tetapi tidak semua, "Select All" akan berada dalam keadaan indeterminate (tanda minus).
        });
    });
</script>

@endsection