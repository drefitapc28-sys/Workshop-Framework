<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    {{-- PROFILE --}}
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
          <span class="text-secondary text-small">Administrator</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    {{-- DASHBOARD --}}
    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('home') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    {{-- DATA MASTER --}}
    <li class="nav-item">
      <a class="nav-link"
         data-bs-toggle="collapse"
         href="#datamaster"
         aria-expanded="{{ request()->routeIs('kategori.*') || request()->routeIs('buku.*') ? 'true' : 'false' }}"
         aria-controls="datamaster">

        <span class="menu-title">Data Master</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-database menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('kategori.*') || request()->routeIs('buku.*') ? 'show' : '' }}"
           id="datamaster">

        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}"
               href="{{ route('kategori.index') }}">
               Kategori
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}"
               href="{{ route('buku.index') }}">
               Buku
            </a>
          </li>

        </ul>
      </div>
    </li>

    {{-- DATA BARANG --}}
    <li class="nav-item">
      <a class="nav-link"
         data-bs-toggle="collapse"
          href="#databarang"
          aria-expanded="{{ request()->routeIs('barang.*') ? 'true' : 'false' }}"
          aria-controls="databarang">
        <span class="menu-title">Data Barang</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-tag menu-icon"></i>
      </a>
      <div class="collapse {{ request()->routeIs('barang.*') ? 'show' : '' }}"
           id="databarang">

        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.index') ? 'active' : '' }}"
               href="{{ route('barang.index') }}">
              Data Barang
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.tabel-biasa') ? 'active' : '' }}"
               href="{{ route('barang.tabel-biasa') }}">
              Tabel Biasa
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.tabel-dt') ? 'active' : '' }}"
               href="{{ route('barang.tabel-dt') }}">
              Tabel Datatables
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}"
               href="{{ route('pos.index') }}">
              POS
            </a>
          </li>

        </ul>
      </div>
    </li>

    {{-- GENERATE PDF --}}
    <li class="nav-item">
      <a class="nav-link"
         data-bs-toggle="collapse"
         href="#generatepdf"
         aria-expanded="{{ request()->routeIs('pdf.*') ? 'true' : 'false' }}"
         aria-controls="generatepdf">

        <span class="menu-title">Generate PDF</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('pdf.*') ? 'show' : '' }}"
           id="generatepdf">

        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pdf.sertifikat') ? 'active' : '' }}"
               href="{{ route('pdf.sertifikat') }}">
              Sertifikat
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pdf.undangan') ? 'active' : '' }}"
               href="{{ route('pdf.undangan') }}">
              Undangan
            </a>
          </li>

        </ul>
      </div>
    </li>

    {{-- WILAYAH --}}
    <li class="nav-item">
      <a class="nav-link"
         data-bs-toggle="collapse"
         href="#wilayah"
         aria-expanded="{{ request()->routeIs('wilayah.*') ? 'true' : 'false' }}"
         aria-controls="wilayah">

        <span class="menu-title">Wilayah</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-map-marker menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('wilayah.*') ? 'show' : '' }}"
           id="wilayah">

        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('wilayah.index') ? 'active' : '' }}"
               href="{{ route('wilayah.index') }}">
              Data Wilayah
            </a>
          </li>

        </ul>
      </div>
    </li>

    {{-- CANTEEN ORDERING --}}
    <li class="nav-item">
      <a class="nav-link"
         data-bs-toggle="collapse"
         href="#canteen"
         aria-expanded="{{ request()->routeIs('customer.*') || request()->routeIs('vendor.*') ? 'true' : 'false' }}"
         aria-controls="canteen">

        <span class="menu-title">Pemesanan Kantin</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-food menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('customer.*') || request()->routeIs('vendor.*') ? 'show' : '' }}"
           id="canteen">

        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.order') ? 'active' : '' }}"
               href="{{ route('customer.order') }}">
              Pesan Makanan
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('vendor.login') ? 'active' : '' }}"
               href="{{ route('vendor.login') }}">
              Vendor Login
            </a>
          </li>

        </ul>
      </div>
    </li>


  </ul>
</nav>