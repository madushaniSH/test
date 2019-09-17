var p_name = '';
var product_count = 0;
var skip_check = false;
var selected_ticket = '';
let save_confirm = false;

function get_ticket_list() {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "get_project_tickets.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                $("#ticket").append(
                    '<option value="" selected disabled>Select</option>'
                );
                // adding missing options
                for (var i = 0; i < data[0].ticket_list.length; i++) {
                    if (
                        !$("#ticket").find(
                            'option[value="' + data[0].ticket_list[i].project_ticket_system_id + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#ticket").append(
                            '<option value="' +
                            data[0].ticket_list[i].project_ticket_system_id +
                            '">' +
                            data[0].ticket_list[i].ticket_id +
                            "</option>"
                        );
                    }
                }
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

function cancel_product_button() {
    var product_name = document.getElementById('product_name').value.trim();
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element.options[product_type_element.selectedIndex].value;
    var flag = true;
    if (product_name != '' || product_type != '') {

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
    var ticket_section = document.getElementById('ticket_section');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_options.classList.add('hide');
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
    } else {
        $('#ticket').empty();
        project_name_error.innerHTML = '';
        probe_hunt_options.classList.add('hide');
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
        ticket_section.classList.remove('hide');
        p_name = project_name;
        get_ticket_list();
    }
}

function get_probe_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    formData.append('ticket', selected_ticket);
    jQuery.ajax({
        url: 'assign_probe.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name + " " + data[0].ticket + '</span>';
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
    if (p_name != '' && selected_ticket != '') {
        var formData = new FormData();
        formData.append('project_name', p_name);
        formData.append('ticket', selected_ticket);
        var d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(15);
        let cycle_start = new Date();
        let cycle_end = new Date();
        cycle_start.setUTCHours(0, 0, 0, 0);
        cycle_end.setUTCHours(0, 0, 0, 0);
        if (cycle_start.getUTCDate() <= 15) {
            cycle_start.setUTCDate(16);
            cycle_start.setUTCHours(4, 30, 0, 0)
            cycle_start.setUTCMonth(cycle_start.getMonth() - 1);
            cycle_end.setUTCDate(15);
            cycle_end.setUTCHours(4, 30, 0, 0);
        } else {
            cycle_start.setUTCDate(16);
            cycle_start.setUTCDate(16);
            cycle_start.setUTCHours(4, 30, 0, 0)
            cycle_end.setUTCMonth(cycle_start.getMonth() + 1);
            cycle_end.setUTCDate(15);
            cycle_end.setUTCHours(4, 30, 0, 0)
        }
        formData.append('start_time', cycle_start.toISOString().slice(0, 19).replace('T', ' '));
        formData.append('end_time', cycle_end.toISOString().slice(0, 19).replace('T', ' '));
        jQuery.ajax({
            url: 'fetch_probe_count.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
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
                    $('#product_count').empty();
                    $('#product_count').html(data[0].number_of_products_added);
                    $('#pro_count').empty();
                    $('#pro_count').html(data[0].checked_count);
                    $('#rename_error_count').empty();
                    $('#rename_error_count').html(data[0].rename_error_count);
                    $('#error_type_count').empty();
                    $('#error_type_count').html(data[0].error_type_count);
                    $('#mon_acc_count').empty();
                    $('#mon_acc_count').html(data[0].mon_accuracy + '%');
                    $('#acc_pro').html(p_name);
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
                    var added_product_count = parseInt(data[0].number_of_products_added, 10);
                    if (added_product_count > 0) {
                        product_count = added_product_count;
                        document.getElementById('status').disabled = true;
                        var formData = new FormData();
                        var project_name_element = document.getElementById('project_name');
                        var project_name = project_name_element.options[project_name_element.selectedIndex].value;
                        formData.append('project_name', project_name);
                        jQuery.ajax({
                            url: 'get_project_status.php',
                            type: 'POST',
                            data: formData,
                            success: function (data) {
                                $('#status').html(data);
                                $('#status').val(2).change();
                            },
                            error: function (data) {
                                alert("Error assigning probe. Please refresh");
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    } else {
                        product_count = 0;
                        document.getElementById('status').disabled = false;
                    }
                    if (product_count > 0) {
                        document.getElementById('cancel_product').classList.remove('hide');
                        document.getElementById('submit_probe').classList.add('hide');
                    } else {
                        document.getElementById('cancel_product').classList.add('hide');
                        document.getElementById('submit_probe').classList.remove('hide');
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
    $('#server_error').html('');
    $('#server_success').html('');
    product_count = 0;
    reset_hunt_information();
}

function reset_hunt_information() {
    $("#product_type").val('').trigger('change');
    document.getElementById('product_name').value = '';
    document.getElementById('manu_link').value = '';
    document.getElementById('product_link').value = '';
    document.getElementById('alt_design_name').value = '';
    document.getElementById("num_facings").value = 0;
    document.getElementById("output").innerHTML = 0;
    document.getElementById('product_name_error').innerHTML = '';
    document.getElementById('alt_design_name_error').innerHTML = '';
    document.getElementById('product_type_error').innerHTML = '';
    document.getElementById('facing_error').innerHTML = '';
    document.getElementById('manu_link_error').innerHTML = '';

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
    var product_name = document.getElementById('product_name').value.trim();

    if (product_name != "" && product_count != 0) {
        if (!add_probe_product()) {
            is_valid_form = false;
        }
    }

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
        if (status === '2' && !skip_check && product_count < 1) {
            var is_valid_form = true;
            var formData = new FormData();
            var product_name = document.getElementById('product_name').value.trim();
            var product_name_error = document.getElementById('product_name_error');
            var product_type_element = document.getElementById('product_type');
            var product_type = product_type_element.options[product_type_element.selectedIndex].value;
            var product_type_error = document.getElementById('product_type_error');
            var alt_design_name = document.getElementById('alt_design_name').value.trim();
            var alt_design_name_error = document.getElementById('alt_design_name_error');
            var project_name_element = document.getElementById('project_name');
            var project_name = project_name_element.options[project_name_element.selectedIndex].value;
            var status_element = document.getElementById('status');
            var status = status_element.options[status_element.selectedIndex].value;
            var facings = document.getElementById("num_facings").value;
            var facing_error = document.getElementById('facing_error');
            var manu_link = document.getElementById('manu_link').value.trim();
            var product_link = document.getElementById('product_link').value.trim();
            formData.append('facings', facings);

            if (product_name == '') {
                product_name_error.innerHTML = 'Product Name required';
                is_valid_form = false;
            } else {
                product_name_error.innerHTML = '';
                var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,]*$/;
                if (!patt.test(product_name)) {
                    is_valid_form = false;
                    product_name_error.innerHTML = 'Non English Product Name Entered';
                } else {
                    product_name_error.innerHTML = '';
                    formData.append('product_name', product_name);
                }
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
                var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,]*$/;
                if (!patt.test(alt_design_name)) {
                    is_valid_form = false;
                    alt_design_name_error.innerHTML = 'Non English Product Name Entered';
                } else {
                    alt_design_name_error.innerHTML = '';
                    formData.append('alt_design_name', alt_design_name);
                }
            }

            if (product_type === 'brand' && manu_link == '') {
                document.getElementById('manu_link_error').innerHTML = 'Manufacturer Link cannot be empty';
                is_valid_form = false;
            } else {
                document.getElementById('manu_link_error').innerHTML = '';
                if (!is_url(manu_link) && product_type == 'brand') {
                    document.getElementById('manu_link_error').innerHTML = 'Invalid URL';
                    is_valid_form = false;
                } else {
                    if (product_type != 'brand') {
                        manu_link = '';
                    }
                    formData.append('manu_link', manu_link);
                }
            }

            if (product_link != '' && !is_url(product_link)) {
                is_valid_form = false;
                document.getElementById('product_link_error').innerHTML = 'Invalid URL';
            } else {
                document.getElementById('product_link_error').innerHTML = '';
                formData.append('product_link', product_link);
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
                $('#confirm_probe').modal('show');
                if (save_confirm) {
                    document.getElementById('status').disabled = true;
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


                    if (product_count > 0) {
                        document.getElementById('cancel_product').classList.remove('hide');
                        document.getElementById('submit_probe').classList.add('hide');
                    } else {
                        document.getElementById('cancel_product').classList.add('hide');
                        document.getElementById('submit_probe').classList.remove('hide');
                    }
                }
                save_confirm = false;
            }
        } else {
            $('#confirm_probe').modal('show');
            if (save_confirm) {
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
                save_confirm = false;
            }
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

const confirm_save = () => {
    save_confirm = true
    document.getElementById('submit_probe').click();
    $('#confirm_probe').modal('hide');
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

function add_cant_find_comment() {
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
    var manu_link_section = document.getElementById('manu_link_section');
    if (status != '' && (product_type === 'dvc' || product_type === 'facing')) {
        alt_design_info.classList.remove('hide');
    } else {
        alt_design_info.classList.add('hide');
    }

    if (product_type === 'brand') {
        manu_link_section.classList.remove('hide');
    } else {
        manu_link_section.classList.add('hide');
    }

    if (product_type === 'facing') {
        document.getElementById('alt_name_label').innerHTML = 'Alternative Design Name:';
    } else {
        document.getElementById('alt_name_label').innerHTML = '*Alternative Design Name:';
    }
}

function is_url(str) {
    regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
    if (regexp.test(str)) {
        return true;
    }
    else {
        return false;
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
    var alt_design_name = document.getElementById('alt_design_name').value.trim();
    var alt_design_name_error = document.getElementById('alt_design_name_error');
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var facings = document.getElementById("num_facings").value;
    var facing_error = document.getElementById('facing_error');
    var manu_link = document.getElementById('manu_link').value.trim();
    var product_link = document.getElementById('product_link').value.trim();
    formData.append('facings', facings);

    if (product_name == '') {
        product_name_error.innerHTML = 'Product Name required';
        is_valid_form = false;
    } else {
        product_name_error.innerHTML = '';
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,]*$/;
        if (!patt.test(product_name)) {
            is_valid_form = false;
            product_name_error.innerHTML = 'Non English Product Name Entered';
        } else {
            product_name_error.innerHTML = '';
            formData.append('product_name', product_name);
        }
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
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,]*$/;
        if (!patt.test(alt_design_name)) {
            is_valid_form = false;
            alt_design_name_error.innerHTML = 'Non English Product Name Entered';
        } else {
            alt_design_name_error.innerHTML = '';
            formData.append('alt_design_name', alt_design_name);
        }
    }

    if (product_type === 'brand' && manu_link == '') {
        document.getElementById('manu_link_error').innerHTML = 'Manufacturer Link cannot be empty';
        is_valid_form = false;
    } else {
        document.getElementById('manu_link_error').innerHTML = '';
        if (!is_url(manu_link) && product_type == 'brand') {
            document.getElementById('manu_link_error').innerHTML = 'Invalid URL';
            is_valid_form = false;
        } else {
            if (product_type != 'brand') {
                manu_link = '';
            }
            formData.append('manu_link', manu_link);
        }
    }

    if (product_link != '' && !is_url(product_link)) {
        is_valid_form = false;
        document.getElementById('product_link_error').innerHTML = 'Invalid URL';
    } else {
        document.getElementById('product_link_error').innerHTML = '';
        formData.append('product_link', product_link);
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

function validate_ticket_name() {
    if (p_name != "") {
        var ticket = jQuery('#ticket').val();
        var probe_hunt_options = document.getElementById('probe_hunt_options');
        var probe_hunt_counter = document.getElementById('probe_hunt_counter');
        var hunter_counter = document.getElementById('hunter_counter');
        if (ticket == "" || ticket == null) {
            probe_hunt_options.classList.add('hide');
            probe_hunt_counter.classList.add('hide');
            hunter_counter.classList.add('hide');
        } else {
            probe_hunt_options.classList.remove('hide');
            probe_hunt_counter.classList.remove('hide');
            hunter_counter.classList.remove('hide');
            selected_ticket = ticket;
            if (p_name == 'GMI_US') {
                document.getElementById('target_message').classList.remove('hide');
            } else {
                document.getElementById('target_message').classList.add('hide');
            }
        }
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#ticket').select2({
        width: '50%',
    });
    jQuery('#status').select2({
        dropdownParent: $("#add_probe"),
        width: '100%',
    });
    jQuery('#product_type').select2({
        dropdownParent: $("#add_probe"),
        width: '100%',
    });
    jQuery('#project_name').on('select2:select', function () {
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
    $("#show_button").mouseenter(function () {
        document.getElementById('counters').classList.remove('hide');
        document.getElementById('arrow_sec').classList.remove('bounce');
        $("#counters").fadeIn();
    });
    $('#ticket').on('select2:select', function (e) {
        validate_ticket_name();
    });
    $("#show_button").mouseleave(function () {
        $("#counters").fadeOut();
        document.getElementById('arrow_sec').classList.add('bounce');
    });
});