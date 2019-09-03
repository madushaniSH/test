
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
                jQuery('#probe_upload_success').html('Processed: ' + file_name);
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

function handleFileSelect_ref(evt) {
    var file = evt.target.files[0];
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var file_name = jQuery(this).val();
    var file_name = file_name.split('\\').pop();
    var extension = file_name.split('.').pop();
    if (extension != 'csv') {
        jQuery('#ref_upload_success').html('');
        jQuery('#ref_upload_error').html('Error. Only CSV files can be uploaded');
    } else {
        jQuery('#ref_upload_error').html('');
        var formData = new FormData();
        formData.append('csv', file);
        formData.append('db_name', project_name);
        jQuery('#ref_upload_success').html('Please wait ' + file_name + ' is now being processed.<br>');
        jQuery('#loading-spinner-ref').css("display", "inline-block");
        jQuery('#loading-spinner-ref').css("text-align", "center");
        jQuery.ajax({
            url: 'process_ref.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                jQuery('#loading-spinner-ref').css("display", "none");
                jQuery('#ref_upload_success').html('Processed: ' + file_name);
                var processed_message = '';
                if (data[0].proccessed_rows != '') {
                    processed_message += data[0].proccessed_rows + ' rows were processed and added to ' + project_name;
                }
                if (data[0].skipped_count != 0) {
                    processed_message += ', ' + data[0].skipped_count + ' rows were skipped due to missing EAN / Brand';
                }
                if (processed_message != '') {
                    jQuery('#ref_process_success').html(processed_message);
                }
                //jQuery('#ref_upload_success').html(data);
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

function show_upload_options_probe() {
    document.getElementById('probe-upload-container').classList.remove('hide');
    document.getElementById('ref-upload-container').classList.add('hide');
    document.getElementById('option1').classList.add('active_btn');
    document.getElementById('option2').classList.remove('active_btn');
}

function show_upload_options_reference() {
    document.getElementById('probe-upload-container').classList.add('hide');
    document.getElementById('ref-upload-container').classList.remove('hide');
    document.getElementById('option1').classList.remove('active_btn');
    document.getElementById('option2').classList.add('active_btn');
}

function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var upload_div = document.getElementById('ticket_section');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        upload_div.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        upload_div.classList.remove('hide');
    }
}

function fetch_tickets(){

}

function validate_new_ticket() {
    var is_valid_form = true;
    var ticket_id = document.getElementById('ticket_id').value.toUpperCase();
    var ticket_id_error = document.getElementById('ticket_id_error');
    if (ticket_id == "") {
        is_valid_form = false;
        ticket_id_error.innerHTML = "Ticket ID cannot be empty";
    } else {
        ticket_id_error.innerHTML = "";
    }
    if (is_valid_form) {
        var formData = new FormData();
        formData.append('ticket_id', ticket_id.trim());
        var project_name_element = document.getElementById('project_name');
        var project_name = project_name_element.options[project_name_element.selectedIndex].value;
        formData.append('project_name', project_name);
        jQuery.ajax({
            url: 'add_new_ticket.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                document.getElementById('close_ticket_form').click();
            },
            error: function (data) {
                alert("Error assigning probe. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

function clear_ticket_form() {
    var ticket_id = document.getElementById('ticket_id');
    ticket_id.value = "";
}


jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#ticket_name').select2({
        width: '100%',
    });
    jQuery('#project_name').change(validate_project_name);
    jQuery("#csv-file").change(handleFileSelect);
    jQuery("#ref-csv-file").change(handleFileSelect_ref);
});