@php
    use App\Models\TrxRequest;
    use App\Models\TrxRequestDocument;
    use App\Models\TrxRequestApproval;
    use App\Http\Api\PermitApi;
@endphp

@extends('app')

@php
    $page_title = 'Revisi Pengajuan: '.$request->name;
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
        
        /* Style for editable fields */
        .editable-field {
            border: 1px dashed #ccc;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            min-height: 30px;
        }
        
        .editable-field:hover {
            background-color: #f8f9fa;
            border-color: #007bff;
        }
        
        .edit-mode {
            border: 1px solid #007bff;
            background-color: #f8f9fa;
        }
        
        /* File upload styling */
        .file-upload-container {
            margin-top: 10px;
        }
        
        .file-preview {
            max-width: 100%;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
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
                    <!-- Informasi Dasar Pengajuan (Editable) -->
                    <div class="row mb-4 border">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Informasi Pengajuan</h5>
                            <div class="button-group">
                                <button class="edit-btn btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit Informasi
                                </button>
                                <button class="save-btn btn btn-sm btn-success" style="display: none;">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                                <button class="cancel-btn btn btn-sm btn-danger" style="display: none;">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="section-title">Informasi Lembaga</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="label-width fw-bold">Nama Lembaga:</td>
                                            <td>
                                                <div class="editable-field" data-field="name" data-original="{{ $request->name }}">
                                                    {{ $request->name }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-width fw-bold">Bentuk Lembaga:</td>
                                            <td>
                                                <div class="editable-field" data-field="foundation_type" data-original="{{ $request->foundation_type }}">
                                                    {{ $request->foundation_type }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-width fw-bold">Alamat:</td>
                                            <td>
                                                <div class="editable-field" data-field="address" data-original="{{ $request->address }}">
                                                    {{ $request->address }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-width fw-bold">Tahun Berdiri:</td>
                                            <td>
                                                <div class="editable-field" data-field="founded_year" data-original="{{ $request->founded_year }}">
                                                    {{ $request->founded_year }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-title">Data Penanggung Jawab</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="label-width fw-bold">Nama:</td>
                                            <td>
                                                <div class="editable-field" data-field="pic_name" data-original="{{ $request->pic_name }}">
                                                    {{ $request->pic_name }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-width fw-bold">Email:</td>
                                            <td>
                                                <div class="editable-field" data-field="email" data-original="{{ $request->email }}">
                                                    {{ $request->email }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-width fw-bold">Telepon:</td>
                                            <td>
                                                <div class="editable-field" data-field="phone" data-original="{{ $request->phone }}">
                                                    {{ $request->phone }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen yang Perlu Direvisi -->
                    <div class="row mb-2 border">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Dokumen yang Perlu Revisi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table_documents" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Jenis Dokumen</th>
                                            <th width="30%">Catatan Revisi</th>
                                            <th width="25%">File pada Server</th>
                                            <th width="15%">Revisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loaded via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Button Process -->
                    <div class="row mt-4">
                        <div id="process-btn-wrapper" class="col-md-12 d-flex justify-content-end gap-2 mt-3">
                            <button id="process-btn" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Selesai Revisi
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
        let isEditMode = false;
        let originalData = {};
        const editedData = {};

        $(document).ready(function() {
            // Toggle edit mode for information fields
            $('.edit-btn').on('click', function() {
                enableEditMode();
            });
            
            $('.cancel-btn').on('click', function() {
                disableEditMode();
            });
            
            $('.save-btn').on('click', function() {
                saveInformationChanges();
            });

            renderRevisionDocuments();
        });
 
        // Enable edit mode function
        function enableEditMode() {
            if (isEditMode) return;
            
            isEditMode = true;
            
            // Store original values
            $('.editable-field').each(function() {
                const field = $(this).data('field');
                originalData[field] = $(this).data('original');
                
                // Convert to input fields
                const currentValue = $(this).text().trim();
                $(this).addClass('edit-mode');
                
                if (field === 'founded_year') {
                    $(this).html(`<input type="number" class="form-control form-control-sm" value="${currentValue}" min="1900" max="${new Date().getFullYear()}">`);
                } else {
                    $(this).html(`<input type="text" class="form-control form-control-sm" value="${currentValue}">`);
                }
            });
            
            // Show/hide buttons
            $('.edit-btn').hide();
            $('.save-btn, .cancel-btn').show();

            // disable process-button
            $('#process-btn').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
        }
        
        // Disable edit mode function
        function disableEditMode() {
            if (!isEditMode) return;
            
            isEditMode = false;
            
            // Revert to original values
            $('.editable-field').each(function() {
                const field = $(this).data('field');
                $(this).removeClass('edit-mode').text(originalData[field]);
            });
            
            // Clear edited data
            Object.keys(editedData).forEach(key => delete editedData[key]);
            
            // Show/hide buttons
            $('.edit-btn').show();
            $('.save-btn, .cancel-btn').hide();

            // enable process-button
            $('#process-btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
        }
        
        // Save information changes
        function saveInformationChanges() {
            // Collect edited values
            $('.editable-field').each(function() {
                const field = $(this).data('field');
                const value = $(this).find('input').val();
                
                if (value !== originalData[field]) {
                    editedData[field] = value;
                }
            });
            
            if (Object.keys(editedData).length === 0) {
                Modal.showInfo('Tidak ada perubahan yang disimpan.');
                disableEditMode();
                return;
            }
            
            // Show loading
            Loading.show('Menyimpan perubahan...');
            
            // Send AJAX request
            $.ajax({
                url: '{{ url("api/permit/request_update") }}',
                type: 'POST',
                data: {
                    req_id: '{{ $request->req_id }}',
                    ...editedData
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 0) {
                        Modal.showSuccess('Perubahan Informasi berhasil.', function() {
                            // Update original values
                            Object.keys(editedData).forEach(field => {
                                originalData[field] = editedData[field];
                                $(`[data-field="${field}"]`).data('original', editedData[field]);
                            });
                            
                            // Clear edited data
                            Object.keys(editedData).forEach(key => delete editedData[key]);
                            
                            disableEditMode();
                            
                            // Optionally reload page to reflect changes
                            // window.location.reload();
                        });
                    } else {
                        console.log(response.message);
                        Modal.showError('Terjadi kesalahan saat menyimpan perubahan.');
                    }
                },
                error: function(xhr, status, error) {
                    Utils.ajaxErrorHandler(xhr, status, error);
                },
                complete: () => {
                    Loading.hide();
                }
            });
        }

        function renderRevisionDocuments() {
            const tbody = $('#table_documents tbody');

            $.ajax({
                url: '{{ url("api/permit/revision_documents/list")."/".$request->req_id }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: () => {
                    Loading.tableLoading(tbody, 5, 'Memuat dokumen revisi...');
                },
                success: function(response) {
                    if (response.status == 0) {
                        tbody.empty();
                        
                        if (response.data.length === 0) {
                            Loading.tableLoadingEmpty(tbody, 5, 'Tidak ada dokumen yang perlu direvisi');
                            return;
                        }

                        response.data.forEach((doc, index) => {
                            let acceptedFiles = '';

                            if (doc.mime.startsWith('image')) {
                                acceptedFiles = '.jpg,.jpeg,.png';
                            } else {
                                acceptedFiles = '.pdf';
                            }

                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${doc.doctype.name} ${doc.doctype.is_optional ? '<span class="text-muted">(opsional)</span>' : '<span class="text-danger">*wajib</span>'}</td>
                                    <td>${doc.revision_note ? doc.revision_note.notes : '<span class="text-muted">-</span>'}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="mb-2 filename">${doc.filename}</span>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary preview-pdf ms-2" 
                                                data-req_doc_id="${doc.req_doc_id}"
                                                data-filename="${doc.filename}"
                                                data-mime="${doc.mime}"
                                                data-url="{{ url('permit/document/preview') }}/${doc.req_doc_id}"
                                            >
                                                <i class="bi bi-eye"></i> Lihat File
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="file-upload-container">
                                            <label class="btn btn-sm btn-outline-primary w-100 file-upload-label">
                                                <i class="bi bi-upload"></i> Unggah Revisi
                                                <input type="file" 
                                                    class="d-none file-upload-input" 
                                                    data-req_doc_id="${doc.req_doc_id}"
                                                    accept="${acceptedFiles}">
                                            </label>
                                            <div class="file-preview-container mt-2" style="display: none;">
                                                <img class="file-preview" src="" alt="Preview">
                                                <div class="mt-1">
                                                    <button type="button" class="btn btn-sm btn-danger remove-file">Hapus</button>
                                                </div>
                                            </div>
                                            <div class="file-name-container mt-2" style="display: none;">
                                                <div class="alert alert-success p-2">
                                                    <span class="filename"></span> (Terupload)
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        Loading.tableLoadingError(tbody, 5, response.message);
                    }
                },
                error: Utils.ajaxErrorHandler
            });
        }

        // Handle file upload changes
        $(document).on('change', '.file-upload-input', function(e) {
            const reqDocId = $(this).data('req_doc_id');
            const file = this.files[0];
            const $container = $(this).closest('.file-upload-container');
            const $label = $container.find('.file-upload-label');

            const originalLabel = $label.html();

            if (file) {
                // Show loading state
                $label.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Mengunggah...');
                
                const formData = new FormData();
                formData.append('req_doc_id', reqDocId);
                formData.append('file', file);
                // formData.append('verify_status', 'pending');
                
                // Upload file immediately
                $.ajax({
                    url: '{{ url("api/permit/reqdoc_update") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 0) {
                            // // Show success
                            // $container.find('.file-name-container').show()
                            //     .find('.filename').text(file.name);
                            
                            // // Update filename in file list
                            // $(`[data-req_doc_id="${reqDocId}"]`).closest('tr').find('.filename').text(file.name);
                            // $(`[data-req_doc_id="${reqDocId}"]`).closest('tr').find('.preview-pdf').data('filename', file.name);
                            
                            // // Modal.showSuccess('File revisi berhasil diunggah');

                            // Show success
                            $container.find('.file-name-container').show()
                                .find('.filename').text(file.name);
                            
                            // Update filename in file list AND update the preview button data
                            const $row = $(`[data-req_doc_id="${reqDocId}"]`).closest('tr');
                            $row.find('.filename').text(file.name);
                            
                            // Update the preview button data attributes
                            const $previewBtn = $row.find('.preview-pdf');
                            $previewBtn.data('filename', file.name);
                            
                            // For images, we also need to update the URL to prevent caching
                            const newUrl = '{{ url("permit/document/preview") }}/' + reqDocId + '?t=' + new Date().getTime();
                            $previewBtn.data('url', newUrl);
                        } else {
                            Modal.showError(response.message || 'Gagal mengunggah file');
                            $(this).val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        Modal.showError('Error mengunggah file');
                        Utils.ajaxErrorHandler(xhr, status, error);
                    },
                    complete: function() {
                        $label.prop('disabled', false).html(originalLabel);
                        $(this).val('');
                    }
                });
            }
        });

        // Process button click (submit all revisions)
        $(document).on('click', '#process-btn', function(e) {
            Loading.show('Menyelesaikan revisi...');
            
            // Submit via AJAX
            $.ajax({
                url: '{{ url("api/permit/request_update") }}',
                type: 'POST',
                data: {
                    req_id: '{{ $request->req_id }}',
                    mode: '{{ PermitApi::REQUEST_UPDATE_MODE_SUBMIT }}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Loading.hide();
                    
                    let redirectUrl='';
                    @if (is_verificator() || is_approver())
                        redirectUrl = '{{ url("permit/verify_list") }}';
                    @else
                        redirectUrl = '{{ url("permit/request_list") }}';
                    @endif
                    
                    if (response.status == 0) {
                        Modal.showSuccess('Revisi berhasil diselesaikan', function() {
                            window.location.replace(redirectUrl);
                        });
                    } else {
                        Modal.showError(response.message || 'Terjadi kesalahan');
                    }
                },
                beforeSend: ()=>{

                },
                complete: ()=>{

                },
                error: function(xhr, status, error) {
                    Loading.hide();
                    Utils.ajaxErrorHandler(xhr, status, error);
                }
            });
        });
    </script>
@endpush