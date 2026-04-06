@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>Daftar Vendor Baru</h4>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p class="mb-0">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    
                    <form action="/vendor/register" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="nama_vendor">Nama Vendor *</label>
                            <input type="text" 
                                   id="nama_vendor" 
                                   name="nama_vendor" 
                                   class="form-control @error('nama_vendor') is-invalid @enderror"
                                   value="{{ old('nama_vendor') }}"
                                   required>
                            @error('nama_vendor')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email">Email *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password">Password *</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password_confirmation">Konfirmasi Password *</label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="form-control"
                                   required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="kontak">Nomor Telepon (Opsional)</label>
                            <input type="text" 
                                   id="kontak" 
                                   name="kontak" 
                                   class="form-control"
                                   value="{{ old('kontak') }}">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="deskripsi">Deskripsi Vendor (Opsional)</label>
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      class="form-control"
                                      rows="3">{{ old('deskripsi') }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">Daftar Vendor</button>
                    </form>
                    
                    <hr>
                    <p class="text-center">
                        Sudah punya akun?
                        <a href="/vendor/login" class="btn btn-link">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
