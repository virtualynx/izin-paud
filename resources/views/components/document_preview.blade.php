
@push('styles')
    {{-- Modal of PDF Preview --}}
    <style>
        /* Add to your stylesheet */
        #pdfModal .modal-body {
            padding: 20px;
            overflow: hidden; /* Prevents scrollbar flash during resize */
        }

        #pdf-viewer-container {
            display: flex;
            flex-direction: column;
            /*height: calc(100vh - 200px);*/ /* Adjust based on modal header/footer */
        }

        #pdf-canvas {
            max-width: 100%;
            margin: 0 auto;
            border: 1px solid #dee2e6;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        /* Ensure footer contents are properly aligned */
        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        #pdf-loading-spinner {
            transition: opacity 0.3s ease;
        }
        
        /* Download Button */
        /* Optional: Fix download button position */
        #pdf-download-btn {
            margin-left: auto; /* Pushes to far right */
            min-width: 150px; /* Prevent resizing */
        }

        /* For spinner customization */
        .spinner-border {
            --bs-spinner-width: 3rem;
            --bs-spinner-height: 3rem;
            --bs-spinner-vertical-align: -0.65em;
        }
    </style>

    <style>
        .label-width {
            width: 20% !important;
            min-width: 130px; /* Adjust as needed */
            white-space: nowrap; /* Prevent text wrapping */
        }
    </style>

    <!-- PDF.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf_viewer.min.css" />
@endpush

<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1">
    <div class="modal-dialog w-auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalTitle">PDF Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="pdf-modal-body" style="overflow: auto;">
                <!-- Loading Spinner -->
                <div id="pdf-loading-spinner" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading PDF...</p>
                </div>

                <!-- Parent Doc Link -->
                <div id="parent_doc_link_div" class="d-none text-center">
                    Dasar Surat: &nbsp;
                    <a id="parent_doc_link" href="" target="_blank">
                        <span id="parent_doc_link_text"></span>
                    </a>
                </div>

                <!-- Image Viewer -->
                <div id="image-viewer-container" style="display: none; text-align: center;">
                    <img id="image-preview" style="max-width: 100%; max-height: 80vh;" class="img-fluid">
                </div>

                <!-- PDF Viewer -->
                <div id="pdf-viewer-container" style="display: none;">
                    <canvas id="pdf-canvas"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <!-- PDF Navigation -->
                <div id="pdf-navigation" class="me-auto" style="display: none;">
                    <button id="prev-page" class="btn btn-secondary">Previous</button>
                    <span id="page-num" class="mx-2">Page: 1</span>
                    <button id="next-page" class="btn btn-secondary">Next</button>
                    <span id="page-count" class="mx-2">/ 1</span>
                </div>
                
                <!-- Download button -->
                <a id="pdf-download-btn" class="btn btn-success" download>
                    <i class="fas fa-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

{{-- PDF.js Scripts --}}
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize PDF.js
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

            // Viewer Variables
            let pdfDoc = null, pageNum = 1, pageRendering = false, pageNumPending = null;
            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const imageElement = document.getElementById('image-preview');
            const pdfModal = new bootstrap.Modal('#pdfModal');
            let currentRenderTask = null;
            let resizeObserver = null;

            // Supported image extensions
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

            // Handle Preview Button Clicks
            $('#table_documents').on('click', '.preview-pdf', function() {
                const fileUrl = $(this).data('url');
                const fileExt = fileUrl.split('.').pop().toLowerCase();
                
                if (imageExtensions.includes(fileExt)) {
                    showImage(fileUrl);
                } else {
                    loadAndRenderPdf(fileUrl);
                }

                const parentUrl = $(this).data('parent_url');
                if(typeof parentUrl === 'string' && parentUrl.trim().length > 0){
                    $('#parent_doc_link_div').removeClass('d-none').addClass('d-block');
                    $('#parent_doc_link_div').find('#parent_doc_link').attr('href', parentUrl);
                    $('#parent_doc_link_div').find('#parent_doc_link_text').html($(this).data('parent_doc_number'));
                }else{
                    $('#parent_doc_link_div').removeClass('d-block').addClass('d-none');
                    $('#parent_doc_link_div').find('#parent_doc_link').attr('href', '');
                    $('#parent_doc_link_div').find('#parent_doc_link_text').html('');
                }

                pdfModal.show();
            });

            // Show Image Function
            function showImage(url) {
                // Hide PDF viewer, show image viewer
                $('#pdf-viewer-container').removeClass('d-flex justify-content-center');
                document.getElementById('pdf-viewer-container').style.display = 'none';
                document.getElementById('image-viewer-container').style.display = 'block';
                document.getElementById('pdf-loading-spinner').style.display = 'none';
                
                // Hide PDF navigation controls
                document.getElementById('pdf-navigation').style.display = 'none';
                
                // Set image source
                imageElement.src = url;
                document.getElementById('pdfModalTitle').textContent = `Previewing: ${url.split('/').pop()}`;
                
                // Set up download button for image
                const downloadBtn = document.getElementById('pdf-download-btn');
                const filename = url.split('/').pop() || 'image.jpg';
                downloadBtn.setAttribute('download', filename);
                downloadBtn.setAttribute('href', url);
            }

            // PDF Loading and Rendering
            function loadAndRenderPdf(url) {
                // Hide image viewer, show PDF viewer
                document.getElementById('image-viewer-container').style.display = 'none';
                $('#pdf-viewer-container').addClass('d-flex justify-content-center');
                document.getElementById('pdf-viewer-container').style.display = 'block';
                document.getElementById('pdf-navigation').style.display = 'flex';
                
                // Reset canvas to clear any previous content
                canvas.width = 0;
                canvas.height = 0;
                
                // Cancel any ongoing rendering
                if (currentRenderTask) {
                    currentRenderTask.cancel();
                    currentRenderTask = null;
                }

                // Show spinner, hide viewer initially
                document.getElementById('pdf-loading-spinner').style.display = 'block';
                document.getElementById('pdf-viewer-container').style.display = 'none';

                // Reset state
                pageNum = 1;
                pageRendering = false;
                pageNumPending = null;
                document.getElementById('page-num').textContent = 'Page: 1';
                
                $('#pdfModal').one('shown.bs.modal', function() { // Wait for modal to be fully shown before loading PDF
                    // Load PDF
                    pdfjsLib.getDocument(url).promise.then(pdf => {
                        pdfDoc = pdf;
                        document.getElementById('pdfModalTitle').textContent = `Previewing: ${url.split('/').pop()}`;
                        document.getElementById('page-count').textContent = `/ ${pdf.numPages}`;

                        // Hide spinner, show viewer when ready to render
                        document.getElementById('pdf-loading-spinner').style.display = 'none';
                        document.getElementById('pdf-viewer-container').style.display = 'block';

                        // Initialize resize observer if not already done
                        if (!resizeObserver) {
                            resizeObserver = new ResizeObserver(() => {
                                if (pdfDoc && pdfModal._element.classList.contains('show')) {
                                    renderPage(pageNum);
                                }
                            });
                            resizeObserver.observe(document.getElementById('pdfModal'));
                        }

                        // Small delay to ensure modal is fully visible before rendering
                        setTimeout(() => {
                            renderPage(1);
                        }, 50);
                    })
                    .catch(err => {
                        console.error('PDF error:', err);
                        document.getElementById('pdf-loading-spinner').innerHTML = `
                            <div class="alert alert-danger">
                            Failed to load PDF. <button class="btn btn-sm btn-link" onclick="window.location.reload()">Retry</button>
                            </div>
                        `;
                    });

                    // Set up download button for PDF
                    const downloadBtn = document.getElementById('pdf-download-btn');
                    const filename = url.split('/').pop() || 'document.pdf';
                    downloadBtn.setAttribute('download', filename);
                    downloadBtn.setAttribute('href', url);
                });
            }

            // Page Rendering (for PDFs)
            function renderPage(num) {
                if (!pdfDoc || pageRendering) {
                    pageNumPending = num;
                    return;
                }

                pageRendering = true;
                pageNumPending = null;

                // Optional: Show mini-spinner during page render
                const pageNumElement = document.getElementById('page-num');
                pageNumElement.innerHTML = `Page: ${num} <span class="spinner-border spinner-border-sm ms-2"></span>`;

                pdfDoc.getPage(num).then(page => {
                    const modalBody = document.querySelector('#pdfModal .modal-body');
                    const modalContent = document.querySelector('#pdfModal .modal-content');

                    // Calculate available space more accurately
                    const maxWidth = modalContent.clientWidth - 80; // Account for padding
                    const maxHeight = window.innerHeight * 0.7; // Use 70% of viewport height

                    const viewport = page.getViewport({ scale: 1 });
                    const scale = Math.min(
                        maxWidth / viewport.width,
                        maxHeight / viewport.height
                    ) * window.devicePixelRatio;

                    const scaledViewport = page.getViewport({ scale });

                    // Ensure canvas is properly sized
                    canvas.height = scaledViewport.height;
                    canvas.width = scaledViewport.width;

                    // pdf-viewer-container
                    let pdfContainer = document.querySelector('#pdf-viewer-container');
                    if(canvas.width >= canvas.height){
                        pdfContainer.style.height = "auto";
                        pdfContainer.style.maxHeight = "70vh";
                    }else{
                        pdfContainer.style.height = "auto";
                        pdfContainer.style.maxHeight = "80vh";
                    }

                    // Clear any previous content
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    // Cancel any previous render task
                    if (currentRenderTask) {
                        currentRenderTask.cancel();
                    }

                    // Store the current render task
                    currentRenderTask = page.render({
                        canvasContext: ctx,
                        viewport: scaledViewport
                    });

                    return currentRenderTask.promise.then(() => {
                        pageRendering = false;
                        pageNum = num;
                        document.getElementById('page-num').textContent = `Page: ${num}`;
                        
                        // Render pending page if requested during rendering
                        if (pageNumPending !== null) {
                            renderPage(pageNumPending);
                        }
                    });
                }).catch(err => {
                    console.error('Page render error:', err);
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                    }
                });
            }

            // Navigation Controls (for PDFs)
            document.getElementById('prev-page').addEventListener('click', () => {
                if (pageNum <= 1 || pageRendering) return;
                renderPage(pageNum - 1);
            });

            document.getElementById('next-page').addEventListener('click', () => {
                if (!pdfDoc || pageNum >= pdfDoc.numPages || pageRendering) return;
                renderPage(pageNum + 1);
            });

            // Cleanup when modal hides
            document.getElementById('pdfModal').addEventListener('hidden.bs.modal', () => {
                if (resizeObserver) {
                    resizeObserver.disconnect();
                    resizeObserver = null;
                }
                
                // Cancel any ongoing rendering when modal is closed
                if (currentRenderTask) {
                    currentRenderTask.cancel();
                    currentRenderTask = null;
                }
                
                // Clear the canvas
                if (ctx) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
                
                // Reset PDF document
                pdfDoc = null;
                
                // Clear image source
                imageElement.src = '';
            });
        });
    </script>
@endpush