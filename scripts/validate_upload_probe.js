
function handleFileSelect(evt) {
    var file = evt.target.files[0];
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
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
        formData.append('db_name', project_name);
        jQuery('#probe_upload_success').html('Please wait ' + file_name + ' is now being processed.<br>');
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

function validate_project_name () {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var upload_div = document.getElementById('probe-upload');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        upload_div.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        upload_div.classList.remove('hide');
    }
}


jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',     
    });
    jQuery('#project_name').change(validate_project_name);
    jQuery("#csv-file").change(handleFileSelect);
});