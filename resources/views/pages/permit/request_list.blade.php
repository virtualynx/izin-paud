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
                        Loading.tableError($('#table_permit tbody'), 4, 'Gagal memuat data');
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
                                    class="btn btn-sm btn-info view-btn" 
                                    data-req_id="${row.req_id}"
                                >
                                    <i class="bi bi-eye"></i> Lihat
                                </button>
                            `;

                            if(row.status_text == '{{ PermitService::STATUS_TEXT_REVISION }}'){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-warning revisi-btn" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-pencil"></i> Revisi
                                    </button>
                                `;
                            }

                            if(row.status == '{{ TrxRequest::STATUS_DRAFT }}'){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-primary edit-btn" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-pencil-square"></i> Ubah
                                    </button>
                                    
                                    <button 
                                        class="btn btn-sm btn-danger delete-btn" 
                                        data-req_id="${row.req_id}"
                                        data-name="${row.name}"
                                    >
                                        <i class="bi bi-trash"></i> Hapus
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

            $('#table_permit_wrapper').on('click', '.revisi-btn', function(event) {
                event.preventDefault();
                let reqId = $(this).data('req_id');
                window.location.href = "{{ url('permit/revision') }}/" + reqId;
            });

            $('#table_permit_wrapper').on('click', '.edit-btn', function(event) {
                event.preventDefault();
                window.location.href = "{{ url('permit/edit') }}/" + $(this).data('req_id');
            });
            
            $('#table_permit_wrapper').on('click', '.delete-btn', function(event) {
                event.preventDefault();
            });

            // $('[name="published_month"]').on('change', function(event){
            //     dt_table.ajax.reload();
            // });
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
