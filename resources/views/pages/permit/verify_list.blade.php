@php
    use App\Models\TrxRequest;
@endphp

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
                    url: "{{ url('api/permit/dt_request_officer_list') }}",
                    type: 'POST',
                    data: function(d){
                        d._token = "{{ csrf_token() }}";
                    },
                    beforeSend: () => {
                        Loading.show('Memuat data permohonan...');
                    },
                    complete: function() {
                        Loading.hide();
                    },
                    error: function(xhr, error, thrown) {
                        Loading.hide();
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
                                    <i class="bi bi-eye"></i>
                                </button>
                            `;

                            @if(is_verificator())
                                if(row.status == '{{ TrxRequest::STATUS_SUBMITTED }}'){
                                    content += `
                                        <button 
                                            class="btn btn-sm btn-warning verify-btn" 
                                            data-req_id="${row.req_id}"
                                        >
                                            <i class="bi bi-zoom-in"></i>
                                        </button>
                                    `;
                                }
                            @endif

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
            
            @if(is_verificator())
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
    </script>
@endpush
