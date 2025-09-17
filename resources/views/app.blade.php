<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="https://kecamatanciomas.bogorkab.go.id/assets/front/img/fav_icon_17188554131994845041.png" type="image/png">

    <title>@yield('title', config('app.name'))</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('head-stacks') <!-- For head stacks -->
</head>
<body>
    <style>
        .global-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            text-align: center;
            color: white;
        }

        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
    <!-- Global Loading Overlay -->
    <div id="globalLoadingOverlay" class="global-loading-overlay d-none">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memproses permohonan...</p>
        </div>
    </div>

    @include('components.modal')

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>@yield('breadcrumbs')</h1>
        </div>
    </section>

    <!-- Content -->
    <section class="content-section">
        <div class="container">
            @yield('content')
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')
    
    @include('components.scripts')

    @stack('scripts') <!-- For JS stacks -->
</body>
</html>
