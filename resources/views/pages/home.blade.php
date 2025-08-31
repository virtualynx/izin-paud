@extends('app')

@section('content')
    <div class="row">
        <!-- Main Article -->
        <div class="col-md-8">
            <h2>Tentang {{ config('app.name', '<app_name>') }}</h2>
            <p><strong>{{ config('app.name', '<app_name>') }}</strong> adalah kondisi gagal tumbuh pada anak akibat kekurangan gizi kronis, terutama sejak dalam kandungan hingga usia dua tahun. Dampaknya tidak hanya pada tinggi badan yang lebih rendah dari standar, tetapi juga pada perkembangan otak, daya tahan tubuh, dan produktivitas di masa depan.</p>
            <p>Meskipun prevalensi stunting di Indonesia menurun dari 37,2% (2013) menjadi 29,9% (2018), angka ini masih tergolong tinggi. Berdasarkan data kerja Puskesmas Ciapus, tercatat 164 anak mengalami stunting di tiga desa: Sukamakmur (83 anak), Ciapus (44 anak), dan Sukaharja (37 anak). </p>
            <p>Puskesmas menyusun program inovatif bernama <strong>MASA RANTING (Makan Sehat, Berenang Atasi Stunting)</strong>.</p>
            <p>Program ini mengombinasikan intervensi gizi dan aktivitas fisik berupa tambahan susu dan telur, edukasi keluarga, serta renang di Kolam Renang Zam-zam Tirta. Target kenaikan berat badan minimal 0,5 kg tiap 2 minggu dan peningkatan status tinggi badan menurut umur.</p>
            <p>Melalui pendekatan menyenangkan, terarah, dan kolaboratif, MASA RANTING diharapkan menjadi model intervensi gizi anak untuk menciptakan generasi sehat, cerdas, dan produktif.</p>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 sidebar">
            <h4>Dasar Hukum</h4>
            <div class="card">
            <div class="card-body">
                <h6>Permendikbudristek No. 32 Tahun 2022 </h6>
                <small>Standar Teknis Pelayanan Minimal Pendidikan Anak Usia Dini</small>
            </div>
            </div>
            <div class="card">
            <div class="card-body">
                <h6>Perbup Bogor Nomor 58 Tahun 2023</h6>
                <small>Pedoman Pelimpahan Sebagian Kewenangan Bupati kepada Camat untuk Melaksanakan Urusan Pemerintahan Daerah</small>
            </div>
            </div>
            <div class="card">
            <div class="card-body">
                <h6>Kepbup Bogor Nomor 100.2/469/Kpts/Per-UU/2023 </h6>
                <small>Pelimpahan Sebagian Kewenangan Bupati kepada Camat di Pemerintah Daerah Kabupaten Bogor untuk Melaksanakan Urusan Pemerintahan Daerah</small>
            </div>
            </div>
        </div>
    </div>
@endsection