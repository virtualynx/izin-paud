@php
    use App\Models\TrxRequest;
    use App\Http\Api\PermitApi;
@endphp

@extends('app')

@section('title', config('app.name').'- Approval')

@section('breadcrumbs', 'Approval Pengajuan')

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
                                    class="btn btn-sm btn-info view-btn me-1" 
                                    data-req_id="${row.req_id}"
                                >
                                    <i class="bi bi-eye"></i> Lihat
                                </button>
                            `;

                            if(row.status == '{{ TrxRequest::STATUS_VERIFIED }}' && row.is_my_approval){
                                content += `
                                    <button 
                                        class="btn btn-sm btn-success approve-btn me-1" 
                                        data-req_id="${row.req_id}"
                                    >
                                        <i class="bi bi-hand-thumbs-up-fill"></i> Setujui
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
            
            @if(is_approver())
                $('#table_permit_wrapper').on('click', '.approve-btn', function(event) {
                    event.preventDefault();
                    window.location.href = "{{ url('permit/verify') }}/"+$(this).data('req_id')+"?mode={{ PermitApi::REQUEST_UPDATE_MODE_APPROVE }}";
                });
            @endif

            // $('[name="published_month"]').on('change', function(event){
            //     dt_table.ajax.reload();
            // });
        });
    </script>
@endpush
