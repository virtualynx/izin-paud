<style>
  .navbar-nav .nav-item {
    margin-right: 10px;
  }
  
  /* For mobile view */
  @media (max-width: 991.98px) {
    .navbar-nav .nav-item {
      margin-right: 0;
      margin-bottom: 8px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img 
        src="https://kecamatanciomas.bogorkab.go.id/assets/front/img/header_logo_1720431087373188301.png" 
        alt="Logo" 
        style="height:40px;"
      >
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="{{ config('app.kec_ciomas_url') }}">&#8592; Kembali</a></li>

        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>

        {{-- <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Panduan
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Persyaratan</a></li>
            <li><a class="dropdown-item" href="#">Alur Proses</a></li>
            <li><a class="dropdown-item" href="#">FAQ</a></li>
          </ul>
        </li> --}}

        @if(is_loggedin())
          <li class="nav-item"><a class="nav-link" href="{{ url('/permit/request_list') }}">Pengajuan</a></li>

          @if(is_verificator() || is_approver())
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Petugas
              </a>
              
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ url('/permit/verify_list') }}">Verifikasi</a></li>
                @if(is_approver())
                  <li><a class="dropdown-item" href="{{ url('/permit/approval_list') }}">Approval</a></li>
                @endif
                <li><a class="dropdown-item" href="{{ url('/permit/decree_list') }}">Penerbitan Izin</a></li>
              </ul>
            </li>
          @endif

          {{-- <li class="nav-item"><a class="btn btn-primary" href="{{ url('/permit/request') }}">AJUKAN IZIN</a></li> --}}
          
          {{-- <li class="nav-item"><a class="btn btn-danger" href="{{ url('/sso/logout') }}">Logout</a></li> --}}
        
          @php
            $userDisplayName = userinfo()->name ?? userinfo()->email;

            $maxDisplayLength = 10;
            $trimmedName = strlen($userDisplayName) > $maxDisplayLength ? substr($userDisplayName, 0, $maxDisplayLength) . '...' : $userDisplayName;
          @endphp
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i> Halo, {{ $trimmedName }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="dropdown-item-text">
                <small>Halo,</small>
                <div><strong>{{ $userDisplayName }}</strong></div>
              </li>
              <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Lihat/Edit Profil</a></li>
              
              <li><hr class="dropdown-divider"></li>
              
              <li><a class="dropdown-item" href="{{ url('/sso/logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </li>
        @else
          <li class="nav-item"><a class="btn btn-primary" href="{{ url('/sso/login') }}">Login</a></li>
        @endif
      </ul>
    </div>
  </div>
</nav>