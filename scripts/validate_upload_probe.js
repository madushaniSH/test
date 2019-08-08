jQuery(document).ready(function(){
    jQuery('#csv-file').change(function(){
        var file_name = jQuery(this).val();
        var file_name = file_name.split('\\').pop();
        var extension = file_name.split('.').pop();
        if (extension != 'csv') {
            jQuery('#probe_upload_success').html('');
            jQuery('#probe_upload_error').html('Error. Only CSV files can be uploaded');
        } else {
            jQuery('#probe_upload_success').html('Processed ' + file_name);
            jQuery('#probe_upload_error').html('');
        }
    });
});