var p_name = '';
function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var probe_hunt_options = document.getElementById('probe_hunt_options');
    var probe_hunt_counter = document.getElementById('probe_hunt_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_options.classList.add('hide');
        probe_hunt_counter.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        probe_hunt_options.classList.remove('hide');
        probe_hunt_counter.classList.remove('hide');
        p_name = project_name;
    }
}

function get_probe_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    jQuery.ajax({
        url: 'assign_probe.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name  + '</span>';
            if (data[0].brand_name != null) {
                title_string += ' <span id="brand_title">' + data[0].brand_name + '</span>';
            }
            if (data[0].client_category_name != null) {
                title_string += ' <span id="client_category_title">' + data[0].client_category_name + '</span>';
            }
            if (data[0].probe_id != null) {
                title_string += ' <span id="probe_id_title">' + data[0].probe_id;
            }
            jQuery('#add_probe_title').html(title_string);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function update_project_count() {
    if (p_name != '') {
        var formData = new FormData();
        formData.append('project_name', p_name);
        jQuery.ajax({
            url: 'fetch_probe_count.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                var output_count;
                if (data[0].number_of_rows != null) {
                    $('#current_probe_count').empty();
                    $('#current_probe_count').html(data[0].number_of_rows);
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
                            document.getElementById('probe_message').innerHTML = 'Probe Assigned. Please press Continue';
                        }
                    }
                } else {
                    $('#current_probe_count').html('XX');
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

function validate_probe_submission() {
    var is_valid_form = true;
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var status_error = document.getElementById('status_error');
    var hunt_information = document.getElementById('hunt_information');
    if (status == '') {
        is_valid_form = false;
        status_error.innerHTML = 'Status must be selected';
    } else {
        status_error.innerHTML = '';
    }
}

function show_additional_options(){
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var hunt_information = document.getElementById('hunt_information');
    if (status === '2') {
        hunt_information.classList.remove('hide');
    } else {
        hunt_information.classList.add('hide');
    }
}

function show_dvc_options() {
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var hunt_information = document.getElementById('hunt_information');
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element.options[product_type_element.selectedIndex].value;
    var alt_design_info = document.getElementById('alt_design_info');
    if (status != '' && product_type === 'dvc') {
        alt_design_info.classList.remove('hide');
    } else {
        alt_design_info.classList.add('hide');
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#status').select2({
        dropdownParent: $("#add_probe"),
        width: '100%',
    });
    jQuery('#product_type').select2({
        dropdownParent: $("#add_probe"),
        width: '100%',
    });
    jQuery('#project_name').change(function () {
        validate_project_name();
    });
    jQuery('#status').change(function () {
        show_additional_options();
    });
    jQuery('#product_type').change(function () {
        show_dvc_options();
    });
    setInterval(function () { update_project_count(); }, 2000);
});