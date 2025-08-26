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
                Modal.show(
                    `<span class="text-danger"><div>${errorMessage}</div></span>`,
                    `<span class="text-danger">Error</span>`
                )
            }else{
                console.error('AJAX Error:', xhr.status, error);
                console.log(errorMessage);
            }
        }
    };
</script>