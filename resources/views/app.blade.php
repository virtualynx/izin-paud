<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kecamatan Ciomas - Tentang Masa Ranting</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('components.modal')

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Hero -->
    <section class="hero">
    <div class="container">
        <h1>TENTANG MASA RANTING</h1>
    </div>
    </section>

    <!-- Content -->
    <section class="content-section">
        <div class="container">
            <script>
                Modal.show('Test modal');
            </script>
            
            <div class="row">
                <!-- Main Article -->
                <div class="col-md-8">
                    <h2>Masa Ranting</h2>
                    <p><strong>Stunting</strong> adalah kondisi gagal tumbuh pada anak akibat kekurangan gizi kronis, terutama sejak dalam kandungan hingga usia dua tahun. Dampaknya tidak hanya pada tinggi badan yang lebih rendah dari standar, tetapi juga pada perkembangan otak, daya tahan tubuh, dan produktivitas di masa depan.</p>
                    <p>Meskipun prevalensi stunting di Indonesia menurun dari 37,2% (2013) menjadi 29,9% (2018), angka ini masih tergolong tinggi. Berdasarkan data kerja Puskesmas Ciapus, tercatat 164 anak mengalami stunting di tiga desa: Sukamakmur (83 anak), Ciapus (44 anak), dan Sukaharja (37 anak). </p>
                    <p>Puskesmas menyusun program inovatif bernama <strong>MASA RANTING (Makan Sehat, Berenang Atasi Stunting)</strong>.</p>
                    <p>Program ini mengombinasikan intervensi gizi dan aktivitas fisik berupa tambahan susu dan telur, edukasi keluarga, serta renang di Kolam Renang Zam-zam Tirta. Target kenaikan berat badan minimal 0,5 kg tiap 2 minggu dan peningkatan status tinggi badan menurut umur.</p>
                    <p>Melalui pendekatan menyenangkan, terarah, dan kolaboratif, MASA RANTING diharapkan menjadi model intervensi gizi anak untuk menciptakan generasi sehat, cerdas, dan produktif.</p>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4 sidebar">
                    <h4>Berita Terbaru</h4>
                    <div class="card">
                    <div class="card-body">
                        <h6>Pedoman Teknis E-Bumil</h6>
                        <small>05 Aug, 2024</small>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-body">
                        <h6>Operasi Penertiban Spanduk</h6>
                        <small>07 Jun, 2023</small>
                    </div>
                    </div>
                    <div class="card">
                    <div class="card-body">
                        <h6>Kegiatan Kelompok Wanita Tani</h6>
                        <small>06 Jun, 2023</small>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')

</body>
</html>
