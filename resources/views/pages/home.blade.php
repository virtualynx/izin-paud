@extends('app')

@section('content')
    <div class="row">
        <!-- Main Article -->
        <div class="col-md-8">
            <h2>Tentang {{ config('app.name', '<app_name>') }}</h2>
            <p>
                <strong>{{ config('app.name', '<app_name>') }}</strong> (Sistem Informasi Pengajuan Izin Operasional Pendidikan Anak Usia Dini) adalah aplikasi berbasis web yang mempermudah proses pengajuan, verifikasi, hingga penerbitan izin operasional PAUD non-formal. Melalui sistem ini, pemohon dapat mengunggah dokumen persyaratan, memantau status pengajuan secara real-time, dan mengunduh SK izin setelah disetujui. 
            </p>
            <p>
                <strong>{{ config('app.name', '<app_name>') }}</strong> hadir untuk menghadirkan layanan perizinan yang cepat, transparan, dan sesuai regulasi, sekaligus mendukung peningkatan kualitas layanan pendidikan anak usia dini.
            </p>
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