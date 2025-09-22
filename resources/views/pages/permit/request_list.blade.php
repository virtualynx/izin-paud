@php
    use App\Models\TrxRequest;
    use App\Services\PermitService;
@endphp

@extends('app')

@section('title', config('app.name').'- Pengajuan')

@section('breadcrumbs', 'Daftar Pengajuan Saya')

@include('imports.datatable')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-primary card-outline">
                {{-- <div class="card-header">
                    <h3 class="card-title">Document List</h3>
                </div> --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Pengajuan Saya</h3>
                    <a class="btn btn-primary ms-2" href="{{ url('/permit/request') }}">
                        <i class="bi bi-plus-circle"></i> AJUKAN IZIN
                    </a>
                </div>

                <div id="table_permit_wrapper" class="card-body table-responsive">
                    <table id="table_permit" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama PAUD</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rate Service -->
    <style>
        .bi.spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .response-icon {
            margin-right: 8px;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            cursor: pointer;
            font-size: 1.5rem;
            padding: 0 2px;
        }

        .star-rating input:checked ~ label {
            color: #ffc107;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc107;
        }
    </style>
    <div class="modal fade" id="modal_rate_service" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penilaian Layanan Pengajuan <span class="paud_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <input type="hidden" name="req_id" value="">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Rating</span></label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" />
                                    <label for="star5" title="5 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4" />
                                    <label for="star4" title="4 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3" />
                                    <label for="star3" title="3 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2" />
                                    <label for="star2" title="2 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1" />
                                    <label for="star1" title="1 star"><i class="bi bi-star-fill"></i></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Catatan</span></label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan Tambahan"></textarea>
                            </div>
                        </div>

                        <!-- Response Message Row -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div id="responseMessage" class="alert d-none">
                                    <i class="response-icon"></i>
                                    <span class="response-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    url: "{{ url('api/permit/dt_request_list') }}",
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
                    { data: 'status_text', name: 'status_text'},
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let content = '';
                            
                            content += `
                                <button 
                                    class="btn btn-sm btn-info view-btn me-1" 
                                    data-req_id="${row.req_id}"
                                >
                                    <i class="bi bi-eye"></i> Lihat
                                </button>
                            `;

                            if(row.status == '{{ TrxRequest::STATUS_DRAFT }}'){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-primary edit-btn me-1" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-pencil-square"></i> Ubah
                                    </button>
                                    
                                    <button 
                                        class="btn btn-sm btn-danger delete-btn me-1" 
                                        data-req_id="${row.req_id}"
                                        data-name="${row.name}"
                                    >
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                `;
                            }

                            if(row.status_text == '{{ PermitService::STATUS_TEXT_REVISION }}'){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-warning revisi-btn me-1" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-pencil"></i> Revisi
                                    </button>
                                `;
                            }

                            if(row.status_text == '{{ PermitService::STATUS_TEXT_PUBLISHED }}' && row.decree){
                                content += `
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-primary preview-pdf me-1" 
                                        data-permit_decree_id="${row.decree.permit_decree_id}"
                                        data-filename="${row.decree.filename}"
                                        data-mime="${row.decree.mime}"
                                        data-url="{{ url('permit/decree/preview') }}/${row.decree.permit_decree_id}"
                                        data-index=""
                                    >
                                        <i class="bi bi-eye"></i> SK Izin
                                    </button>
                                    <button 
                                        class="btn btn-sm btn-warning rate-btn me-1" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-star"></i> Beri Nilai
                                    </button>
                                `;
                            }

                            return `
                                <div class="btn-group">
                                    ${content}
                                </div>
                            `;
                        }
                    }
                ],
                // order: [
                //     [1, 'desc'],
                //     [2, 'desc']
                // ]
            });

            $('#table_permit_wrapper').on('click', '.view-btn', function(event) {
                event.preventDefault();
                window.location.href = "{{ url('permit/view') }}/" + $(this).data('req_id');
            });

            $('#table_permit_wrapper').on('click', '.edit-btn', function(event) {
                event.preventDefault();
                window.location.href = "{{ url('permit/edit') }}/" + $(this).data('req_id');
            });
            
            $('#table_permit_wrapper').on('click', '.delete-btn', function(event) {
                event.preventDefault();
            });

            $('#table_permit_wrapper').on('click', '.revisi-btn', function(event) {
                event.preventDefault();
                let reqId = $(this).data('req_id');
                window.location.href = "{{ url('permit/revision') }}/" + reqId;
            });

            $('#table_permit_wrapper').on('click', '.rate-btn', function(event) {
                event.preventDefault();

                const req_id = $(this).data('req_id');
                const modal_body = $('#modal_rate_service').find('.modal-body');
                const originalBody = modal_body.html();

                $.ajax({
                    url: `{{ url('/api/rate/get') }}/${req_id}`,
                    type: 'GET',
                    beforeSend: ()=>{
                        Utils.showBsModal('#modal_rate_service');
                        Loading.componentLoading(modal_body);
                    },
                    success: function(res) {
                        if(res.status == 0){
                            modal_body.html(originalBody);

                            const modal = $(document).find('#modal_rate_service');
                            modal.find('.paud_name').text(res.data.name);
                            modal.find('input[name="req_id"]').val(req_id);

                            if(res.data.rating){
                                modal.find('input[name="rating"]').prop('checked', false);
                                modal.find(`input[name="rating"][value="${res.data.rating.rating}"]`).prop('checked', true);
                                modal.find('textarea[name="notes"]').val(res.data.rating.notes || '');
                            }
                        }else{
                            Utils.hideBsModal('#modal_rate_service');
                            Modal.showError(res.message);
                        }
                    },
                    error: Utils.ajaxErrorHandler,
                    complete: ()=>{
                    }
                });
            });

            // $('[name="published_month"]').on('change', function(event){
            //     dt_table.ajax.reload();
            // });
        });

        $('#modal_rate_service').find('form').on('submit', function(e) {
            e.preventDefault();
            
            // Hide any previous response messages
            const responseMessage = $('#modal_rate_service').find('#responseMessage');
            responseMessage.addClass('d-none');
            
            // Show loading state
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

            let formData = new FormData(this);

            // Submit revision notes via AJAX
            $.ajax({
                url: '{{ url("api/rate/send") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 0) {
                        // Show success message
                        responseMessage
                            .removeClass('d-none alert-danger')
                            .addClass('alert-success')
                            .html('<i class="bi bi-check-circle-fill"></i> Catatan revisi berhasil disimpan!');
                        
                        setTimeout(() => {
                            responseMessage
                                .addClass('d-none')
                                .html('');
                            Utils.hideBsModal('#modal_rate_service');
                        }, 1000);
                    } else {
                        // Show error message
                        responseMessage
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger')
                            .html(`<i class="bi bi-exclamation-circle-fill"></i> ${response.message || 'Terjadi kesalahan'}`);
                    }
                },
                error: function(xhr, status, error) {
                    responseMessage.removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .html(`<i class="bi bi-exclamation-circle-fill"></i> Error: ${xhr.statusText || 'Terjadi kesalahan jaringan'}`);
                        
                    Utils.ajaxErrorHandler(xhr, status, error);
                },
                complete: function() {
                    // Re-enable submit button
                    submitButton.prop('disabled', false).html('Simpan');
                }
            });
        });

        function deleteFile(el){
            let doc_id = $(el).data('doc_id');
            console.log('deleteFile - doc_id', doc_id);
            let filename = $(el).data('filename');
            console.log('deleteFile - filename', filename);

            modalShow(
                `Hapus ${filename} ?`,
                `
                    Yakin ingin hapus ${filename} ?
                `,
                {
                    label: 'Ya',
                    callback: function(){
                        $.ajax({
                            url: `{{ url('/api/docs/delete') }}/${doc_id}`,
                            type: 'GET',
                            success: function(res) {
                                console.log('res', res);
                                if(res.status == 0){
                                    dt_table.ajax.reload();
                                    modalShow(`
                                        <span class="text-success">Delete success !!</span>
                                    `);
                                }else{
                                    modalShow(
                                        `
                                            <span class="text-danger">
                                                <div>${res.message}</div>
                                            </span>
                                        `,
                                        `<span class="text-danger">Error</span>`
                                    );
                                }
                            },
                            error: function(xhr) {
                                $('#status_message').html(
                                    `<span class="text-danger">Upload failed</span><br>
                                    <small>${xhr.responseJSON?.message || 'Server error'}</small>`
                                );
                            }
                        });
                    }
                }
            );

            return true;
        }
    </script>
@endpush
