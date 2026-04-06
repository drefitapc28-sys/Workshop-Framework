@extends('layouts.app')

@section('content')
<style>
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 20px 28px; border-radius: 0 0 20px 20px; margin-bottom: 28px; }
    .menu-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; transition: transform .2s, box-shadow .2s; }
    .menu-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(0,0,0,0.13); }
    .menu-card .menu-img { height: 160px; object-fit: cover; width: 100%; }
    .menu-card .menu-placeholder { height: 160px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; }
    .menu-card .card-body { padding: 14px 16px; }
    .menu-card .nama { font-weight: 700; font-size: .95rem; color: #1e293b; margin-bottom: 4px; }
    .menu-card .deskripsi { font-size: .82rem; color: #64748b; margin-bottom: 8px; min-height: 36px; }
    .menu-card .harga { font-weight: 800; color: #16a34a; font-size: 1rem; }
    .menu-card .card-footer { background: white; border-top: 1px solid #f1f5f9; padding: 10px 16px; }
    .btn-edit { background: #f59e0b; color: white; border: none; border-radius: 8px; padding: 5px 16px; font-size: .82rem; font-weight: 600; }
    .btn-edit:hover { background: #d97706; color: white; }
    .btn-hapus { background: #ef4444; color: white; border: none; border-radius: 8px; padding: 5px 16px; font-size: .82rem; font-weight: 600; }
    .btn-hapus:hover { background: #dc2626; color: white; }
    .btn-tambah { background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none; border-radius: 10px; padding: 9px 20px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-tambah:hover { opacity: .9; color: white; }
    .nav-pills .nav-link { border-radius: 10px; color: #64748b; font-weight: 600; }
    .nav-pills .nav-link.active { background: #2563eb; color: white; }
</style>

{{-- Header --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-menu-button-wide me-2"></i>Kelola Menu</h4>
            <small class="opacity-75">Total {{ $menus->count() }} menu tersedia</small>
        </div>
        <a href="{{ route('vendor.menu.create') }}" class="btn-tambah">
            <i class="bi bi-plus-lg"></i> Tambah Menu
        </a>
    </div>
</div>

<div class="container-fluid px-4">

    {{-- Nav --}}
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link px-4" href="{{ route('vendor.dashboard') }}">
                <i class="bi bi-receipt me-1"></i> Pesanan Lunas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active px-4" href="{{ route('vendor.menus') }}">
                <i class="bi bi-menu-button-wide me-1"></i> Kelola Menu
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($menus->count() > 0)
    <div class="row g-3">
        @foreach($menus as $menu)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card menu-card h-100">
                @if($menu->path_gambar)
                    <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                         class="menu-img" alt="{{ $menu->nama_menu }}">
                @else
                    <div class="menu-placeholder">
                        <i class="bi bi-egg-fried fs-1 text-muted"></i>
                    </div>
                @endif
                <div class="card-body">
                    <div class="nama">{{ $menu->nama_menu }}</div>
                    <div class="deskripsi">{{ Str::limit($menu->deskripsi ?? 'Tidak ada deskripsi', 60) }}</div>
                    <div class="harga">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('vendor.menu.edit', $menu->idmenu) }}" class="btn-edit flex-fill text-center">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form action="{{ route('vendor.menu.delete', $menu->idmenu) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus menu ini?')" class="flex-fill">
                        @csrf
                        @method('DELETE')
                        <button class="btn-hapus w-100">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $menus->links() }}</div>

    @else
    <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
        <p class="text-muted mb-3">Belum ada menu. Mulai tambahkan menu kantin Anda.</p>
        <a href="{{ route('vendor.menu.create') }}" class="btn-tambah mx-auto">
            <i class="bi bi-plus-lg"></i> Tambah Menu Pertama
        </a>
    </div>
    @endif

</div>
@endsection