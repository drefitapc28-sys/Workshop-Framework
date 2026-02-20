@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-bookmark"></i>
                </span> Daftar Kategori
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Kategori
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title d-flex justify-content-between align-items-center">
                            <span>Tabel Kategori</span>
                            <a href="{{ route('kategori.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus me-2"></i>Tambah Kategori
                            </a>
                        </h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Kategori</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kategoris as $kategori)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge badge-gradient-info">
                                                    {{ $kategori->nama_kategori }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('kategori.edit', $kategori) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>

                                                <form action="{{ route('kategori.destroy', $kategori) }}" 
                                                      method="POST" 
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm btn-delete">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                Belum ada data kategori
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- ================= STYLE PAGE ================= --}}
@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f6f3ff;
    }

    .badge-gradient-info {
        font-size: 13px;
        padding: 6px 10px;
    }
</style>
@endpush


{{-- ================= JAVASCRIPT PAGE ================= --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin menghapus data ini?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
