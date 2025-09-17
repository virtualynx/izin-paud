<script>
    window.Utils = {
        ajaxErrorHandler: function(xhr, status, error) {
            let errorMessage = `Error ${xhr.status}: `;
            errorMessage += xhr.statusText || 'Unknown error';
            
            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || response.error || errorMessage;
            } catch (e) {
                // Not JSON response
            }
            
            if(window.Modal){
                window.Modal.showError(errorMessage || 'Terjadi kesalahan');
            }else{
                console.error('AJAX Error:', xhr.status, error);
                console.log(errorMessage);
            }
        },
        showBsModal: function(cssSelector){
            const jqModal = $(cssSelector);

            // Clean up any existing modal instance
            const existingModal = bootstrap.Modal.getInstance(jqModal[0]);
            if (existingModal) {
                existingModal.hide();
            }

            const modal = new bootstrap.Modal(jqModal[0], {
                backdrop: 'static' // This prevents closing when clicking outside
            });
            
            // Handle hidden event to clean up
            jqModal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
                window.Utils.hideBsModal(cssSelector);
            });
            
            modal.show();
        },
        hideBsModal: function(cssSelector){
            const modal = bootstrap.Modal.getInstance(document.querySelector(cssSelector));
            if (modal) {
                modal.hide();
            }
            
            // Remove backdrop if it exists
            $('.modal-backdrop').remove();
            
            // Reset body styles
            $('body').css({
                'overflow': '',
                'padding-right': ''
            }).removeClass('modal-open');
        },
        saveFormData: function(formId){
            const inputs = $('#'+formId).find('input');

            let datas = {};
            Object.entries(inputs).forEach(([key, value]) => {
                let el = $(value);
                if(el.prop('name') && el.prop('name').trim() !== '' && el.prop('type') != 'file'){
                    console.log(`Name: ${el.prop('name')}, Value:`, el.val());
                }
            });

            // console.log('inputs', inputs);
        },
        loadFormData: function(formId){

        }
    };

    window.Loading = {
        show: function(message = 'Loading ...') {
            const overlay = $('#globalLoadingOverlay');
            if (message) {
                overlay.find('p').text(message);
            }
            overlay.removeClass('d-none');
            // Disable body scrolling
            $('body').css('overflow', 'hidden');
        },
        hide: function() {
            $('#globalLoadingOverlay').addClass('d-none');
            // Re-enable body scrolling
            $('body').css('overflow', 'auto');
        }
    };
</script>