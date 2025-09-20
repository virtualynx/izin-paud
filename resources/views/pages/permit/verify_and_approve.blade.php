@php
    use App\Models\TrxRequest;
    use App\Models\TrxRequestDocument;
    use App\Models\TrxRequestApproval;
@endphp

@extends('app')

@php
    $page_title = $request->name;
    if($is_approver){
        $page_title = 'Approve Pengajuan: '.$page_title;
    }else{
        $page_title = 'Verifikasi Pengajuan: '.$page_title;
    }
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
                                        <td>
                                            <select 
                                                class="form-select form-select-sm doc-status" 
                                                data-req_doc_id="{{ $doc->req_doc_id }}"
                                                data-doctypereq_id="{{ $doc->doctypereq_id }}"
                                            >
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

                    {{-- Catatan Revisi --}}
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Catatan Revisi Dokumen</h6>
                                </div>
                                <div class="card-body">
                                    <table id="table_revnotes" class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="40%">Jenis Dokumen</th>
                                                <th>Catatan</th>
                                                <th width="15%">Ubah</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Revisi Pemeriksa -->
                    {{-- <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Catatan Revisi dari Pemeriksa</h6>
                                </div>
                                <div class="card-body">
                                    <textarea 
                                        id="request_revision_notes" 
                                        class="form-control" 
                                        rows="5" 
                                        placeholder="Catatan revisi dari pemeriksa (opsional)"
                                    >{{ !empty($request->revision_note)? $request->revision_note->notes: '' }}</textarea>
                                    
                                    <div id="req-notes-btn-wrapper" class="d-flex justify-content-start gap-2 mt-3">
                                        <button id="req-notes-btn" class="btn btn-primary">
                                            <i class="bi bi-floppy"></i> Simpan Catatan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Button Process -->
                    <div class="row mt-4">
                        <div id="process-btn-wrapper" class="col-md-12 d-flex justify-content-end gap-2 mt-3">
                            <button id="process-btn" class="btn btn-success">
                                @php
                                    $processBtnLabel = $is_approver? 'Setujui': 'Selesai Verifikasi';
                                @endphp
                                <i class="bi bi-check-circle"></i> {{ $processBtnLabel }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.document_preview')

    <!-- Modal Add Revision Notes -->
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
    </style>
    <div class="modal fade" id="modal_revision_notes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catatan Revisi <span class="doctype"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <input type="hidden" name="req_id" value="{{ $request->req_id }}">
                    <input type="hidden" name="req_doc_id" value="">
                    <input type="hidden" name="rev_note_id" value="">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Catatan Revisi</span></label>
                                <textarea name="revision_notes" class="form-control" rows="3" placeholder="Catatan Revisi"></textarea>
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
@endsection

@push('scripts')
    <script>
        const modalRevNotes = $('#modal_revision_notes');

        $(document).ready(function() {
            renderRevisionNotes();
            renderProcessButton();

            // Update the doc-status change event handler
            $('.doc-status').on('change', function(e){
                const $dropdown = $(this);
                const req_doc_id = $dropdown.data('req_doc_id');
                const status = $dropdown.val();
                const $statusBadge = $dropdown.closest('tr').find('.verify-status');
                const $badgeContainer = $statusBadge.closest('.badge');

                // If changing to revision, just open the modal without changing status yet
                if(status == 'revision'){
                    const doctypereq_id = $dropdown.data('doctypereq_id');
                    editRevisionNotes(req_doc_id, doctypereq_id);
                    
                    // Store the intended value but don't update UI yet
                    $dropdown.data('pending-value', status);
                    
                    // Revert the dropdown to its previous value
                    const previousValue = $dropdown.data('previous-value') || 'pending';
                    $dropdown.val(previousValue);
                    
                    return; // Exit early
                }
                
                // For other status changes (verified/pending), use the reusable function
                // Show loading state
                $dropdown.prop('disabled', true);
                $statusBadge.html('<i class="bi bi-arrow-repeat spin"></i>');
                $badgeContainer.removeClass('bg-success bg-warning bg-secondary').addClass('bg-info');

                // Update status via API
                $.ajax({
                    url: '{{ url("api/permit/reqdoc_update") }}',
                    type: 'POST',
                    data: {
                        req_doc_id: req_doc_id,
                        verify_status: status
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 0) {
                            // Update status display
                            let badgeClass = 'bg-secondary';
                            let statusText = 'pending';
                            
                            if(status == 'verified'){
                                badgeClass = 'bg-success';
                                statusText = 'verified';
                            }

                            // switch(status) {
                            //     case 'verified':
                            //         badgeClass = 'bg-success';
                            //         statusText = 'verified';
                            //         break;
                            //     case 'revision':
                            //         badgeClass = 'bg-warning';
                            //         statusText = 'revision';
                            //         break;
                            // }
                            
                            $statusBadge.text(statusText);
                            $badgeContainer.removeClass('bg-info bg-success bg-warning bg-secondary').addClass(badgeClass);
                            
                            if(status == 'verified' || status == 'revision'){
                                renderRevisionNotes();
                            }
                            renderProcessButton();
                        } else {
                            // Error callback
                            // Revert dropdown on error
                            $dropdown.val($dropdown.data('previous-value') || 'pending');
                            
                            // Show error message
                            Modal.show(
                                `<div class="text-center">
                                    <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 3rem;"></i>
                                    <h4 class="mt-3">Terjadi Kesalahan</h4>
                                    <p>${response.message}</p>
                                </div>`,
                                'Error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        $dropdown.val($dropdown.data('previous-value') || 'pending');
                        Utils.ajaxErrorHandler(xhr, status, error);
                    },
                    complete: function() {
                        $dropdown.prop('disabled', false);
                        // Store current value for potential revert
                        $dropdown.data('previous-value', status);
                    }
                });
            });
        });

        function renderRevisionNotes(){
            const tbody = $('#table_revnotes').find('tbody');

            $.ajax({
                url: '{{ url("api/permit/revision_notes/list")."/".$request->req_id }}',
                type: 'GET',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 0) {
                        tbody.empty();

                        const temp_arr = [];
                        response.data.docreq_notes.forEach(row => {
                            if(row.is_resolved == 1)return;

                            const index = $(`[data-req_doc_id="${row.req_doc_id}"]`).data('index');

                            temp_arr.push({
                                index: index,
                                doctype: row.request_document.doctype.name,
                                rev_note_id: row.rev_note_id,
                                notes: row.notes,
                                req_doc_id: row.req_doc_id
                            });
                        });

                        temp_arr.sort((a, b) => a.index - b.index);

                        temp_arr.forEach(row => {
                            tbody.append(`
                                <tr>
                                    <td>${ row.index }</td>
                                    <td>${ row.doctype }</td>
                                    <td id="revision_notes-data-${ row.rev_note_id }">${ row.notes }</td>
                                    <td>
                                        <button 
                                            type="button" 
                                            class="btn btn-primary"
                                            onclick="editRevisionNotes('${row.req_doc_id}', '${row.doctype}', '${row.rev_note_id}')"
                                        >Ubah</button>
                                    </td>
                                </tr>
                            `);
                        });

                        if(temp_arr.length == 0){
                            tbody.append(`
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <span class="">Tidak ada data ...</span>
                                    </td>
                                </tr>
                            `);
                            Loading.tableEmpty(tbody, 4, 'Tidak ada data');
                        }
                    } else {
                        tbody.empty();
                        // Show error message
                        Loading.tableError(tbody, 4, response.message);
                        // Modal.show(
                        //     `<div class="text-center">
                        //         <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 3rem;"></i>
                        //         <h4 class="mt-3">Terjadi Kesalahan</h4>
                        //         <p>${response.message}</p>
                        //     </div>`,
                        //     'Error'
                        // );
                    }
                },
                beforeSend: () => {
                    Loading.tableLoading(tbody, 4, 'Memuat catatan revisi');
                },
                error: Utils.ajaxErrorHandler,
                complete: function() {
                }
            });
        }

        function editRevisionNotes(req_doc_id, doctype, rev_note_id = null){
            modalRevNotes.find('[name="req_doc_id"]').val(req_doc_id);
            modalRevNotes.find('.doctype').html(doctype);

            if(rev_note_id){
                let existingNotes = $(`#revision_notes-data-${ rev_note_id }`).html();
                modalRevNotes.find('[name="revision_notes"]').val(existingNotes);
                modalRevNotes.find('[name="rev_note_id"]').val(rev_note_id);
            }else{
                modalRevNotes.find('[name="rev_note_id"]').val('');
                modalRevNotes.find('[name="revision_notes"]').val('');
            }

            Utils.showBsModal('#modal_revision_notes');
        }

        $('#modal_revision_notes').find('form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            const req_doc_id = formData.get('req_doc_id');
            const $dropdown = $(`[data-req_doc_id="${req_doc_id}"]`);
            const $statusBadge = $dropdown.closest('tr').find('.verify-status');
            const $badgeContainer = $statusBadge.closest('.badge');
            const submitButton = $(this).find('button[type="submit"]');

            // Hide any previous response messages
            $('#responseMessage').addClass('d-none');
            
            // Show loading state
            submitButton.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

            // Submit revision notes via AJAX
            $.ajax({
                url: '{{ url("api/permit/revision_notes/update") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const responseMessage = $('#responseMessage');

                    if (response.status == 0) {
                        // Show success message
                        responseMessage
                            .removeClass('d-none alert-danger')
                            .addClass('alert-success')
                            .html('<i class="bi bi-check-circle-fill"></i> Catatan revisi berhasil disimpan!');
                        
                        // Now update the document status to revision
                        // Show loading state for document status update
                        $dropdown.prop('disabled', true);
                        $statusBadge.html('<i class="bi bi-arrow-repeat spin"></i>');
                        $badgeContainer.removeClass('bg-success bg-warning bg-secondary').addClass('bg-info');
                        
                        // Update document status via API
                        $.ajax({
                            url: '{{ url("api/permit/reqdoc_update") }}',
                            type: 'POST',
                            data: {
                                req_doc_id: req_doc_id,
                                verify_status: 'revision'
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(statusResponse) {
                                if (statusResponse.status == 0) {
                                    // Update status display
                                    $statusBadge.text('revision');
                                    $badgeContainer.removeClass('bg-info bg-success bg-secondary').addClass('bg-warning');
                                    
                                    // Update the dropdown value
                                    $dropdown.val('revision');
                                    $dropdown.data('previous-value', 'revision');
                                    
                                    renderRevisionNotes();
                                    renderProcessButton();

                                    // if adding revision on approval page even once, removes the process button
                                    @if($mode==1)
                                        $('#process-btn').hide();
                                    @endif
                                    
                                    // Close the modal after a brief delay
                                    setTimeout(function() {
                                        modalRevNotes.modal('hide');
                                        
                                        // Reset the modal form
                                        modalRevNotes.find('form')[0].reset();
                                        modalRevNotes.find('[name="rev_note_id"]').val('');
                                        $('#responseMessage').addClass('d-none');
                                    }, 500);
                                } else {
                                    // Show error message for status update
                                    responseMessage
                                        .removeClass('d-none alert-success')
                                        .addClass('alert-danger')
                                        .html(`<i class="bi bi-exclamation-circle-fill"></i> ${statusResponse.message || 'Gagal mengubah status dokumen'}`);
                                }
                            },
                            error: function(xhr, status, error) {
                                // Show error message for status update
                                responseMessage
                                    .removeClass('d-none alert-success')
                                    .addClass('alert-danger')
                                    .html(`<i class="bi bi-exclamation-circle-fill"></i> Error: ${xhr.statusText || 'Terjadi kesalahan jaringan saat mengubah status'}`);
                            },
                            complete: function() {
                                $dropdown.prop('disabled', false);
                            }
                        });
                    } else {
                        // Show error message for notes update
                        responseMessage
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger')
                            .html(`<i class="bi bi-exclamation-circle-fill"></i> ${response.message || 'Terjadi kesalahan'}`);
                    }
                },
                error: function(xhr, status, error) {
                    const responseMessage = $('#responseMessage');
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

        $(document).on('click', '#req-notes-btn', function(e) {
            // Hide any previous response messages
            $('#responseMessage').addClass('d-none');
            
            // Show loading state
            const buttonWrapper = $('#req-notes-btn-wrapper');
            const originalText = buttonWrapper.html();
            buttonWrapper.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

            // Submit via AJAX
            $.ajax({
                url: '{{ url("api/permit/revision_notes/update") }}',
                type: 'POST',
                data: {
                    req_id: '{{ $request->req_id }}',
                    revision_notes: $('#request_revision_notes').val()
                },
                // processData: false,
                // contentType: false,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const responseMessage = $('#responseMessage');

                    if (response.status == 0) {
                        // Show success message
                        responseMessage
                            .removeClass('d-none alert-danger')
                            .addClass('alert-success')
                            .html('<i class="bi bi-check-circle-fill"></i> Catatan revisi berhasil disimpan!');
                    } else {
                        // Show error message
                        Modal.showError(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr, status, error) {
                    const responseMessage = $('#responseMessage');
                    responseMessage.removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .html(`<i class="bi bi-exclamation-circle-fill"></i> Error: ${xhr.statusText || 'Terjadi kesalahan jaringan'}`);
                        
                    Utils.ajaxErrorHandler(xhr, status, error);
                },
                complete: function() {
                    // Re-enable submit button
                    buttonWrapper.prop('disabled', false).html(originalText);
                }
            });
        });

        function renderProcessButton(){
            let allVerified = true;
            let hasRevision = false;

            // Check each document status
            $('.verify-status').each(function() {
                const status = $(this).text().trim();
                if (status !== 'verified') {
                    allVerified = false;
                    if (status === 'revision') {
                        hasRevision = true;
                    }
                }
            });
            
            // Update status badge in header
            let overallStatus = 'pending';
            let overallBadgeClass = 'bg-light';
            
            if (allVerified) {
                overallStatus = 'verified';
                overallBadgeClass = 'bg-success';
            } else if (hasRevision) {
                overallStatus = 'revision needed';
                overallBadgeClass = 'bg-warning';
            }
            
            $('#statusBadge')
                .text(overallStatus)
                .removeClass('bg-light bg-success bg-warning bg-secondary bg-info')
                .addClass(overallBadgeClass);
            
            // Enable/disable the verify button based on the status
            if (allVerified) {
                $('#process-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
            } else {
                $('#process-btn').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
            }
        }

        $(document).on('click', '#process-btn', function(e) {
            // Hide any previous response messages
            $('#responseMessage').addClass('d-none');
            
            // Show loading state
            const buttonWrapper = $('#process-btn-wrapper');
            const originalText = buttonWrapper.html();
            buttonWrapper.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

            // Submit via AJAX
            $.ajax({
                url: '{{ url("api/permit/request_update") }}',
                type: 'POST',
                data: {
                    req_id: '{{ $request->req_id }}',
                    status: '{{ $mode==1? TrxRequestApproval::STATUS_APPROVED : TrxRequest::STATUS_VERIFIED }}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const successMessage = '{{ $mode==1? "Approval Berhasil" : "Verifikasi Selesai" }}';
                    const redirectUrl = '{{ url( "permit/".($mode==1? "approval_list": "verify_list") ) }}';

                    if (response.status == 0) {
                        Modal.showSuccess(successMessage, ()=>{
                            // window.location.replace = redirectUrl;
                            window.location.replace(redirectUrl)
                        });
                    } else {
                        // Show error message
                        Modal.showError(response.message || 'Terjadi kesalahan');
                    }
                },
                error: Utils.ajaxErrorHandler,
                complete: function() {
                    // Re-enable submit button
                    buttonWrapper.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
@endpush
