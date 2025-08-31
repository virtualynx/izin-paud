@push('head-stacks')
    <style>
        /* FilePond container styling */
        .filepond--root {
            margin-bottom: 0;
        }

        .filepond--panel-root {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
        }

        .filepond--drop-label {
            color: #6c757d;
            border-radius: 0.375rem;
        }

        .filepond--list-scroller {
            margin-top: 10px;
        }

        /* Ensure proper positioning of preview items */
        .filepond--item {
            width: calc(50% - 0.5em);
        }

        @media (min-width: 576px) {
            .filepond--item {
                width: calc(33.33% - 0.5em);
            }
        }
    </style>

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <!-- Load FilePond plugins -->
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    
    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>

    <script>
        // $(document).ready(function() {
        //     $.fn.filepond.registerPlugin(FilePondPluginFileValidateSize);
        //     $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);
        //     $.fn.filepond.registerPlugin(FilePondPluginImageExifOrientation);
        //     $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
        // });
        
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateSize);
        $.fn.filepond.registerPlugin(FilePondPluginFileValidateType);
        $.fn.filepond.registerPlugin(FilePondPluginImageExifOrientation);
        $.fn.filepond.registerPlugin(FilePondPluginImagePreview);
    </script>
@endpush