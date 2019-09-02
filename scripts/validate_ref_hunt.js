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
                            document.getElementById('probe_message').innerHTML = "Reference already Assigned for brand " + data[0].brand_name  + ". Please press Continue";
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
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#project_name').change(function () {
        validate_project_name();
        jQuery('#current_brand_ref_count').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('ref_brand_button').disabled = true;
    });
    jQuery("#brand_name_filter").select2({
        width: "80%"
    });
    setInterval(function () { update_ref_count(); }, 500);
    jQuery('#brand_name_filter').on("change", function (e) {
        jQuery('#current_brand_ref_count').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('ref_brand_button').disabled = true;
    });
});