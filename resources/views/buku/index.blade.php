@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book"></i>
                </span> Daftar Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Buku
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title d-flex justify-content-between align-items-center">
                            <span>Tabel Buku</span>
                            <a href="{{ route('buku.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus me-2"></i>Tambah Buku
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
                                        <th>Kode</th>
                                        <th>Judul</th>
                                        <th>Pengarang</th>
                                        <th>Kategori</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bukus as $buku)
                                        <tr>
                                            <td>
                                                {{ ($bukus->currentPage() - 1) * $bukus->perPage() + $loop->iteration }}
                                            </td>
                                            <td>
                                                <span class="badge badge-gradient-success">
                                                    {{ $buku->kode }}
                                                </span>
                                            </td>
                                            <td>{{ $buku->judul }}</td>
                                            <td>{{ $buku->pengarang }}</td>
                                            <td>
                                                @if($buku->kategori)
                                                    <span class="badge badge-gradient-info">
                                                        {{ $buku->kategori->nama_kategori }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-gradient-danger">
                                                        Tidak ada kategori
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('buku.edit', $buku) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>

                                                <form action="{{ route('buku.destroy', $buku) }}" 
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
                                            <td colspan="6" class="text-center text-muted py-3">
                                                Belum ada data buku
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($bukus->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bukus->links() }}
                            </div>
                        @endif

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
        background-color: #f3f0ff;
    }

    .badge-gradient-success {
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
                if (!confirm('Yakin ingin menghapus buku ini?')) {
                    e.preventDefault();
                }
            });
        });

        console.log('Halaman Buku aktif');
    });
</script>
@endpush
