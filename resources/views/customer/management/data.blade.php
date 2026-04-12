@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3">
                <i class="mdi mdi-table"></i> Data Customer
            </h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.management.index') }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Lokasi</th>
                        <th class="text-center" style="width: 80px;">Foto</th>
                        <th class="text-center" style="width: 70px;">Tipe</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $customer->nama }}</strong>
                        </td>
                        <td>
                            <small>{{ Str::limit($customer->alamat, 30) }}</small>
                        </td>
                        <td>
                            <small>{{ $customer->kota }}, {{ $customer->provinsi }}</small>
                        </td>
                        <td class="text-center">
                            @if ($customer->tipe_foto == 'blob' && $customer->foto)
                                <img src="{{ $customer->foto }}"
                                     width="50" height="50" class="rounded img-thumbnail" 
                                     style="object-fit: cover; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#fotoModal"
                                     onclick="showPhoto(this)">
                            @elseif ($customer->tipe_foto == 'file' && $customer->foto_path)
                                <img src="{{ Storage::url($customer->foto_path) }}" 
                                     width="50" height="50" class="rounded img-thumbnail" 
                                     style="object-fit: cover; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#fotoModal"
                                     onclick="showPhoto(this)">
                            @else
                                <span class="badge bg-secondary">Tidak ada</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($customer->tipe_foto)
                                <span class="badge bg-{{ $customer->tipe_foto == 'blob' ? 'info' : 'success' }}">
                                    <i class="mdi mdi-{{ $customer->tipe_foto == 'blob' ? 'database' : 'file-document' }}"></i>
                                    {{ ucfirst($customer->tipe_foto) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" 
                                    onclick="deleteCustomer({{ $customer->id }})" 
                                    title="Hapus">
                                <i class="mdi mdi-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="mdi mdi-inbox-outline" style="font-size: 2rem;"></i><br>
                            Tidak ada data customer. <a href="{{ route('customer.management.create1') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk preview foto -->
<div class="modal fade" id="fotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewFoto" style="max-width: 100%; max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<script>
function showPhoto(img) {
    document.getElementById('previewFoto').src = img.src;
}

function deleteCustomer(id) {
    if (confirm('Yakin ingin menghapus customer ini?')) {
        fetch(`/customer/management/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal menghapus: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}
</script>
@endsection
