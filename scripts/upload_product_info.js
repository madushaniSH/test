let user_selection = {
    project_name: '',
}

function upload_product_info(event) {
    jQuery('#upload_message').html('');
    jQuery('#display_message').html('');
    let file = event.target.files[0];
    if (user_selection.project_name != '') {
        jQuery('#upload_message').html('');
        jQuery('#display_message').html('');
        $('#load_section').removeClass('hide');
        document.getElementById('project_name').disabled = true;
        var file_name = jQuery(this).val();
        var file_name = file_name.split('\\').pop();
        var extension = file_name.split('.').pop();
        if (extension != 'csv') {
            $('#load_section').addClass('hide');
            $('#display_message').html('Only CSV files can be uploaded');
            document.getElementById('project_name').disabled = false;
        } else {
            $('#display_message').html('');
            let formData = new FormData();
            formData.append('csv', file);
            formData.append('db_name', user_selection.project_name);
            jQuery('#upload_message').html('Please wait ' + file_name + ' is now being processed.<br>');
            jQuery.ajax({
                url: 'process_product_info.php',
                type: 'POST',
                data: formData,
                dataType: "JSON",
                success: function (data) {
                    console.log(data[0].info);
                },
                error: function (data) {
                    alert("AJAX error");
                },
                cache: false,
                contentType: false,
                processData: false
            });
            document.getElementById('project_name').disabled = false;
            $('#load_section').addClass('hide');
        }
    }
}

const validate_project_name = () => {
    const project_name = $('#project_name').val();
    jQuery('#upload_message').html('');
    jQuery('#display_message').html('');
    $('#selected_project_name').html(project_name);
    if (project_name != '') {
        user_selection.project_name = project_name;
        $('#product_csv_upload_label').removeClass('hide');
    } else {
        user_selection.project_name = '';
        $('#product_csv_upload_label').addClass('hide');
    }

}

$(document).ready(function () {
    $('#project_name').select2({
        width: '100%',
    });
    $("#project_name").change(function () {
        validate_project_name();
    });
    $('#product_csv_upload').change(upload_product_info);
});