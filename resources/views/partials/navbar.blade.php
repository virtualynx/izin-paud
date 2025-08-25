
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

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Panduan
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Persyaratan</a></li>
            <li><a class="dropdown-item" href="#">Alur Proses</a></li>
            <li><a class="dropdown-item" href="#">FAQ</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="btn btn-primary" href="{{ url('/permit/request') }}">AJUKAN IZIN</a></li>
      </ul>
    </div>
  </div>
</nav>