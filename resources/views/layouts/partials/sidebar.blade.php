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

  </ul>
</nav>