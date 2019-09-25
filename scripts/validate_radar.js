let p_name = '';
let selected_ticket = '';
let product_count = 0;
let is_confirmed = false;
let suggestion_count = 0;
const get_ticket_list = () => {
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
const validate_project_name = () => {
    selected_ticket = '';
    const project_name_element = document.getElementById('project_name');
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const project_name_error = document.getElementById('project_name_error');
    const probe_hunt_counter = document.getElementById('probe_hunt_counter');
    const hunter_counter = document.getElementById('hunter_counter');
    const ticket_section = document.getElementById('ticket_section');
    const probe_hunt_options = document.getElementById('probe_hunt_options');
    const radar_assign = document.getElementById('radar_assign');
    $('#brand_name_filter').empty();
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
        probe_hunt_options.classList.add('hide');
    } else {
        $('#ticket').empty();
        $("#brand_name_filter").val("").trigger("change");
        project_name_error.innerHTML = '';
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
        ticket_section.classList.remove('hide');
        probe_hunt_options.classList.add('hide');
        p_name = project_name;
        get_ticket_list();
    }
}
const validate_ticket_name = () => {
    if (p_name != "") {
        $("#brand_name_filter").empty();
        $("#brand_name_filter").val("").trigger("change");
        const ticket = jQuery('#ticket').val();
        const probe_hunt_options = document.getElementById('probe_hunt_options');
        const probe_hunt_counter = document.getElementById('probe_hunt_counter');
        const hunter_counter = document.getElementById('hunter_counter');
        const radar_assign = document.getElementById('radar_assign');
        $('#brand_name_filter').empty();
        $("#brand_name_filter").val("").trigger("change");
        if (ticket == "" || ticket == null) {
            probe_hunt_options.classList.add('hide');
            probe_hunt_counter.classList.add('hide');
            hunter_counter.classList.add('hide');
        } else {
            probe_hunt_options.classList.remove('hide');
            probe_hunt_counter.classList.remove('hide');
            hunter_counter.classList.remove('hide');
            selected_ticket = ticket;
            get_client_cat("brand_name_filter");
        }
    }
}

const validate_client_cat = () => {
    if (p_name != '') {
        const client_cat = $('#brand_name_filter').val();
        const radar_assign = document.getElementById('radar_assign');
    }
}

const update_ref_count = () => {
    if (p_name != '' && selected_ticket != '') {
        get_client_cat("brand_name_filter");
        let formData = new FormData();
        formData.append('project_name', p_name);
        var radar_cat = $("#brand_name_filter").val();
        formData.append("radar_cat", radar_cat);
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
            url: 'fetch_radar_count.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                if (data[0].number_of_rows != null) {
                    $('#radar_brands').empty();
                    $('#radar_brands').html(data[0].number_of_rows);
                    $('#radar_brand_handle').empty();
                    $('#radar_brand_handle').html(data[0].number_of_handled_rows);
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
                    $('#suggestion_count').empty();
                    $('#suggestion_count').html(data[0].current_link_count);
                    suggestion_count = data[0].current_link_count;
                    var count = parseInt(data[0].number_of_rows, 10);
                    var probe_count = parseInt(data[0].processing_probe_row, 10);
                    if (count == 0 && probe_count == 0) {
                        document.getElementById('radar_button').disabled = true;
                        document.getElementById('probe_message').innerHTML = '';
                        if ($('#add_reference').is(':visible')) {
                            $('#close_probe_title').click();
                        }
                    } else {
                        document.getElementById('radar_button').disabled = false;
                        if (probe_count === 1) {
                            document.getElementById('probe_message').innerHTML = "Radar already assigned for Category " + data[0].radar_cat;
                        }
                        if (probe_count == 0) {
                            document.getElementById('probe_message').innerHTML = '';
                        }
                    }
                    $("#current_cat_radar").empty();
                    $("#current_cat_radar").html(data[0].radar_cat_count);

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

const get_client_cat = (select_element) => {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("ticket", selected_ticket);
        jQuery.ajax({
            url: "get_radar_client_cat.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                for (var i = 0; i < data[0].cat_rows.length; i++) {
                    if (
                        !$("#" + select_element).find(
                            'option[value="' + data[0].cat_rows[i].name + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#" + select_element).append(
                            '<option value="' +
                            data[0].cat_rows[i].name +
                            '">' +
                            data[0].cat_rows[i].name +
                            "</option>"
                        );
                    }
                }

                var element = document.getElementById(select_element).options;
                var found = true;
                for (var i = 0; i < element.length; i++) {
                    found = false;
                    for (var j = 0; j < data[0].cat_rows.length; j++) {
                        if (data[0].cat_rows[j].name == element[i].value) {
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
                $("#brand_name_filter").trigger("change");
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

const get_radar_info = () => {
    const project_name_element = document.getElementById("project_name");
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const client_cat = $("#brand_name_filter").val();
    let formData = new FormData();
    formData.append("project_name", project_name);
    formData.append("client_cat", client_cat);
    formData.append("ticket", selected_ticket);
    jQuery.ajax({
        url: "assign_radar.php",
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name + ' ' + data[0].ticket + "</span>";
            if (data[0].brand_name != null) {
                title_string +=
                    ' <span id="brand_title">' + data[0].brand_name + "</span>";
            }
            if (data[0].client_category_name != null) {
                title_string +=
                    ' <span id="client_category_title">' +
                    data[0].client_category_name +
                    "</span>";
            }
            jQuery("#add_radar_title").html(title_string);
            $("#add_radar").modal("show");
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
}

const show_additional_options = () => {
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const hunt_information = document.getElementById('hunt_information');
    const save_sec = document.getElementById('save_sec');
    save_sec.classList.remove('hide');
    if (status === '2') {
        hunt_information.classList.remove('hide');
        save_sec.classList.add('hide');
    } else {
        hunt_information.classList.add('hide');
        save_sec.classList.remove('hide');
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
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,\#\\\:\=\$]*$/;
        if (!patt.test(product_name)) {
            is_valid_form = false;
            product_name_error.innerHTML = 'Non English Product Name Entered';
        } else {
            product_name_error.innerHTML = '';
        }
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
        var patt = /^[a-zA-Z 0-9\-\'\!\%\&\(\)\.\_\/\+\,\#\\\:\=\$]*$/;
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

const save_radar_source = (save_radar, close_radar) => {
    let is_valid_form = true;
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
    const status_error = document.getElementById('status_error');
    const suggestion_source = document.getElementById('source').value.trim();
    const source_error = document.getElementById('source_error');
    const comment = document.getElementById('comment').value.trim();
    const server_success = document.getElementById('server_success');
    if (suggestion_count > 0 && close_radar && status == '' && suggestion_source == '') {
        let formData = new FormData();
        formData.append('project_name', p_name);
        jQuery.ajax({
            url: "close_source.php",
            type: "POST",
            data: formData,
            success: function (data) {
                reset_radar_form();
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
                toastr.success('Suggestion Cleared');
            },
            error: function (data) {
                alert("Error assigning probe. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    } else {
        if (status === '') {
            status_error.innerHTML = 'Status must be selected';
            is_valid_form = false;
        } else {
            status_error.innerHTML = '';
        }
        if (suggestion_source === '') {
            source_error.innerHTML = 'Source cannot be left blank';
            is_valid_form = false;
        } else {
            source_error.innerHTML = '';
            if (!is_url(suggestion_source)) {
                source_error.innerHTML = 'Invalid source URL';
                is_valid_form = false;
            } else {
                source_error.innerHTML = '';
            }
        }
        const product_name = document.getElementById('product_name').value.trim();
        const product_type_element = document.getElementById('product_type');
        const product_type = product_type_element.options[product_type_element.selectedIndex].value;
        let flag = false;
        if (status == 2) {
            if (product_count >= 1) {
                if ((product_name !== '' || product_type !== '') || (!save_radar)) {
                    if (!validate_product_info()) {
                        is_valid_form = false;
                    }
                } else {
                    flag = true;
                }
            } else {
                if (!validate_product_info()) {
                    is_valid_form = false;
                }
            }
        }
        if (flag) {
            reset_radar_form();
            reset_hunt_information();
        } else {
            if (is_valid_form && status != 2) {
                let formData = new FormData();
                formData.append('project_name', p_name);
                formData.append('status', status);
                formData.append('source', suggestion_source);
                formData.append('comment', comment);
                jQuery.ajax({
                    url: "process_source.php",
                    type: "POST",
                    data: formData,
                    success: function (data) {
                        reset_radar_form();
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
                        toastr.success('Details Added');
                    },
                    error: function (data) {
                        alert("Error assigning probe. Please refresh");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
                suggestion_count++;
                reset_hunt_information();
            } else if (is_valid_form && status == 2) {
                const alt_design_name = document.getElementById('alt_design_name').value.trim();
                const alt_design_name_error = document.getElementById('alt_design_name_error');
                const facings = document.getElementById("num_facings").value;
                let manu_link = document.getElementById('manu_link').value.trim();
                const product_link = document.getElementById('product_link').value.trim();
                if (product_type != 'brand') {
                    manu_link = '';
                }
                let formData = new FormData();
                formData.append('project_name', p_name);
                formData.append('status', status);
                formData.append('source', suggestion_source);
                formData.append('comment', comment);
                formData.append('product_name', product_name);
                formData.append('product_type', product_type);
                formData.append('alt_design_name', alt_design_name);
                formData.append('manu_link', manu_link);
                formData.append('product_link', product_link);
                formData.append('facings', facings);
                status_element.disabled = true;
                document.getElementById('source').disabled = true;
                jQuery.ajax({
                    url: 'add_radar_product.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    success: function (data) {
                        if (data[0].success != '') {
                            server_success.innerHTML = data[0].success;
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
                            toastr.success('Details Added');
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
                product_count++;
                suggestion_count++;
                reset_hunt_information();
            }
        }
        if (close_radar && is_valid_form) {
            let formData = new FormData();
            formData.append('project_name', p_name);
            jQuery.ajax({
                url: "close_source.php",
                type: "POST",
                data: formData,
                success: function (data) {
                    reset_radar_form();
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
                    toastr.success('Suggestion Cleared');
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

const reset_radar_form = () => {
    $("#status").val('').trigger('change');
    $("#probe_form").trigger('reset');
    $('#status_error').empty();
    $('#source_error').empty();
    document.getElementById('source').disabled = false;
    document.getElementById('status').disabled = false;
    document.getElementById('server_success').innerHTML = '';
    product_count = 0;
}

const show_dvc_options = () => {
    const status_element = document.getElementById('status');
    const status = status_element.options[status_element.selectedIndex].value;
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

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#status').select2({
        dropdownParent: $("#add_radar"),
        width: '100%',
    });
    jQuery('#product_type').select2({
        dropdownParent: $("#add_radar"),
        width: '100%',
    });
    jQuery('#status').change(function () {
        show_additional_options();
    });
    jQuery('#ticket').select2({
        width: '50%',
    });
    jQuery('#brand_name_filter').select2({
        width: '100%',
    });
    jQuery('#ticket').on('select2:select', function () {
        validate_ticket_name();
    });
    jQuery('#project_name').on('select2:select', function () {
        validate_project_name();
    });
    jQuery('#brand_name_filter').change(function () {
        validate_client_cat();
    });
    $("#show_button").mouseleave(function () {
        $("#counters").fadeOut();
        document.getElementById('arrow_sec').classList.add('bounce');
    });
    $("#show_button").mouseenter(function () {
        document.getElementById('counters').classList.remove('hide');
        document.getElementById('arrow_sec').classList.remove('bounce');
        $("#counters").fadeIn();
    });
    setInterval(function () { update_ref_count(); }, 500);
    $("#continue_one").click(function (event) {
        event.preventDefault();
        save_radar_source(true, false);
    });
    $("#plus_product").click(function (event) {
        event.preventDefault();
        save_radar_source(false, false);
    });
    $("#continue_two").click(function (event) {
        event.preventDefault();
        if (save_radar_source(true, false)) {
            reset_radar_form();
        }
    });
    $("#submit_probe").click(function (event) {
        event.preventDefault();
        $('#confirm_probe').modal('show');
    });
    $("#confirm_save").click(function (event) {
        event.preventDefault();
        confirm_save = true;
        $('#confirm_probe').modal('hide');
        if (save_radar_source(true, true)) {
            reset_radar_form();
            suggestion_count = 0;
            $('#add_radar').modal('hide');
        }
    });
    jQuery('#product_type').change(function () {
        show_dvc_options();
    });
});