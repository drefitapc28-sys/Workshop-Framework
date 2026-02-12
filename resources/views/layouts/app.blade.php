<!DOCTYPE html>
<html lang="id">
<head>
    {{-- Header --}}
    @include('layouts.partials.header')
    
    {{-- Style Global - Purple Admin CSS --}}
    @include('layouts.partials.style-global')
    
    {{-- Style Page - Khusus halaman ini --}}
    @stack('styles')
</head>
<body>
    <div class="container-scroller">
        {{-- Navbar --}}
        @include('layouts.partials.navbar')
        
        <div class="container-fluid page-body-wrapper">
            {{-- Sidebar --}}
            @include('layouts.partials.sidebar')
            
            <div class="main-panel">
                <div class="content-wrapper">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    {{-- Content - Area konten utama --}}
                    @yield('content')
                </div>
                
                {{-- Footer --}}
                @include('layouts.partials.footer')
            </div>
        </div>
    </div>
    
    {{-- Javascript Global - Purple Admin JS --}}
    @include('layouts.partials.js-global')
    
    {{-- Javascript Page - Khusus halaman ini --}}
    @stack('scripts')
</body>
</html>
