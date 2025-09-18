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
            
            <!-- Call-to-Action Buttons -->
            <div class="action-buttons mt-4">
                @if(is_loggedin())
                    <a href="{{ url('/permit/request') }}" class="btn btn-primary btn-lg me-3 mb-2 mb-md-0">
                        <i class="bi bi-plus-circle"></i> Ajukan Izin Baru
                    </a>
                    <a href="{{ url('/permit/request_list') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-list-ul"></i> Lihat Pengajuan Saya
                    </a>
                @else
                    <a href="{{ url('/sso/login') }}" class="btn btn-primary btn-lg me-3 mb-2 mb-md-0">
                        <i class="bi bi-box-arrow-in-right"></i> Login untuk Mengajukan
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#panduanModal">
                        <i class="bi bi-info-circle"></i> Lihat Panduan
                    </a>
                @endif
            </div>
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
            
            <!-- Quick Links Card -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tautan Penting</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#panduanModal">
                        <i class="bi bi-file-text me-2"></i> Panduan Penggunaan
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-question-circle me-2"></i> FAQ (Pertanyaan Umum)
                    </a>
                    <a href="{{ config('app.kec_ciomas_url') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-house me-2"></i> Website Kecamatan Ciomas
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panduan Modal (to be implemented) -->
    <div class="modal fade" id="panduanModal" tabindex="-1" aria-labelledby="panduanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="panduanModalLabel">Panduan Penggunaan SIPENDI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Konten panduan akan ditampilkan di sini. Anda dapat menambahkan langkah-langkah penggunaan sistem, persyaratan dokumen, dan informasi penting lainnya.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .action-buttons {
            padding: 20px 0;
            border-top: 1px solid #eaeaea;
            margin-top: 30px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3494e6, #ec6ead);
            border: none;
            padding: 12px 25px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c83d6, #e55a9f);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .btn-outline-primary {
            border-color: #3494e6;
            color: #3494e6;
            padding: 12px 25px;
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background-color: #3494e6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .sidebar .card {
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .sidebar .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .list-group-item {
            transition: all 0.2s;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            padding-left: 25px;
        }
    </style>
@endsection