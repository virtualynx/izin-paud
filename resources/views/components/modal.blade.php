<!-- Modal -->
<div class="modal fade" id="modal_general" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Modal content goes here
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Ok</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.Modal = {
        showInfo: function(
            message,
            buttonOkCallback = function(event){
                window.Modal.hide();
            },
            title = 'Info'
        ){
            window.Modal.show(
                `<div class="text-center">
                    <i class="bi bi-info-circle text-primary" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">${title}</h4>
                    <p>${message}</p>
                </div>`,
                title,
                {
                    label: 'Ok', 
                    callback: buttonOkCallback
                },
                false
            );
        },
        showError: function(
            message,
            buttonOkCallback = function(event){
                window.Modal.hide();
            },
            title = 'Error'
        ){
            window.Modal.show(
                `<div class="text-center">
                    <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Terjadi Kesalahan</h4>
                    <p>${message}</p>
                </div>`,
                title,
                {
                    label: 'Ok', 
                    callback: buttonOkCallback
                },
                false
            );
        },
        show: function(
            body = null, 
            title = null,
            primaryButtonConfig = {
                label: 'Ok', 
                callback: function(event){
                    // console.log('Primary-Button clicked');
                    window.Modal.hide();
                }
            },
            showCancelButton = true
        ){
            // console.log('modal.id', $('#modal_general').attr('id'));
            const jqModal = $('#modal_general');

            // Clean up any existing modal instance
            const existingModal = bootstrap.Modal.getInstance(jqModal[0]);
            if (existingModal) {
                existingModal.hide();
            }

            // Remove previous click handlers to prevent accumulation
            jqModal.find('.btn-primary').off('click');

            if(title === null){
                jqModal.find('.modal-title').html('');
            }else{
                jqModal.find('.modal-title').html(title);
            }
            
            if(body !== null){
                jqModal.find('.modal-body').html(body);
            }

            jqModal.find('.btn-primary').html(primaryButtonConfig.label);
            jqModal.find('.btn-primary').on('click', primaryButtonConfig.callback);

            // Initialize and show modal
            const modal = new bootstrap.Modal(jqModal[0], {
                backdrop: 'static' // This prevents closing when clicking outside
            });
            
            // Handle hidden event to clean up
            jqModal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
                window.Modal.hide();
            });

            if(showCancelButton){
                jqModal.find('.btn-secondary').css('visibility', 'visible');
            }else{
                jqModal.find('.btn-secondary').css('visibility', 'hidden');
            }
            
            modal.show();
        },
        hide: function(){
            const modal = bootstrap.Modal.getInstance(document.querySelector('#modal_general'));
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
        }
    };
</script>