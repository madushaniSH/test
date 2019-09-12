var p_name = '';

function get_brand_list(select_element) {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
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

function get_ref_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    var sku_brand_name = $("#brand_name_filter").val();
    formData.append("sku_brand_name", sku_brand_name);
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
    if (p_name != '') {
        get_brand_list("brand_name_filter");
        var formData = new FormData();
        formData.append('project_name', p_name);
        var sku_brand_name = $("#brand_name_filter").val();
        formData.append("sku_brand_name", sku_brand_name);
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
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tab_name).style.display = "block";
    evt.currentTarget.className += " active";
    return false;
}


function validate_project_name() {
    $('#brand_name_filter').empty();
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

function show_additional_options() {
    var status_element = document.getElementById('status');
    var status = status_element.options[status_element.selectedIndex].value;
    var hunt_information = document.getElementById('ref_product_information');
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
});