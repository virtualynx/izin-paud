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
</script>