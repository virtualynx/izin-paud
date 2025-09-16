@extends('app')

@section('title', config('app.name').'- Verifikasi')

@section('breadcrumbs', 'Verifikasi Pengajuan')

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
                    url: "{{ url('api/permit/dt_to_verify_list') }}",
                    type: 'POST',
                    data: function(d){
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'request_date', name: 'request_date'},
                    // { data: 'reg_num', name: 'reg_num' },
                    { data: 'status', name: 'status'},
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let content = '';

                            @if(is_verificator() || is_approver())
                                content += `
                                    <button 
                                        class="btn btn-sm btn-warning verify-btn" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-zoom-in"></i>
                                    </button>
                                `;
                            @endif
                            
                            if(row.is_own){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-primary edit-btn" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <button 
                                        class="btn btn-sm btn-danger delete-btn" 
                                        data-req_id="${row.req_id}"
                                        data-name="${row.name}"
                                    >
                                        <i class="bi bi-trash"></i>
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
            
            @if(is_verificator() || is_approver())
                $('#table_permit_wrapper').on('click', '.verify-btn', function(event) {
                    event.preventDefault();
                    // window.location.href = "{{ url('permit/verify') }}/" +$(this).data('req_id');
                    window.open("{{ url('permit/verify') }}/"+$(this).data('req_id'), '_blank');
                });
            @endif

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
