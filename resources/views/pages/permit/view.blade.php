@php
    use App\Models\TrxRequest;
    use App\Models\TrxRequestDocument;
    use App\Models\TrxRequestApproval;
    use App\Http\Api\PermitApi;
@endphp

@extends('app')

@php
    $page_title = 'Pengajuan: '.$request->name;
@endphp

@section('title', $page_title)

@section('breadcrumbs', $page_title)

@section('content')
    {{-- Loading animation for verify-status badges --}}
    <style>
        .badge.bg-info {
            background-color: #0dcaf0 !important;
        }

        .bi-arrow-repeat.spin {
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $page_title }}</h4>
                    {{-- <span class="badge bg-light text-dark fs-6" id="statusBadge">Loading status...</span> --}}
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
                                    <th width="50%">Jenis Dokumen</th>
                                    <th width="30%">File</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $index = 1;
                                ?>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>{{ $index}}</td>
                                        <td>{{ $doc->doctype->name }} {!! $doc->doctype->is_optional? '<span class="text-muted">(opsional)</span>': '<span class="text-danger">*wajib</span>' !!}</td>
                                        <td>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary preview-pdf" 
                                                data-req_doc_id="{{ $doc->req_doc_id }}"
                                                data-filename="{{ $doc->filename }}"
                                                data-mime="{{ $doc->mime }}"
                                                data-url="{{ url('permit/document/preview').'/'.$doc->req_doc_id }}"
                                                data-index="{{ $index++ }}"
                                            >
                                                <i class="bi bi-eye"></i> Lihat File
                                            </button>
                                            <span class="ms-2">{{ $doc->filename }}</span>
                                        </td>
                                        <td>
                                            <?php
                                                $badge_class = 'bg-secondary';
                                                $status = 'pending';

                                                if($doc->verify_status){
                                                    $status = $doc->verify_status;

                                                    switch ($doc->verify_status) {
                                                        case 'verified':
                                                            $badge_class = 'bg-success';
                                                            break;
                                                        case 'revision':
                                                            $badge_class = 'bg-warning';
                                                            break;
                                                        default:
                                                    }
                                                }
                                            ?>
                                            <span class="badge {{ $badge_class }}">
                                                <span class="verify-status">{{ $status }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Button Process -->
                    <div class="row mt-4">
                        <div id="process-btn-wrapper" class="col-md-12 d-flex justify-content-end gap-2 mt-3">
                            <button onclick="window.history.back()" 
                                    class="btn btn-primary"
                                    aria-label="Kembali ke halaman sebelumnya"
                            >
                                <i class="bi bi-arrow-90deg-left"></i> Kembali
                            </button>
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
        const modalRevNotes = $('#modal_revision_notes');

        $(document).ready(function() {
        });
    </script>
@endpush
