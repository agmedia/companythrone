@pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/filepond@^4/dist/filepond.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.css">
@endPushOnce

@pushOnce('scripts')
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type@^1/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size@^2/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize
            );
            const input = document.querySelector('input[name="avatar_file"]');
            if (input) {
                FilePond.create(input, {
                    allowMultiple: false,
                    acceptedFileTypes: ['image/png','image/jpeg','image/webp'],
                    maxFileSize: '4MB',
                    storeAsFile: true // submit as normal form file
                });
            }
        });
    </script>
@endPushOnce
