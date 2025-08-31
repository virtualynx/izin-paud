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

                <div class="card-body table-responsive">
                    <table id="table_permit" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama PAUD</th>
                                <th>Nomor Registrasi</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
                    url: "{{ url('api/permit/dt_to_verify_list') }}",
                    type: 'POST',
                    data: function(d){
                        // d.published_month = $('[name="published_month"]').val();
                        // d.tag_objects = $('[name="tag_objects"]').val();
                        // d.keywords = $('[name="keywords"]').val();
                    }
                },
                columns: [
                    // { 
                    //     data:  'subject', 
                    //     name: 'subject',
                    //     orderable: true,
                    //     searchable: true,
                    //     render: function(data, type, row) {
                    //         if (type === 'display') {
                    //             let text = '';

                    //             if(data){
                    //                 text = data;
                    //             }else{
                    //                 text = row.filename;
                    //             }

                    //             if(row.doc_number){
                    //                 text += ` (${row.doc_number})`;
                    //             }

                    //             return `
                    //                 <a 
                    //                     class="preview-pdf d-inline-flex align-items-center bg-light rounded p-2 pe-1"
                    //                     href="javascript:void(0)"
                    //                     data-url="${row.url}"
                    //                     data-parent_doc_number="${row.parent_doc_number}"
                    //                     data-parent_url="${row.parent_url}"
                    //                 >
                    //                     <span 
                    //                         title="${text}" 
                    //                         style="display:inline-block;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                    //                         class="me-2"
                    //                     >${text}</span>
                    //                     <i class="bi bi-eye"></i>  
                    //                 </a>
                    //             `;
                    //         }
                            
                    //         return data;
                    //     }
                    // },
                    { data: 'name', name: 'name' },
                    { data: 'reg_num', name: 'reg_num' },
                    { data: 'request_date', name: 'request_date'},
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let content = '';
                            
                            @if(has_role('admin'))
                                content = `
                                    <div class="btn-group">
                                        <button 
                                            class="edit-pdf btn btn-sm btn-primary edit-btn" 
                                            data-doc_id="${row.req_id}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        
                                        <button 
                                            class="edit-pdf btn btn-sm btn-primary approve-btn" 
                                            data-doc_id="${row.req_id}"
                                        >
                                            <i class="bi bi-clipboard-check"></i>
                                        </button>
                                        
                                        <button 
                                            class="edit-pdf btn btn-sm btn-primary reject-btn" 
                                            data-doc_id="${row.req_id}"
                                        >
                                            <i class="bi bi-clipboard-x"></i>
                                        </button>

                                        <button 
                                            class="delete-pdf btn btn-sm btn-danger delete-btn" 
                                            data-doc_id="${row.req_id}"
                                            data-name="${row.name}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                `;
                            @endif

                            return content;
                        }
                    }
                ],
                // order: [
                //     [1, 'desc'],
                //     [2, 'desc']
                // ]
            });

            // $('[name="published_month"]').on('change', function(event){
            //     dt_table.ajax.reload();
            // });

            // $('[name="tag_objects"]').on('change', function(event){
            //     dt_table.ajax.reload();
            // });

            // $('#btn_search').on('click', function(event){
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
