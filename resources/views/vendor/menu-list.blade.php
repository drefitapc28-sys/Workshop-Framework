@extends('layouts.app')

@section('content')
<style>
    body { background: #f0f2f5; }
    .form-wrap { max-width: 620px; margin: 30px auto; }
    .page-header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; }
    .form-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .form-card .card-body { padding: 28px; }
    .form-label { font-weight: 600; font-size: .88rem; color: #374151; margin-bottom: 6px; }
    .form-control, .form-select { border-radius: 10px; border: 2px solid #e2e8f0; padding: 10px 14px; font-size: .9rem; transition: border-color .2s; }
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    .img-preview { border-radius: 10px; max-height: 160px; object-fit: cover; border: 2px solid #e2e8f0; }
    .btn-save { background: linear-gradient(135deg, #16a34a, #15803d); color: white; border: none; border-radius: 10px; padding: 11px 28px; font-weight: 700; }
    .btn-save:hover { opacity: .9; color: white; }
    .btn-cancel { border: 2px solid #e2e8f0; background: white; color: #475569; border-radius: 10px; padding: 9px 20px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-cancel:hover { background: #f8fafc; color: #1e293b; }
</style>

<div class="form-wrap px-3">

    {{-- Header --}}
    <div class="page-header">
        <h5 class="mb-1 fw-bold">
            <i class="bi bi-{{ isset($menu) ? 'pencil-square' : 'plus-circle' }} me-2"></i>
            {{ isset($menu) ? 'Edit Menu' : 'Tambah Menu Baru' }}
        </h5>
        <small class="opacity-75">{{ isset($menu) ? 'Perbarui informasi menu' : 'Isi detail menu kantin Anda' }}</small>
    </div>

    {{-- Form --}}
    <div class="card form-card">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ isset($menu) ? route('vendor.menu.update', $menu->idmenu) : route('vendor.menu.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($menu)) @method('PUT') @endif

                <div class="mb-4">
                    <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" name="nama_menu" class="form-control"
                           value="{{ old('nama_menu', $menu->nama_menu ?? '') }}"
                           placeholder="Contoh: Nasi Goreng Spesial" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e2e8f0;border-right:none;">Rp</span>
                        <input type="number" name="harga" class="form-control" style="border-radius:0 10px 10px 0;"
                               value="{{ old('harga', $menu->harga ?? '') }}"
                               placeholder="15000" min="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi Menu</label>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Deskripsi singkat menu...">{{ old('deskripsi', $menu->deskripsi ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Gambar Menu</label>
                    @if(isset($menu) && $menu->path_gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                                 class="img-preview d-block mb-1" alt="Gambar saat ini">
                            <small class="text-muted">Gambar saat ini. Upload baru untuk mengganti.</small>
                        </div>
                    @endif
                    <input type="file" name="path_gambar" class="form-control"
                           accept="image/*" onchange="previewImg(this)">
                    <div id="previewWrap" class="mt-2 d-none">
                        <img id="previewEl" src="" class="img-preview">
                    </div>
                    <div class="form-text">Format JPG/PNG/GIF, maks 2MB</div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg me-1"></i>
                        {{ isset($menu) ? 'Update Menu' : 'Simpan Menu' }}
                    </button>
                    <a href="{{ route('vendor.menus') }}" class="btn-cancel">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewEl').src = e.target.result;
            document.getElementById('previewWrap').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush