var p_name = '';

function get_ref_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    jQuery.ajax({
        url: 'assign_ref.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name + '</span>';
            if (data[0].ean != null) {
                title_string += ' <span id="brand_title">' + data[0].ean + '</span>';
            }
            if (data[0].brand != null) {
                title_string += ' <span id="client_category_title">' + data[0].brand + '</span>';
            }
            jQuery('#add_reference_title').html(title_string);
            /*var formData = new FormData();
            formData.append('project_name', project_name);
            jQuery.ajax({
                url: 'get_project_status.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('#status').html(data);
                },
                error: function (data) {
                    alert("Error assigning probe. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });*/
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
    $('#add_reference').modal('show');
}

function update_ref_count() {
    if (p_name != '') {
        var formData = new FormData();
        formData.append('project_name', p_name);
        jQuery.ajax({
            url: 'fetch_ref_count.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                if (data[0].number_of_rows != null) {
                    $('#current_ref_count').empty();
                    $('#current_ref_count').html(data[0].number_of_rows);
                    $('#current_ref_handle_count').empty();
                    $('#current_ref_handle_count').html(data[0].number_of_handled_rows);
                    var count = parseInt(data[0].number_of_rows, 10);
                    var probe_count = parseInt(data[0].processing_probe_row, 10);
                    if (count == 0 && probe_count == 0) {
                        document.getElementById('continue_btn').classList.add('hide');
                        document.getElementById('probe_message').innerHTML = '';
                        if ($('#add_probe').is(':visible')) {
                            $('#close_probe_title').click();
                        }
                    } else {
                        document.getElementById('continue_btn').classList.remove('hide');
                        if (probe_count === 1) {
                            document.getElementById('probe_message').innerHTML = 'Reference Assigned. Please press Continue';
                        }
                        if (probe_count == 0) {
                            document.getElementById('probe_message').innerHTML = '';
                        }
                    }
                } else {
                    $('#current_ref_count').html('XX');
                }
            },
            error: function (data) {
                alert("Error fetching probe information. Please refresh");
                clearInterval(request);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var ref_hunt_options = document.getElementById('ref_hunt_options');
    var ref_hunt_counter = document.getElementById('ref_hunt_counter');
    //var hunter_counter = document.getElementById('hunter_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        ref_hunt_options.classList.add('hide');
        ref_hunt_counter.classList.add('hide');
      //hunter_counter.classList.add('hide')
    } else {
        project_name_error.innerHTML = '';
        ref_hunt_options.classList.remove('hide');
        ref_hunt_counter.classList.remove('hide');
        //hunter_counter.classList.remove('hide')
        p_name = project_name;
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#project_name').change(function () {
        validate_project_name();
    });
    setInterval(function () { update_ref_count(); }, 500);
});