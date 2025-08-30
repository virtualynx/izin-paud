@extends('app')

@section('title', config('app.name').'- Pengajuan')

@section('breadcrumbs', 'Pengajuan Izin')

@push('head-stacks')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet Locate Control CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.76.0/dist/L.Control.Locate.min.css" /> --}}
@endpush

@section('content')
    <style>
        .mandatory-tag {
            color: #dc3545;
            font-size: 0.8em;
            font-weight: 600;
            margin-left: 4px;
        }

        #mapContainer {
            margin-top: 15px;
            border: 1px solid #ddd;
        }

        .leaflet-control-locate a {
            background-color: #fff;
            border-radius: 4px;
        }

        #submitButton:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    {{-- Styles for Sarpras Photos --}}
    <style>
        .multiple-file-upload {
            position: relative;
        }

        .file-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .file-preview {
            position: relative;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview .file-name {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            font-size: 10px;
            padding: 2px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .file-preview .remove-file {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255,255,255,0.8);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 1;
            cursor: pointer;
        }
    </style>

    {{-- Single file upload styles --}}
    <style>
        /* Single file upload styles */
        .single-file-upload {
            position: relative;
        }

        .single-file-upload .file-preview-container {
            margin-bottom: 10px;
        }

        .single-file-upload .file-preview {
            width: 100%;
            height: 120px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }

        .single-file-upload .file-preview.empty {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .single-file-upload .file-preview.empty .placeholder {
            text-align: center;
            color: #6c757d;
        }

        .single-file-upload .file-preview.empty .placeholder i {
            font-size: 2rem;
            display: block;
            margin-bottom: 5px;
        }

        .single-file-upload .file-preview.filled {
            border: 1px solid #ced4da;
            padding: 5px;
        }

        .single-file-upload .file-preview img,
        .single-file-upload .file-preview .pdf-preview {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .single-file-upload .file-preview .pdf-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .single-file-upload .file-preview .pdf-preview i {
            font-size: 2.5rem;
            color: #dc3545;
            margin-bottom: 8px;
        }

        .single-file-upload .file-preview .pdf-preview .file-name {
            font-size: 0.8rem;
            text-align: center;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 5px;
        }

        .single-file-upload .file-preview .remove-file {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .single-file-upload .file-input {
            margin-top: 8px;
        }
    </style>

    <form id="request_form" enctype="multipart/form-data">
        @csrf

        {{-- Informations --}}
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Informasi Tentang PAUD</h4>
                    </div>

                    <div class="card-body">
                        <h5 class="section-title">Data Lembaga</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lembaga PAUD <span class="mandatory-tag">*</span></label>
                                <input name="name" type="text" class="form-control" placeholder="Nama lengkap lembaga" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bentuk Lembaga <span class="mandatory-tag">*</span></label>
                                <select name="foundation_type" class="form-select" required>
                                    <option value="">Pilih bentuk lembaga</option>
                                    <option value="yayasan">Yayasan</option>
                                    <option value="perusahaan">Perusahaan</option>
                                    <option value="perorangan">Perorangan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Telepon <span class="mandatory-tag">*</span></label>
                                <input name="phone" type="tel" class="form-control" placeholder="Nomor telepon yang dapat dihubungi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="mandatory-tag">*</span></label>
                                <input name="email" type="email" class="form-control" placeholder="Alamat email aktif" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Pilih titik lokasi PAUD <span class="mandatory-tag">*</span></label>
                                <div id="mapContainer" style="display: block; height: 300px; border-radius: 8px; overflow: hidden;">
                                    <div id="map" style="height: 100%;"></div>
                                </div>
                                
                                <input type="hidden" name="lat">
                                <input type="hidden" name="lng">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Lengkap <span class="mandatory-tag">*</span></label>
                                <textarea name="address" data-manual-input="false" class="form-control" rows="2" placeholder="Alamat lengkap lembaga" required></textarea>
                            </div>
                        </div>
                        
                        <h5 class="section-title mt-3">Data Pendiri/Penanggung Jawab</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pendiri/Penanggung Jawab <span class="mandatory-tag">*</span></label>
                                <input name="pic_name" type="text" class="form-control" placeholder="Nama lengkap pendiri" required>
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan <span class="mandatory-tag">*</span></label>
                                <input type="text" class="form-control" placeholder="Jabatan dalam lembaga" required>
                            </div> --}}
                        </div>
                        
                        <div class="row">
                            {{-- <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor HP/WhatsApp <span class="mandatory-tag">*</span></label>
                                <input type="tel" class="form-control" placeholder="Nomor yang dapat dihubungi" required>
                            </div> --}}
                        </div>
                        
                        <h5 class="section-title mt-3">Data Operasional</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Berdiri <span class="mandatory-tag">*</span></label>
                                <input name="founded_year" type="number" class="form-control" min="1900" max="2099" placeholder="Tahun" required>
                            </div>
                            {{-- <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Siswa Saat Ini</label>
                                <input type="number" class="form-control" min="0" placeholder="Jumlah siswa">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Guru/Tenaga Pengajar <span class="mandatory-tag">*</span></label>
                                <input type="number" class="form-control" min="1" placeholder="Jumlah guru" required>
                            </div> --}}
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Visi dan Misi Lembaga <span class="mandatory-tag">*</span></label>
                                <textarea name="vision_mission" class="form-control" rows="3" placeholder="Visi dan misi lembaga" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="row justify-content-center mt-2">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Upload Dokumen Persyaratan Izin Operasional PAUD</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Silakan upload dokumen persyaratan sesuai dengan daftar di bawah ini. Format file yang diterima: PDF, JPG, JPEG, PNG (Maks. 2MB per file)</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="60%">Persyaratan</th>
                                        <th width="35%">Upload Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    Saya menyatakan bahwa semua dokumen yang diupload adalah benar dan valid
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                {{-- <button type="reset" class="btn btn-secondary me-md-2">Reset</button> --}}
                                <button id="submitButton" type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

{{-- on page load --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: `{{ url('/api/permit/docrec/list') }}`,
                type: 'GET',
                success: function(res) {
                    console.log('res', res);
                    if(res.status == 0){
                        // Modal.show(`
                        //     <span class="text-success">res: ${res} !!</span>
                        // `);
                        
                        let tbody = $('#request_form').find('tbody');
                        let no = 1;
                        res.data.forEach(a => {
                            let mandatoryTag = a.is_optional == 0 ? '<span class="mandatory-tag">*wajib</span>' : '<span class="text-muted">(opsional)</span>';
                            let toBeAppended = '';
                            if(a.is_multiple_file){
                                toBeAppended = `
                                    <tr>
                                        <td>${no}</td>
                                        <td>${a.description} ${mandatoryTag}</td>
                                        <td>
                                            <div class="multiple-file-upload" data-doctype="${a.doctypereq_id}">
                                                <div class="file-preview-container"></div>
                                                <input 
                                                    type="file" 
                                                    class="form-control form-control-sm" 
                                                    name="${a.doctypereq_id}[]" 
                                                    accept=".pdf,.jpg,.jpeg,.png" 
                                                    multiple
                                                    ${a.is_optional == 0 ? 'required' : ''}
                                                >
                                                <small class="text-muted d-block mt-1">Maks. 2MB per file</small>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }else{
                                toBeAppended = `
                                    <tr>
                                        <td>${no}</td>
                                        <td>${a.description} ${mandatoryTag}</td>
                                        <td>
                                            <div class="single-file-upload" data-doctype="${a.doctypereq_id}">
                                                <div class="file-preview-container mb-2">
                                                    <div class="file-preview empty">
                                                        <div class="placeholder">
                                                            <i class="bi bi-file-earmark"></i>
                                                            <span>No file selected</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input 
                                                    type="file" 
                                                    class="form-control form-control-sm file-input" 
                                                    name="${a.doctypereq_id}" 
                                                    accept=".pdf,.jpg,.jpeg,.png" 
                                                    ${a.is_optional == 0 ? 'required' : ''}
                                                >
                                                <small class="text-muted">Maks. 2MB</small>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }

                            tbody.append(toBeAppended);
                            no++;
                        });
                    }else{
                        Modal.show(
                            `
                                <span class="text-danger">
                                    <div>${res.message}</div>
                                </span>
                            `,
                            `<span class="text-danger">Error</span>`
                        );
                    }
                },
                error: Utils.ajaxErrorHandler
            });

            // Handle agreeTerms checkbox change
            $('#agreeTerms').change(function() {
                const isChecked = this.checked;
                const submitButton = $('#submitButton');
                
                // Enable/disable button
                submitButton.prop('disabled', !isChecked);
                
                // Change visual appearance
                if (isChecked) {
                    submitButton.removeClass('btn-secondary').addClass('btn-primary');
                } else {
                    submitButton.removeClass('btn-primary').addClass('btn-secondary');
                }
                   
                // console.log(isChecked ? 'Checked' : 'Unchecked');
            });
            
            // Set initial state
            $('#agreeTerms').trigger('change');

            // prevent reverse geocoding fill adress after adress is manually inputted
            $(document).on('keyup', 'textarea[name="address"]', function() {
                $(this).data('manual-input', true);
            });

            // setInterval(() => {
            //     Utils.saveFormData('request_form');
            // }, 5 * 1000);
        });
    </script>
@endpush

{{-- Script for single-file upload preview --}}
@push('scripts')
    <script>
        // Handle single file uploads with preview
        $(document).on('change', '.single-file-upload input[type="file"]', function(e) {
            const container = $(this).closest('.single-file-upload');
            const previewContainer = container.find('.file-preview-container');
            const file = this.files[0];
            
            if (!file) return;
            
            // Check file size
            if (file.size > 2 * 1024 * 1024) {
                alert(`File ${file.name} melebihi ukuran maksimum 2MB`);
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                let previewHtml = '';
                
                if (file.type.startsWith('image/')) {
                    previewHtml = `
                        <div class="file-preview filled">
                            <img src="${e.target.result}" alt="${file.name}">
                            <button type="button" class="remove-file">&times;</button>
                        </div>
                    `;
                } else if (file.type === 'application/pdf') {
                    // For PDF files - show icon and filename
                    previewHtml = `
                        <div class="file-preview filled">
                            <div class="pdf-preview">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <div class="file-name">${file.name}</div>
                            </div>
                            <button type="button" class="remove-file">&times;</button>
                        </div>
                    `;
                } else {
                    // For other file types
                    previewHtml = `
                        <div class="file-preview filled">
                            <div class="pdf-preview">
                                <i class="bi bi-file-earmark"></i>
                                <div class="file-name">${file.name}</div>
                            </div>
                            <button type="button" class="remove-file">&times;</button>
                        </div>
                    `;
                }
                
                previewContainer.html(previewHtml);
            };
            reader.readAsDataURL(file);
        });

        // Remove file preview for single files
        $(document).on('click', '.single-file-upload .remove-file', function() {
            const container = $(this).closest('.single-file-upload');
            const fileInput = container.find('input[type="file"]');
            const previewContainer = container.find('.file-preview-container');
            
            // Reset file input
            fileInput.val('');
            
            // Reset to empty state
            previewContainer.html(`
                <div class="file-preview empty">
                    <div class="placeholder">
                        <i class="bi bi-file-earmark"></i>
                        <span>No file selected</span>
                    </div>
                </div>
            `);
        });
    </script>
@endpush

{{-- Multiple file upload --}}
@push('scripts')
    <script>
        // Handle multiple file uploads with preview
        $(document).on('change', '.multiple-file-upload input[type="file"]', function(e) {
            const container = $(this).closest('.multiple-file-upload');
            const previewContainer = container.find('.file-preview-container');
            const files = this.files;
            
            if (!files || files.length === 0) return;
            
            // Clear existing previews
            previewContainer.empty();
            
            // Process each file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file size
                if (file.size > 2 * 1024 * 1024) {
                    alert(`File ${file.name} melebihi ukuran maksimum 2MB`);
                    continue;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewHtml = '';
                    
                    if (file.type.startsWith('image/')) {
                        previewHtml = `
                            <div class="file-preview">
                                <img src="${e.target.result}" alt="${file.name}">
                                <div class="file-name">${file.name}</div>
                                <button type="button" class="remove-file">&times;</button>
                            </div>
                        `;
                    } else if (file.type === 'application/pdf') {
                        previewHtml = `
                            <div class="file-preview">
                                <div class="pdf-preview">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>
                                <div class="file-name">${file.name}</div>
                                <button type="button" class="remove-file">&times;</button>
                            </div>
                        `;
                    } else {
                        previewHtml = `
                            <div class="file-preview">
                                <div class="pdf-preview">
                                    <i class="bi bi-file-earmark"></i>
                                </div>
                                <div class="file-name">${file.name}</div>
                                <button type="button" class="remove-file">&times;</button>
                            </div>
                        `;
                    }
                    
                    previewContainer.append(previewHtml);
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove file preview for multiple files
        $(document).on('click', '.multiple-file-upload .remove-file', function() {
            const filePreview = $(this).closest('.file-preview');
            const container = filePreview.closest('.multiple-file-upload');
            const fileInput = container.find('input[type="file"]');
            
            // Get the index of the file to remove
            const index = filePreview.index();
            
            // Create a new FileList without the removed file
            const dt = new DataTransfer();
            const files = fileInput[0].files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            fileInput[0].files = dt.files;
            
            // Remove the preview
            filePreview.remove();
            
            // Trigger change event to update the input
            fileInput.trigger('change');
        });
    </script>
@endpush

{{-- OSM map --}}
@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet Locate Control JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.76.0/dist/L.Control.Locate.min.js"></script> --}}

    <script>
        // Add this script after your existing document ready function
        let map, marker;

        $(document).ready(function() {
            initMap();

            // setTimeout(function() {
            //     if(marker){
            //         map.setView(marker.getLatLng(), 15);
            //     }
            // }, 300);
        });

        function initMap() {
            // Default location (you can set to user's current location or a default)
            const defaultLat = -6.6027;
            const defaultLng = 106.7653;
            
            // Initialize map
            map = L.map('map').setView([defaultLat, defaultLng], 15);
            // map = L.map('map');
            
            // Add OSM tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add marker
            // marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            
            // Set initial coordinates
            // $('#latitude').val(defaultLat);
            // $('#longitude').val(defaultLng);
            
            // Update marker position when map is clicked
            map.on('click', async function(event) {
                const {lat, lng} = event.latlng;

                if(!marker){
                    initMarker(lat, lng);
                }

                marker.setLatLng([lat, lng]);
                $('[name="lat"]').val(lat);
                $('[name="lng"]').val(lng);

                // Show loading state in address field
                // $('textarea[name="address"]').val('Mendapatkan alamat...');

                // Get address from coordinates
                // const address = await getAddressFromCoordinates(lat, lng);
                // if (address) {
                //     $('textarea[name="address"]').val(address);
                // } else {
                //     $('textarea[name="address"]').val('Alamat tidak dapat ditemukan. Silakan ketik manual.');
                // }

                // Get address when marker is dragged
                const address = await getAddressFromCoordinates(lat, lng);
            });
            
            // Add locate control
            // const locateControl = L.control.locate({
            //     position: 'topright',
            //     strings: {
            //         title: "Tunjukkan lokasi saya"
            //     }
            // }).addTo(map);
        }

        function initMarker(lat, lng){
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                    
            // Update coordinates when marker is dragged
            marker.on('dragend', async function(event) {
                const position = marker.getLatLng();
                $('[name="lat"]').val(position.lat);
                $('[name="lng"]').val(position.lng);

                // Show loading state in address field
                $('textarea[name="address"]').val('Mendapatkan alamat...');
                
                // Get address when marker is dragged
                const address = await getAddressFromCoordinates(position.lat, position.lng);
            });
        }

        // Function to get address from coordinates using Nominatim (OSM)
        async function getAddressFromCoordinates(lat, lng) {
            if($('textarea[name="address"]').data('manual-input') == true)return;

            try {
                // Show loading state in address field
                $('textarea[name="address"]').val('Mendapatkan alamat...');

                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    $('textarea[name="address"]').val(data.display_name);

                    return data.display_name;
                }else{
                    $('textarea[name="address"]').val('Alamat tidak dapat ditemukan. Silakan ketik manual.');
                }

                return null;
            } catch (error) {
                console.error('Error getting address:', error);
                $('textarea[name="address"]').val('Error getting address');
                return null;
            }
        }
    </script>
@endpush

{{-- form submit event --}}
@push('scripts')
    <script>
        $('#request_form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            // Validate that location is selected
            if (!$('[name="lat"]').val() || !$('[name="lng"]').val()) {
                alert('Harap pilih lokasi di peta.');

                // Scroll to the map container
                $('html, body').animate({
                    scrollTop: $('#mapContainer').offset().top - 100 // Adjust offset as needed
                }, 500);

                // Highlight the map container
                $('#mapContainer').css('border', '2px solid red');
                
                // Remove highlight after 3 seconds
                setTimeout(function() {
                    $('#mapContainer').css('border', '1px solid #ddd');
                }, 3000);

                return;
            }
            
            // Create FormData object
            let formData = new FormData(this);

            $('[type="file"].file-input-hidden').css('display', 'block');
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Mengirim...
            `);
            
            // Submit via AJAX
            $.ajax({
                url: '{{ url("api/permit/request_submit") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Modal.show(
                            `<div class="text-center">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">Pengajuan Berhasil!</h4>
                                <p>${response.message}</p>
                                <p>Nomor Registrasi: <strong>${response.registration_number || 'N/A'}</strong></p>
                            </div>`,
                            'Sukses',
                            {
                                label: 'Tutup', 
                                callback: function() {
                                    // Redirect or reset form
                                    window.location.href = response.redirect_url || '/';
                                }
                            }
                        );
                    } else {
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
                error: Utils.ajaxErrorHandler,
                complete: function() {
                    // Restore button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
@endpush