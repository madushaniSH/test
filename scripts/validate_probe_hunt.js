var p_name = '';
var product_count = 0;
var skip_check = false;

function cancel_product_button() {
    var product_name = document.getElementById('product_name').value.trim();
    var flag = true;
    if (product_name != '') {

        if (!add_probe_product()) {
            flag = false;
        }
    }
    if (flag) {
        skip_check = true;
        validate_probe_submission();
        product_count = 0;
    }
}

function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var probe_hunt_options = document.getElementById('probe_hunt_options');
    var probe_hunt_counter = document.getElementById('probe_hunt_counter');
    var hunter_counter = document.getElementById('hunter_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_options.classList.add('hide');
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
    } else {
        project_name_error.innerHTML = '';
        probe_hunt_options.classList.remove('hide');
        probe_hunt_counter.classList.remove('hide');
        hunter_counter.classList.remove('hide')
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
            var title_string = '<span id="project_title">' + project_name + '</span>';
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
            var formData = new FormData();
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
            });
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
    $('#add_probe').modal('show');
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
                    $('#current_probe_handle_count').empty();
                    $('#current_probe_handle_count').html(data[0].number_of_handled_rows);
                    $('#brand_count').empty();
                    $('#brand_count').html(data[0].brand_count);
                    $('#sku_count').empty();
                    $('#sku_count').html(data[0].sku_count);
                    $('#dvc_count').empty();
                    $('#dvc_count').html(data[0].dvc_count);
                    $('#checked_probe_count').empty();
                    $('#checked_probe_count').html(data[0].checked_count);
                    $('#qa_error_count').empty();
                    $('#qa_error_count').html(data[0].error_count);
                    $('#system_error_count').empty();
                    $('#system_error_count').html(data[0].system_error_count);
                    $('#facing_count').empty();
                    $('#facing_count').html(data[0].facing_count);
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
                        if (probe_count == 0) {
                            document.getElementById('probe_message').innerHTML = '';
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

function reset_probe_modal() {
    $('#add_probe').modal('hide');
    $("#probe_form").trigger('reset');
    $("#status").val('').trigger('change');
    $("#product_type").val('').trigger('change');
    document.getElementById('status').disabled = false;
    document.getElementById('comment').disabled = false;
    document.getElementById('remark').disabled = false;
    $('#server_error').html('');
    $('#server_success').html('');
    product_count = 0;
    reset_hunt_information();
}

function reset_hunt_information() {
    $("#product_type").val('').trigger('change');
    document.getElementById('product_name').value = '';
    document.getElementById('alt_design_name').value = '';
    document.getElementById("num_facings").value = 0;
    document.getElementById("output").innerHTML = 0;
    document.getElementById('product_name_error').innerHTML = '';
    document.getElementById('alt_design_name_error').innerHTML = '';
    document.getElementById('product_type_error').innerHTML = '';
    document.getElementById('facing_error').innerHTML = '';

}

function validate_probe_submission() {
    var is_valid_form = true;
    var formData = new FormData();
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var status_error = document.getElementById('status_error');
    var hunt_information = document.getElementById('hunt_information');
    var comment = document.getElementById('comment').value.trim();
    var remark = document.getElementById('remark').value.trim();
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    if (status == '') {
        is_valid_form = false;
        status_error.innerHTML = 'Status must be selected';
    } else {
        status_error.innerHTML = '';
        formData.append('status', status);
    }

    if (comment != '') {
        formData.append('comment', comment);
    }

    if (remark != '') {
        formData.append('remark', remark);
    }

    if (project_name != '') {
        formData.append('project_name', project_name);
    } else {
        is_valid_form = false;
    }

    if (is_valid_form) {
        if (status === '2' && !skip_check && product_count <= 1) {
            if (add_probe_product()) {
                jQuery.ajax({
                    url: 'update_probe_queue.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    success: function (data) {
                        if (data[0].success != '') {
                            reset_probe_modal();
                            product_count = 0;
                        }
                    },
                    error: function (data) {
                        alert("Error fetching probe information. Please refresh");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        } else {
            jQuery.ajax({
                url: 'update_probe_queue.php',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    reset_probe_modal();
                    product_count = 0;
                },
                error: function (data) {
                    alert("Error fetching probe information. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
        product_count = 0;
        skip_check = false;
    }
    if (product_count > 0) {
        document.getElementById('cancel_product').classList.remove('hide');
        document.getElementById('submit_probe').classList.add('hide');
    } else {
        document.getElementById('cancel_product').classList.add('hide');
        document.getElementById('submit_probe').classList.remove('hide');
    }
}

function show_additional_options() {
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var hunt_information = document.getElementById('hunt_information');
    if (status === '2') {
        hunt_information.classList.remove('hide');
    } else {
        hunt_information.classList.add('hide');
    }
}

function add_rec_comment() {
    document.getElementById('comment').value = "Recongnition Issue";
    return false;
}

function add_cant_find_comment(){
    document.getElementById('comment').value = "Some Products in Probe could not be found";
    return false;
}

function show_dvc_options() {
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var hunt_information = document.getElementById('hunt_information');
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element.options[product_type_element.selectedIndex].value;
    var alt_design_info = document.getElementById('alt_design_info');
    if (status != '' && (product_type === 'dvc' || product_type === 'facing')) {
        alt_design_info.classList.remove('hide');
    } else {
        alt_design_info.classList.add('hide');
    }

    if (product_type === 'facing') {
        document.getElementById('alt_name_label').innerHTML = 'Alternative Design Name:';
    } else {
        document.getElementById('alt_name_label').innerHTML = '*Alternative Design Name:';
    }
}

function add_probe_product() {
    var is_valid_form = true;
    var formData = new FormData();
    var product_name = document.getElementById('product_name').value.trim();
    var product_name_error = document.getElementById('product_name_error');
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element.options[product_type_element.selectedIndex].value;
    var product_type_error = document.getElementById('product_type_error');
    var alt_design_name = document.getElementById('alt_design_name').value;
    var alt_design_name_error = document.getElementById('alt_design_name_error');
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var facings = document.getElementById("num_facings").value;
    var facing_error = document.getElementById('facing_error');
    formData.append('facings', facings);

    if (product_name == '') {
        product_name_error.innerHTML = 'Product Name required';
        is_valid_form = false;
    } else {
        product_name_error.innerHTML = '';
        formData.append('product_name', product_name);
    }

    if (product_type == '') {
        product_type_error.innerHTML = 'Product Type required';
        is_valid_form = false;
    } else {
        product_type_error.innerHTML = '';
        formData.append('product_type', product_type);
    }

    if (product_type === 'dvc' && alt_design_name == '') {
        alt_design_name_error.innerHTML = 'Alternative Name Required';
        is_valid_form = false;
    } else {
        alt_design_name_error.innerHTML = '';
        formData.append('alt_design_name', alt_design_name)
    }

    if (product_type === 'facing' && facings == 0) {
        facing_error.innerHTML = 'Number of facings cannot be 0';
        is_valid_form = false;
    } else {
        facing_error.innerHTML = '';
        formData.append('facings', facings);
    }

    if (project_name != '') {
        formData.append('project_name', project_name);
    } else {
        is_valid_form = false;
    }

    if (status != '') {
        formData.append('status', status);
    } else {
        is_valid_form = false;
    }


    if (is_valid_form) {
        document.getElementById('status').disabled = true;
        document.getElementById('comment').disabled = true;
        document.getElementById('remark').disabled = true;
        jQuery.ajax({
            url: 'add_probe_product.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                if (data[0].success != '') {
                    server_success.innerHTML = data[0].success;
                } else {
                    server_success.innerHTML = '';
                    reset_hunt_information();
                }

                if (data[0].error != '') {
                    server_error.innerHTML = data[0].error;
                } else {
                    server_error.innerHTML = '';
                }
                if (data[0].duplicate_error != '') {
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                    }
                    toastr.error(data[0].duplicate_error);
                }
            },
            error: function (data) {
                alert("Error fetching probe information. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
        reset_hunt_information();
        product_count++;
    }

    if (product_count > 0) {
        document.getElementById('cancel_product').classList.remove('hide');
        document.getElementById('submit_probe').classList.add('hide');
    } else {
        document.getElementById('cancel_product').classList.add('hide');
        document.getElementById('submit_probe').classList.remove('hide');
    }
    return is_valid_form;
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
    setInterval(function () { update_project_count(); }, 500);
    if (product_count > 0) {
        document.getElementById('cancel_product').classList.remove('hide');
        document.getElementById('submit_probe').classList.add('hide');
    } else {
        document.getElementById('cancel_product').classList.add('hide');
        document.getElementById('submit_probe').classList.remove('hide');
    }
});