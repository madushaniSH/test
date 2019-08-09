
function handleFileSelect(evt) {
    var file = evt.target.files[0];
    var file_name = jQuery(this).val();
    var file_name = file_name.split('\\').pop();
    var extension = file_name.split('.').pop();
    if (extension != 'csv') {
        jQuery('#probe_upload_success').html('');
        jQuery('#probe_upload_error').html('Error. Only CSV files can be uploaded');
    } else {
        jQuery('#probe_upload_error').html('');
        var formData = new FormData();
        formData.append('csv', file);
        jQuery('#probe_upload_success').html('Please wait' + file_name + 'is now being processed.<br>');
        jQuery('#loading-spinner').css("display", "inline-block");
        jQuery('#loading-spinner').css("text-align", "center");
        jQuery.ajax({
            url: 'process_probe.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                jQuery('#loading-spinner').css("display", "none");
                jQuery('#probe_upload_success').html('Processed: '+ file_name);
            },
            error: function (data) {
                alert("AJAX error");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}


jQuery(document).ready(function () {
    jQuery("#csv-file").change(handleFileSelect);
});