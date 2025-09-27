@php
    use App\Models\TrxRequest;
    use App\Services\PermitService;
@endphp

@extends('app')

@section('title', config('app.name').'- Penerbitan Izin')

@section('breadcrumbs', 'Penerbitan Izin')

@include('imports.datatable')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    {{-- <h3 class="card-title">Document List</h3> --}}
                </div>

                <div id="table_permit_wrapper" class="card-body table-responsive">
                    <table id="table_permit" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama PAUD</th>
                                <th>Tanggal Pengajuan</th>
                                <th>File SK Izin</th>
                                <th>Unggah SK</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Decree Upload Modal -->
    <div class="modal fade" id="decreeUploadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload File SK Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="decreeUploadForm">
                        <input type="hidden" name="req_id" id="modal_req_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Nomor SK</label>
                            <input type="text" class="form-control" name="decree_num" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit</label>
                                <input type="date" class="form-control" name="issued_date" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Berlaku</label>
                                <input type="date" class="form-control" name="effective_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kadaluarsa</label>
                                <input type="date" class="form-control" name="expired_date" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">File SK</label>
                            <label class="btn btn-outline-primary w-100 decree-file-modal-label">
                                <i class="bi bi-upload"></i> Pilih File SK
                                <input type="file" class="d-none" id="modal_decree_file" name="decree_file" accept=".pdf" required>
                            </label>
                            <div id="filePreview" class="mt-2 alert alert-info" style="display: none;">
                                <i class="bi bi-file-earmark-pdf"></i> <span id="fileName">No file selected</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="uploadDecreeBtn">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .decree-file-modal-label {
            cursor: pointer;
            padding: 10px;
            border: 2px dashed #0d6efd;
            border-radius: 5px;
            text-align: center;
        }
        .decree-file-modal-label:hover {
            background-color: #f8f9fa;
        }
    </style>

    @include('components.document_preview')
@endsection

@push('scripts')
    <script>
        let dt_table = null;

        $(document).ready(function() {
            // $('[name="published_month"]').datepicker({
            //     format: 'mm/yyyy',
            //     autoclose: true,
            //     todayHighlight: true,
            //     minViewMode: 'months',
            //     clearBtn: true,
            //     allowEmptyDate: true
            // })
            // .datepicker('setDate', new Date());

            dt_table = $('#table_permit').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ url('api/permit/dt_request_officer_list') }}",
                    type: 'POST',
                    data: function(d){
                        d._token = "{{ csrf_token() }}";
                    },
                    beforeSend: () => {
                        Loading.tableLoading($('#table_permit tbody'), 4, 'Memuat data ...');
                    },
                    complete: function() {
                    },
                    error: function(xhr, error, thrown) {
                        console.log(error);
                        Loading.tableLoadingError($('#table_permit tbody'), 4, 'Gagal memuat data');
                        Modal.showError('Gagal memuat data. Silakan coba lagi.');
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'request_date', name: 'request_date'},
                    // { data: 'reg_num', name: 'reg_num' },
                    {
                        data: 'decree', 
                        name: 'decree',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => {
                            return row.decree? `
                                <div class="d-flex flex-column">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary preview-pdf mb-2" 
                                        data-permit_decree_id="${row.decree.permit_decree_id}"
                                        data-filename="${row.decree.filename}"
                                        data-mime="${row.decree.mime}"
                                        data-url="{{ url('permit/decree/preview') }}/${row.decree.permit_decree_id}"
                                        data-index=""
                                    >
                                        <i class="bi bi-eye"></i> Lihat File
                                    </button>
                                    <small class="form-text text-muted">${row.decree.filename}</small>
                                    <small class="form-text text-muted">(Terbit pada ${row.decree.created_at})</small>
                                </div>
                            `: `
                                <span class="ms-2 mb-2 text-danger">Belum ada SK</span>
                            `;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return ['{{ PermitService::STATUS_TEXT_PUBLISHING }}', '{{ PermitService::STATUS_TEXT_PUBLISHED }}'].indexOf(row.status_text) !== -1 ? `
                                <div class="file-upload-container">
                                    <button class="btn btn-sm btn-outline-primary mb-2 w-100 open-decree-modal" 
                                        data-req_id="${row.req_id}">
                                        <i class="bi bi-upload"></i> Pilih File SK
                                    </button>
                                </div>
                            ` : `
                                <span class="ms-2 mb-2 text-danger">${row.status_text}</span>
                            `;
                        }
                    }
                ],
                // order: [
                //     [1, 'desc'],
                //     [2, 'desc']
                // ]
            });

            // Open modal for decree upload
            $(document).on('click', '.open-decree-modal', function(e) {
                const reqId = $(this).data('req_id');
                $('#modal_req_id').val(reqId);
                
                // Reset form
                $('#decreeUploadForm')[0].reset();
                $('#filePreview').hide();
                
                $('#decreeUploadModal').modal('show');
            });

            // Handle file selection in modal
            $('#modal_decree_file').on('change', function(e) {
                const file = this.files[0];
                if (file) {
                    $('#fileName').text(file.name);
                    $('#filePreview').show();
                }
            });

            // Handle upload button click
            $('#uploadDecreeBtn').on('click', function() {
                const formData = new FormData($('#decreeUploadForm')[0]);
                
                // Validate form
                if (!formData.get('decree_num') || !formData.get('issued_date') || !formData.get('expired_date') || !formData.get('decree_file')) {
                    Modal.showError('Harap isi semua field yang diperlukan');
                    return;
                }
                
                Loading.show('Mengupload file SK...');
                
                $.ajax({
                    url: '{{ url("api/permit/decree_upload") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status == 0) {
                            $('#decreeUploadModal').modal('hide');
                            dt_table.ajax.reload();
                            Modal.showSuccess('File SK berhasil diupload');
                        } else {
                            Modal.showError(response.message || 'Gagal mengupload file');
                        }
                    },
                    error: function(xhr, status, error) {
                        Modal.showError('Error mengupload file');
                        Utils.ajaxErrorHandler(xhr, status, error);
                    },
                    complete: function() {
                        Loading.hide();
                    }
                });
            });
        });
    </script>
@endpush
