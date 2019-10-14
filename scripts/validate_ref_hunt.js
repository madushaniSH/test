var p_name = '';
let product_count = 0;
let selected_ticket = '';
let current_brand_count = 0;
let current_sku_count = 0;
const reset_ref_form = () => {
    $("#status").val('').trigger('change');
    $("#probe_form").trigger('reset');
    $('#status_error').empty();
    $('#source_error').empty();
    document.getElementById('status').disabled = false;
    $('#server_success').empty();
    product_count = 0;
    jQuery('#ref_recognition').val('');
    jQuery('#ref_short_name').val('');
    jQuery('#ref_sub_brand').val('');
    jQuery('#ref_manufacturer').val('');
    jQuery('#ref_category').val('');
    jQuery('#ref_sub_category').val('');
    jQuery('#ref_base_size').val('');
    jQuery('#ref_size').val('');
    jQuery('#ref_measurement_unit').val('');
    jQuery('#ref_container_type').val('');
    jQuery('#ref_agg_level').val('');
    jQuery('#ref_segment').val('');
    jQuery('#ref_upc2').val('');
    jQuery('#ref_flavor_detail').val('');
    jQuery('#ref_case_pack').val('');
    jQuery('#ref_multi_pack').val('');
}

function get_brand_list(select_element) {
    if (p_name != "" && selected_ticket != '') {
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("ticket", selected_ticket);
        jQuery.ajax({
            url: "get_ref_brand_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                for (var i = 0; i < data[0].brand_rows.length; i++) {
                    if (
                        !$("#" + select_element).find(
                            'option[value="' + data[0].brand_rows[i].name + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#" + select_element).append(
                            '<option value="' +
                            data[0].brand_rows[i].name +
                            '">' +
                            data[0].brand_rows[i].name +
                            "</option>"
                        );
                    }
                }

                var element = document.getElementById(select_element).options;
                var found = true;
                for (var i = 0; i < element.length; i++) {
                    found = false;
                    for (var j = 0; j < data[0].brand_rows.length; j++) {
                        if (data[0].brand_rows[j].name == element[i].value) {
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        document
                            .getElementById(select_element)
                            .remove(document.getElementById(select_element)[i]);
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

const is_url = (str) => {
    regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
    if (regexp.test(str)) {
        return true;
    }
    else {
        return false;
    }
}

const validate_product_info = () => {
    let is_valid_form = true;
    const product_name = document.getElementById('product_name').value.trim();
    const product_name_error = document.getElementById('product_name_error');
    const product_name_ref_error = document.getElementById('product_name_ref_error');
    const product_type_element = document.getElementById('product_type');
    const product_type = product_type_element.options[product_type_element.selectedIndex].value;
    const product_type_error = document.getElementById('product_type_error');
    const alt_design_name = document.getElementById('alt_design_name').value.trim();
    const alt_design_name_error = document.getElementById('alt_design_name_error');
    const project_name_element = document.getElementById('project_name');
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const facings = document.getElementById("num_facings").value;
    const facing_error = document.getElementById('facing_error');
    let manu_link = document.getElementById('manu_link').value.trim();
    const product_link = document.getElementById('product_link').value.trim();

    if (product_name == '') {
        product_name_error.innerHTML = 'Product Name required';
        is_valid_form = false;
    } else {
        product_name_error.innerHTML = '';
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,\#\\\;\:\=\$]*$/;
        if (!patt.test(product_name)) {
            is_valid_form = false;
            product_name_error.innerHTML = 'Non English Product Name Entered.';
        } else {
            product_name_error.innerHTML = '';
        }
    }

    if (((product_type === 'brand' && current_brand_count >= 1) || (product_type === 'sku' && current_sku_count >= 1)) && product_name != ''){
        product_name_ref_error.innerHTML = 'Cannot enter more than one of this product type';
        is_valid_form = false;
    } else {
        product_name_ref_error.innerHTML = '';
    }

    if (product_type == '') {
        product_type_error.innerHTML = 'Product Type required';
        is_valid_form = false;
    } else {
        product_type_error.innerHTML = '';
    }

    if (product_type === 'dvc' && alt_design_name == '') {
        alt_design_name_error.innerHTML = 'Alternative Name Required';
        is_valid_form = false;
    } else {
        alt_design_name_error.innerHTML = '';
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,\#\\\;\:\=\$]*$/;
        if (!patt.test(alt_design_name)) {
            is_valid_form = false;
            alt_design_name_error.innerHTML = 'Non English Product Name Entered';
        } else {
            alt_design_name_error.innerHTML = '';
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
        }
    }


    if (product_link != '' && !is_url(product_link)) {
        is_valid_form = false;
        document.getElementById('product_link_error').innerHTML = 'Invalid URL';
    } else {
        document.getElementById('product_link_error').innerHTML = '';
    }

    if (product_type === 'facing' && facings == 0) {
        facing_error.innerHTML = 'Number of facings cannot be 0';
        is_valid_form = false;
    } else {
        facing_error.innerHTML = '';
    }

    if (project_name == '') {
        is_valid_form = false;
    }

    if (status == '') {
        is_valid_form = false;
    }
    return is_valid_form;
}

const reset_hunt_information = () => {
    $("#product_type").val('').trigger('change');
    document.getElementById('product_name').value = '';
    document.getElementById('manu_link').value = '';
    document.getElementById('product_link').value = '';
    document.getElementById('alt_design_name').value = '';
    document.getElementById("num_facings").value = 0;
    document.getElementById("output").innerHTML = 0;
    document.getElementById('product_name_error').innerHTML = '';
    document.getElementById('server_success').innerHTML = '';
    document.getElementById('alt_design_name_error').innerHTML = '';
    document.getElementById('product_type_error').innerHTML = '';
    document.getElementById('facing_error').innerHTML = '';
    document.getElementById('manu_link_error').innerHTML = '';
}

const save_ref_info = (save_product, close_reference) => {
    let valid_product = false;
    let is_valid_form = true;
    let skip_check = false;
    const comment = document.getElementById('comment').value.trim();
    const remark = document.getElementById('remark').value.trim();
    const product_name = document.getElementById('product_name').value.trim();
    const product_type_element = document.getElementById('product_type');
    const product_type = product_type_element.options[product_type_element.selectedIndex].value;
    const alt_design_name = document.getElementById('alt_design_name').value.trim();
    const project_name_element = document.getElementById('project_name');
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const facings = document.getElementById("num_facings").value;
    let manu_link = document.getElementById('manu_link').value.trim();
    const product_link = document.getElementById('product_link').value.trim();
    const server_success = document.getElementById('server_success');
    if (status === '') {
        document.getElementById('status_error').innerHTML = 'Status must be selected';
        is_valid_form = false;
    } else {
        document.getElementById('status_error').innerHTML = '';
    }
    if (save_product && status == 2 && is_valid_form) {
        if (product_name === '' && product_type === '' && close_reference && product_count > 0) {
            skip_check = true;
        }
        if (!skip_check) {
            valid_product = validate_product_info();
            if (valid_product) {
                if (product_type != 'brand') {
                    manu_link = '';
                }
                let formData = new FormData();
                formData.append('project_name', p_name);
                formData.append('status', status);
                formData.append('product_name', product_name);
                formData.append('product_type', product_type);
                formData.append('alt_design_name', alt_design_name);
                formData.append('manu_link', manu_link);
                formData.append('product_link', product_link);
                formData.append('facings', facings);
                status_element.disabled = true;
                jQuery.ajax({
                    url: 'add_ref_product.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    success: function (data) {
                        if (data[0].success != '') {
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
                                "timeOut": "5000",
                                "extendedTimeOut": "0",
                            }
                            toastr.success('Product Added');
                        } else {
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
            } else {
                is_valid_form = false;
            }
        }
        if (close_reference && (valid_product || skip_check)) {
            let formData = new FormData();
            formData.append('project_name', p_name);
            formData.append('status', status);
            formData.append('comment', comment);
            formData.append('remark', remark);
            jQuery.ajax({
                url: 'close_ref.php',
                type: 'POST',
                data: formData,
                success: function (data) {
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
                        "timeOut": "5000",
                        "extendedTimeOut": "0",
                    }
                    toastr.success('Reference Cleared');
                },
                error: function (data) {
                    alert("Error fetching probe information. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });
            reset_hunt_information();
        }
    }
    if (close_reference && is_valid_form && status != 2) {
        let formData = new FormData();
        formData.append('project_name', p_name);
        formData.append('status', status);
        formData.append('comment', comment);
        formData.append('remark', remark);
        jQuery.ajax({
            url: 'close_ref.php',
            type: 'POST',
            data: formData,
            success: function (data) {
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
                    "timeOut": "5000",
                    "extendedTimeOut": "0",
                }
                toastr.success('Reference Cleared');
            },
            error: function (data) {
                alert("Error fetching probe information. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
        reset_hunt_information();
    }
    return is_valid_form;
}


function get_ref_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    var sku_brand_name = $("#brand_name_filter").val();
    formData.append("sku_brand_name", sku_brand_name);
    formData.append('project_name', project_name);
    formData.append('ticket', selected_ticket);
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
            title_string += ' <span>' + data[0].ref_info["ticket_id"] + '</span';
            jQuery('#add_reference_title').html(title_string);
            jQuery('#ref_recognition').val(data[0].ref_info["reference_recognition_level"]);
            jQuery('#ref_short_name').val(data[0].ref_info["reference_short_name"]);
            jQuery('#ref_sub_brand').val(data[0].ref_info["reference_sub_brand"]);
            jQuery('#ref_manufacturer').val(data[0].ref_info["reference_manufacturer"]);
            jQuery('#ref_category').val(data[0].ref_info["reference_category"]);
            jQuery('#ref_sub_category').val(data[0].ref_info["reference_sub_category"]);
            jQuery('#ref_base_size').val(data[0].ref_info["reference_base_size"]);
            jQuery('#ref_size').val(data[0].ref_info["reference_size"]);
            jQuery('#ref_measurement_unit').val(data[0].ref_info["reference_measurement_unit"]);
            jQuery('#ref_container_type').val(data[0].ref_info["reference_container_type"]);
            jQuery('#ref_agg_level').val(data[0].ref_info["reference_agg_level"]);
            jQuery('#ref_segment').val(data[0].ref_info["reference_segment"]);
            jQuery('#ref_upc2').val(data[0].ref_info["reference_count_upc2"]);
            jQuery('#ref_flavor_detail').val(data[0].ref_info["reference_flavor_detail"]);
            jQuery('#ref_case_pack').val(data[0].ref_info["reference_case_pack"]);
            jQuery('#ref_multi_pack').val(data[0].ref_info["reference_multi_pack"]);
            var formData = new FormData();
            formData.append('project_name', project_name);
            jQuery.ajax({
                url: 'get_project_status.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('#status').html(data);
                    if (product_count > 0) {
                        document.getElementById('status').disabled = true;
                        $('#status').val(2).change();
                    }
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
    $('#add_reference').modal('show');
    document.getElementById("def_tab").click();
}

function update_ref_count() {
    if (p_name != '' && selected_ticket != '') {
        get_brand_list("brand_name_filter");
        var formData = new FormData();
        formData.append('project_name', p_name);
        var sku_brand_name = $("#brand_name_filter").val();
        formData.append("sku_brand_name", sku_brand_name);
        formData.append("ticket", selected_ticket);
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
                    $('#ref_current_brand_counter').empty();
                    $('#ref_current_brand_counter').html(data[0].number_of_added_brand);
                    current_brand_count = parseInt(data[0].number_of_added_brand, 10);
                    $('#ref_current_sku_counter').empty();
                    $('#ref_current_sku_counter').html(data[0].number_of_added_sku);
                    current_sku_count = parseInt(data[0].number_of_added_sku, 10);
                    $('#ref_current_dvc_counter').empty();
                    $('#ref_current_dvc_counter').html(data[0].number_of_added_dvc);
                    let dvc_count = parseInt(data[0].number_of_added_dvc, 10);
                    $('#ref_current_facing_counter').empty();
                    $('#ref_current_facing_counter').html(data[0].number_of_added_facing);
                    let facing_count = parseInt(data[0].number_of_added_facing, 10);
                    product_count = current_sku_count + current_brand_count + dvc_count + facing_count;
                    var count = parseInt(data[0].number_of_rows, 10);
                    var probe_count = parseInt(data[0].processing_probe_row, 10);
                    if (count == 0 && probe_count == 0) {
                        document.getElementById('ref_brand_button').disabled = true;
                        document.getElementById('probe_message').innerHTML = '';
                        if ($('#add_reference').is(':visible')) {
                            $('#close_probe_title').click();
                        }
                    } else {
                        document.getElementById('ref_brand_button').disabled = false;
                        if (probe_count === 1) {
                            document.getElementById('probe_message').innerHTML = "Reference already assigned for brand " + data[0].brand_name;
                        }
                        if (probe_count == 0) {
                            document.getElementById('probe_message').innerHTML = '';
                        }
                    }
                    $("#current_brand_ref_count").empty();
                    $("#current_brand_ref_count").html(data[0].ref_brand_count);

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

function open_tab(evt, tab_name) {
    let i = 0;
    const tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    const tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tab_name).style.display = "block";
    evt.currentTarget.className += " active";
    return false;
}

const validate_project_name = () => {
    $('#brand_name_filter').empty();
    $('#ticket').empty();
    selected_ticket = '';
    const project_name_element = document.getElementById('project_name');
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const project_name_error = document.getElementById('project_name_error');
    const ref_hunt_options = document.getElementById('ref_hunt_options');
    const ref_hunt_counter = document.getElementById('ref_hunt_counter');
    const ticket_section = document.getElementById('ticket_section');
    const hunter_counter = document.getElementById('hunter_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        ref_hunt_options.classList.add('hide');
        ref_hunt_counter.classList.add('hide');
        ticket_section.classList.add('hide');
        hunter_counter.classList.add('hide')
    } else {
        project_name_error.innerHTML = '';
        ticket_section.classList.remove('hide');
        ref_hunt_counter.classList.add('hide');
        ref_hunt_options.classList.add('hide');
        hunter_counter.classList.add('hide')
        p_name = project_name;
        get_ticket_list();
        jQuery('#ref_recognition').val('');
        jQuery('#ref_short_name').val('');
        jQuery('#ref_sub_brand').val('');
        jQuery('#ref_manufacturer').val('');
        jQuery('#ref_category').val('');
        jQuery('#ref_sub_category').val('');
        jQuery('#ref_base_size').val('');
        jQuery('#ref_size').val('');
        jQuery('#ref_measurement_unit').val('');
        jQuery('#ref_container_type').val('');
        jQuery('#ref_agg_level').val('');
        jQuery('#ref_segment').val('');
        jQuery('#ref_upc2').val('');
        jQuery('#ref_flavor_detail').val('');
        jQuery('#ref_case_pack').val('');
        jQuery('#ref_multi_pack').val('');
    }
}

const validate_ticket_name = () => {
    if (p_name != "") {
        $('#brand_name_filter').empty();
        const ticket = jQuery('#ticket').val();
        const probe_hunt_options = document.getElementById('ref_hunt_options');
        const probe_hunt_counter = document.getElementById('ref_hunt_counter');
        const hunter_counter = document.getElementById('hunter_counter');
        const radar_assign = document.getElementById('radar_assign');
        if (ticket == "" || ticket == null) {
            probe_hunt_options.classList.add('hide');
            probe_hunt_counter.classList.add('hide');
            hunter_counter.classList.add('hide');
        } else {
            probe_hunt_options.classList.remove('hide');
            probe_hunt_counter.classList.remove('hide');
            hunter_counter.classList.remove('hide');
            selected_ticket = ticket;
            get_brand_list("brand_name_filter");
        }
    }
}

function show_additional_options() {
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const hunt_information = document.getElementById('hunt_information');
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
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const hunt_information = document.getElementById('hunt_information');
    const product_type_element = document.getElementById('product_type');
    const product_type = product_type_element.options[product_type_element.selectedIndex].value;
    const alt_design_info = document.getElementById('alt_design_info');
    const manu_link_section = document.getElementById('manu_link_section');
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

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#status').select2({
        dropdownParent: $("#add_reference"),
        width: '100%',
    });
    jQuery('#product_type').select2({
        dropdownParent: $("#add_reference"),
        width: '100%',
    });
    jQuery('#status').change(function () {
        show_additional_options();
    });
    jQuery('#project_name').change(function () {
        validate_project_name();
        jQuery('#current_brand_ref_count').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('ref_brand_button').disabled = true;
    });
    jQuery('#ticket').change(function () {
        validate_ticket_name();
        jQuery('#current_brand_ref_count').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('ref_brand_button').disabled = true;
    });
    jQuery("#brand_name_filter").select2({
        width: "100%"
    });
    setInterval(function () { update_ref_count(); }, 500);
    jQuery('#brand_name_filter').on("change", function (e) {
        jQuery('#current_brand_ref_count').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('ref_brand_button').disabled = true;
    });
    $("#show_button").mouseenter(function () {
        document.getElementById('counters').classList.remove('hide');
        document.getElementById('arrow_sec').classList.remove('bounce');
        $("#counters").fadeIn();
    });
    $("#show_button").mouseleave(function () {
        $("#counters").fadeOut();
        document.getElementById('arrow_sec').classList.add('bounce');
    });
    jQuery('#product_type').change(function () {
        show_dvc_options();
    });
    jQuery('#ticket').select2({
        width: '50%',
    });
    $("#add_ref_product").click(function (event) {
        event.preventDefault();
        save_ref_info(true, false);
    });
    $("#submit_probe").click(function (event) {
        event.preventDefault();
        $('#confirm_probe').modal('show');
    });
    $("#confirm_save").click(function (event) {
        event.preventDefault();
        $('#confirm_probe').modal('hide');
        if (save_ref_info(true, true)) {
            reset_ref_form();
            $('#add_reference').modal('hide');
            $('#server_success').empty();
        }
    });
});
