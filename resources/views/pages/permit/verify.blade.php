@extends('app')

@section('title', 'Verifikasi '.$request->name)

@section('breadcrumbs', 'Verifikasi '.$request->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Verifikasi Dokumen - {{ $request->name }}</h4>
                    <span class="badge bg-light text-dark fs-6" id="statusBadge">Loading status...</span>
                </div>

                <div class="card-body">
                    <!-- Informasi Dasar Pengajuan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="section-title">Informasi Lembaga</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="label-width fw-bold">Nama Lembaga:</td>
                                    <td id="institutionName">{{ $request->name }}</td>
                                </tr>
                                <tr>
                                    <td class="label-width fw-bold">Bentuk Lembaga:</td>
                                    <td id="foundationType">{{ $request->foundation_type }}</td>
                                </tr>
                                <tr>
                                    <td class="label-width fw-bold">Alamat:</td>
                                    <td id="institutionAddress">{{ $request->address }}</td>
                                </tr>
                                <tr>
                                    <td class="label-width fw-bold">Tahun Berdiri:</td>
                                    <td id="foundedYear">{{ $request->founded_year }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="section-title">Data Penanggung Jawab</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="label-width fw-bold">Nama:</td>
                                    <td id="picName">{{ $request->pic_name }}</td>
                                </tr>
                                <tr>
                                    <td class="label-width fw-bold">Email:</td>
                                    <td id="picEmail">{{ $request->email }}</td>
                                </tr>
                                <tr>
                                    <td class="label-width fw-bold">Telepon:</td>
                                    <td id="picPhone">{{ $request->phone }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Dokumen yang Perlu Diverifikasi -->
                    <h5 class="section-title">Dokumen Persyaratan</h5>
                    <div class="table-responsive">
                        <table id="table_documents" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">Jenis Dokumen</th>
                                    <th width="25%">File</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh JavaScript -->
                                <?php
                                    $index = 1;
                                ?>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $doc->doctype->name }} {!! $doc->doctype->is_optional? '<span class="text-muted">(opsional)</span>': '<span class="text-danger">*wajib</span>' !!}</td>
                                        <td>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary preview-pdf" 
                                                data-req_doc_id="{{ $doc->req_doc_id }}"
                                                data-filename="{{ $doc->filename }}"
                                                data-mime="{{ $doc->mime }}"
                                                data-url="{{ url('permit/document/preview').'/'.$doc->req_doc_id }}"
                                            >
                                                <i class="bi bi-eye"></i> Lihat File
                                            </button>
                                            <span class="ms-2">{{ $doc->filename }}</span>
                                        </td>
                                        <td>
                                            <?php
                                                $badge_class = 'bg-warning';
                                                $status = 'pending';
                                                switch ($doc->verify_status) {
                                                    case 'verified':
                                                        $badge_class = 'bg-success';
                                                        break;
                                                    case 'revision':
                                                        $badge_class = 'bg-danger';
                                                        break;
                                                    default:
                                                }
                                            ?>
                                            <span class="badge {{ $badge_class }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm doc-status" data-req_doc_id="{{ $doc->req_doc_id }}">
                                                <option value="pending" {{ $status=='pending'? 'selected': '' }}>Pending</option>
                                                <option value="verified" {{ $status=='verified'? 'selected': '' }}>Verified</option>
                                                <option value="revision" {{ $status=='revision'? 'selected': '' }}>Revision</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Catatan dan Tindakan -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Catatan Verifikasi</h6>
                                </div>
                                <div class="card-body">
                                    <textarea id="verificationNotes" class="form-control" rows="3" 
                                            placeholder="Tambahkan catatan verifikasi (opsional)"></textarea>
                                    
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button id="rejectBtn" class="btn btn-danger">
                                            <i class="bi bi-x-circle"></i> Tolak Pengajuan
                                        </button>
                                        <button id="verifyBtn" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Setujui Verifikasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.document_preview')

@endsection

@push('scripts')
    <script>
        // $(document).ready(function() {
        //     $('#table_documents').on('click', '.btn-preview', function(event) {
        //         const fileUrl = $(this).data('url');

        //         // Trigger modal preview dari component document_preview
        //         $('.preview-pdf').trigger('click', [{
        //             url: fileUrl,
        //             parent_url: '',
        //             parent_doc_number: ''
        //         }]);
        //     });
        // });

        function getStatusBadgeClass(status) {
            switch(status) {
                case 'valid': return 'bg-success';
                case 'invalid': return 'bg-danger';
                default: return 'bg-warning';
            }
        }
    </script>
@endpush
