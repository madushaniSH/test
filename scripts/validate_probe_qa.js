var p_name = "";
var product_type = "";
var facing_num = 0;
var selected_ticket = '';
var is_dupilcate = false;
var is_dupilcate_dvc = false;
var org_manu_link = '';

function assign_brand() {
    product_type = "brand";
    get_probe_qa_info();
}

function assign_sku() {
    product_type = "sku";
    get_probe_qa_info();
}

function assign_dvc() {
    product_type = "dvc";
    get_probe_qa_info();
}

function assign_facing() {
    product_type = "facing";
    get_probe_qa_info();
}

function clear_error_form() {
    document.getElementById("error_new_name").value = "";
    document.getElementById("error_new_error").innerHTML = "";
}

function validate_new_error_type() {
    var is_valid_form = true;
    var error_new_name = document.getElementById("error_new_name").value.trim();

    if (error_new_name == "") {
        document.getElementById("error_new_error").innerHTML = "Cannot be empty";
        is_valid_form = false;
    } else {
        document.getElementById("error_new_error").innerHTML = "";
    }

    if (is_valid_form) {
        var formData = new FormData();
        formData.append("error_new_name", error_new_name);
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "add_new_error.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                if (data[0].error != "") {
                    document.getElementById("error_new_error").innerHTML = data[0].error;
                } else {
                    document.getElementById("error_new_error").innerHTML = "";
                    get_error_list();
                    document.getElementById("close_error_form").click();
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

function get_brand_list(product_type, select_element) {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("product_type", product_type);
        formData.append("ticket", selected_ticket);
        jQuery.ajax({
            url: "get_qa_brand_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                let selected_val = $("#" + select_element).val();
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
                if (selected_val != '') {
                    $("#" + select_element).val(selected_val).change();
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

const get_product_name_list = (product_type, select_element) => {
    if (p_name != "") {
        let sku_dvc_name = $("#dvc_name").val();
        if (sku_dvc_name != '' || sku_dvc_name != null) {
            var formData = new FormData();
            formData.append("project_name", p_name);
            formData.append("product_type", product_type);
            formData.append("ticket", selected_ticket);
            formData.append("dvc_name", sku_dvc_name)
            jQuery.ajax({
                url: "get_qa_dvc_products.php",
                type: "POST",
                data: formData,
                dataType: "JSON",
                success: function (data) {
                    // adding missing options
                    let selected_val = $("#" + select_element).val();
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
                    if (selected_val != '') {
                        $("#" + select_element).val(selected_val).change();
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
}

function get_error_list() {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "get_qa_error_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                for (var i = 0; i < data[0].error_rows.length; i++) {
                    if (
                        !$("#error_qa").find(
                            'option[value="' + data[0].error_rows[i].project_error_id + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#error_qa").append(
                            '<option value="' +
                            data[0].error_rows[i].project_error_id +
                            '">' +
                            data[0].error_rows[i].project_error_name +
                            "</option>"
                        );
                    }
                }

                var element = document.getElementById("error_qa").options;
                var found = true;
                for (var i = 0; i < element.length; i++) {
                    found = false;
                    for (var j = 0; j < data[0].error_rows.length; j++) {
                        if (data[0].error_rows[j].project_error_id == element[i].value) {
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        document.getElementById("error_qa").remove(document.getElementById("error_qa")[i]);
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

function display_qa_probe() {
    if (document.getElementById("product_name").value != "") {
        $("#qa_probe").modal("show");
        if (product_type == 'facing') {
            document.getElementById('rename_section').classList.add("hide");
            document.getElementById("alt_rename_section").classList.add("hide");
        } else {
            document.getElementById('rename_section').classList.remove("hide");
            document.getElementById("alt_rename_section").classList.remove("hide");
        }
        if (document.getElementById("alt_name").value == "") {
            document.getElementById("alt_name_section").classList.add("hide");
            document.getElementById("alt_rename_section").classList.add("hide");
        } else {
            document.getElementById("alt_name_section").classList.remove("hide");
            if (product_type != 'facing') {
                document.getElementById("alt_rename_section").classList.remove("hide");
            }
        }
        if (product_type == "brand") {
            document.getElementById("manu_link_section").classList.remove("hide");
        } else {
            document.getElementById("manu_link_section").classList.add("hide");
        }
    }
}

function get_probe_qa_info() {
    var project_name_element = document.getElementById("project_name");
    var project_name =
        project_name_element.options[project_name_element.selectedIndex].value;
    var sku_brand_name = $("#brand_name").val();
    var sku_dvc_name = $("#dvc_product_name").val();
    if (sku_dvc_name == null) {
        sku_dvc_name = $("#dvc_name").val() + ' %';
    }
    var sku_facing_name = $("#facing_name").val();
    var formData = new FormData();
    formData.append("project_name", project_name);
    formData.append("product_type", product_type);
    formData.append("sku_brand_name", sku_brand_name);
    formData.append("sku_dvc_name", sku_dvc_name);
    formData.append("sku_facing_name", sku_facing_name);
    formData.append("ticket", selected_ticket);
    jQuery.ajax({
        url: "assign_qa_product.php",
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
            if (data[0].product_type != null) {
                title_string +=
                    ' <span id="probe_id_title">' + data[0].product_type.toUpperCase() + '</span>';
            }

            if (data[0].probe_id != null) {
                title_string +=
                    ' <span>' + data[0].probe_id + '</span>';
            }
            let dateTimeParts = data[0].time.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
            dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
            const dateObject = new Date(...dateTimeParts);
            title_string +=
                ' <span id="time_title">' + dateObject.toLocaleString() + '</span>';

            jQuery("#qa_probe_title").html(title_string);
            jQuery("#product_name").val(data[0].product_name);
            jQuery("#alt_name").val(data[0].product_alt_design_name);
            org_manu_link = data[0].manufacturer_link;
            jQuery('#manu_link').val(data[0].manufacturer_link);
            var product_link = data[0].product_link;
            if (product_link == null) {
                product_link = '';
                document.getElementById('product_source_button').classList.add('hide');
            } else {
                document.getElementById('product_source_button').classList.remove('hide');
                var str = "Go to Product Source <i class=\"fas fa-external-link-alt\">";
                var result = str.link(product_link);
                document.getElementById('product_source_button').innerHTML = result;
                $('#product_source_button a').attr('target', '_blank');
            }
            if (data[0].manufacturer_link == null) {
                document.getElementById('manu_source_button').classList.add('hide');
            } else {
                document.getElementById('manu_source_button').classList.remove('hide');
                var str = "<i class=\"fas fa-external-link-alt\">";
                var result = str.link(data[0].manufacturer_link);
                document.getElementById('manu_source_button').innerHTML = result;
                $('#manu_source_button a').attr('target', '_blank');
            }
            document.getElementById("num_facings").value = data[0].product_facing_count;
            facing_num = data[0].product_facing_count;
            document.getElementById("output").innerHTML = document.getElementById("num_facings").value;
            if (data[0].product_alt_design_previous != null) {
                jQuery('#name_error').html('Orignal name was overwritten by an Analyst');
            }
            display_qa_probe();
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function unassign_probe() {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "unassign_qa.php",
            type: "POST",
            data: formData,
            success: function (data) { },
            error: function (data) {
                alert("Error fetching probe information. Please refresh");
                clearInterval(request);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
    document.getElementById("qa_form").reset();
    $("#error_qa")
        .val("")
        .trigger("change");
    document.getElementById("product_rename_error").innerHTML = "";
    document.getElementById("product_alt_rename_error").innerHTML = "";
    document.getElementById("status_error").innerHTML = "";
    document.getElementById("error_qa_error").innerHTML = "";
    document.getElementById("error_facing_error").innerHTML = "";
    document.getElementById("error_link_error").innerHTML = "";
    document.getElementById("manu_error").innerHTML = "";
    document.getElementById("image_error").innerHTML = "";
    document.getElementById("name_error").innerHTML = "";
    document.getElementById("num_facings").value = 0;
    document.getElementById("output").innerHTML = 0;
    is_dupilcate = false;
    is_dupilcate_dvc = false;
    rename_alert.classList.add("hide");
    dvc_rename_alert.classList.add("hide");
    facing_num = 0;
}

function update_project_qa_count() {
    if (p_name != "") {
        get_brand_list("sku", "brand_name");
        get_brand_list("dvc", "dvc_name");
        get_brand_list("facing", "facing_name");
        get_product_name_list("dvc", "dvc_product_name");
        get_error_list();
        var sku_brand_name = $("#brand_name").val();
        var sku_dvc_name = $("#dvc_product_name").val();
        if (sku_dvc_name == null) {
            sku_dvc_name = $("#dvc_name").val() + ' %';
        }
        var sku_facing_name = $('#facing_name').val();
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("sku_brand_name", sku_brand_name);
        formData.append("sku_dvc_name", sku_dvc_name);
        formData.append("sku_facing_name", sku_facing_name);
        formData.append('ticket', selected_ticket);
        jQuery.ajax({
            url: "fetch_probe_qa_count.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                var output_count;
                if (data[0].brand_count != null) {
                    var total_count = 0;
                    var probe_count = parseInt(data[0].processing_probe_row, 10);
                    var product_type = data[0].product_type;
                    var display_message = "";

                    if (product_type == "brand" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = true;
                        document.getElementById("sku_qa_button").disabled = true;
                        document.getElementById("brand_qa_button").disabled = false;
                        document.getElementById("facing_qa_button").disabled = true;
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else if (product_type == "sku" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = true;
                        document.getElementById("sku_qa_button").disabled = false;
                        document.getElementById("brand_qa_button").disabled = true;
                        document.getElementById("facing_qa_button").disabled = true;
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else if (product_type == "dvc" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = false;
                        document.getElementById("sku_qa_button").disabled = true;
                        document.getElementById("brand_qa_button").disabled = true;
                        document.getElementById("facing_qa_button").disabled = true;
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else if (product_type == "facing" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = true;
                        document.getElementById("sku_qa_button").disabled = true;
                        document.getElementById("brand_qa_button").disabled = true;
                        document.getElementById("facing_qa_button").disabled = false;
                        display_message = product_type.toUpperCase() + " already assigned";
                    }
                    else {
                        document.getElementById("sku_qa_button").disabled = false;
                        document.getElementById("dvc_qa_button").disabled = false;
                        document.getElementById("brand_qa_button").disabled = false;
                        document.getElementById("facing_qa_button").disabled = false;
                        display_message = "";
                    }

                    $("#probe_qa_message").html(display_message);

                    $("#current_brand_count").empty();
                    $("#current_brand_count").html(data[0].brand_count);
                    var brand_count = parseInt(data[0].brand_user_count, 10);
                    if (brand_count == 0 && product_type != "brand") {
                        document.getElementById("brand_qa_button").disabled = true;
                    }
                    $("#current_sku_count").empty();
                    $("#current_sku_count").html(data[0].sku_count);
                    var sku_count = parseInt(data[0].brand_sku_count, 10);
                    if (sku_count == 0 && product_type != "sku") {
                        document.getElementById("sku_qa_button").disabled = true;
                    }
                    $("#current_dvc_count").empty();
                    $("#current_dvc_count").html(data[0].dvc_count);
                    var dvc_count = parseInt(data[0].brand_dvc_count, 10);
                    if (dvc_count == 0 && product_type != "dvc") {
                        document.getElementById("dvc_qa_button").disabled = true;
                    }

                    $("#current_facing_count").empty();
                    $("#current_facing_count").html(data[0].facing_count);
                    var facing_count = parseInt(data[0].facing_sku_count, 10);
                    if (facing_count == 0 && product_type != "facing") {
                        document.getElementById("facing_qa_button").disabled = true;
                    }

                    $("#current_brand_count_2").empty();
                    $("#current_brand_count_2").html(data[0].brand_user_count);

                    $("#current_sku_count_2").empty();
                    $("#current_sku_count_2").html(data[0].brand_sku_count);

                    $("#current_dvc_count_2").empty();
                    $("#current_dvc_count_2").html(data[0].brand_dvc_count);


                    $("#current_facing_count_2").empty();
                    $("#current_facing_count_2").html(data[0].facing_sku_count);
                } else {
                    $("#current_brand_count").html("XX");
                    $("#current_sku_count").html("XX");
                    $("#current_dvc_count").html("XX");
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
function validate_ticket_name() {
    if (p_name != "") {
        var ticket = jQuery('#ticket').val();
        var probe_qa_options = document.getElementById("probe_qa_options");
        var probe_hunt_section = document.getElementById("probe_hunt_section");
        var ticket_section = document.getElementById('ticket_section');
        var counters = document.getElementById("counters");
        if (ticket == "" || ticket == null) {
            probe_qa_options.classList.add("hide");
            probe_hunt_section.classList.add("hide");
            counters.classList.add("hide");
        } else {
            probe_qa_options.classList.remove("hide");
            probe_hunt_section.classList.remove("hide");
            counters.classList.remove("hide");
            selected_ticket = ticket;
            get_brand_list("sku", "brand_name");
            get_brand_list("dvc", "dvc_name");
            get_brand_list("facing", "facing_name");
            get_product_name_list("dvc", "dvc_product_name");
        }
    }
}


function validate_project_name() {
    var project_name_element = document.getElementById("project_name");
    var project_name =
        project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById("project_name_error");
    var probe_qa_options = document.getElementById("probe_qa_options");
    var probe_hunt_section = document.getElementById("probe_hunt_section");
    var ticket = document.getElementById('ticket_section');
    var counters = document.getElementById("counters");
    if (project_name == "") {
        project_name_error.innerHTML = "Project Name required for upload";
        probe_qa_options.classList.add("hide");
        probe_hunt_section.classList.add("hide");
        counters.classList.add("hide");
        ticket.classList.add("hide");
    } else {
        $('#ticket').empty();
        project_name_error.innerHTML = "";
        probe_qa_options.classList.add("hide");
        probe_hunt_section.classList.add("hide");
        counters.classList.add("hide");
        p_name = project_name;
        ticket.classList.remove("hide");
        get_ticket_list();
        $("#brand_name").empty();
        $("#dvc_name").empty();
        $("#facing_name").empty();
    }
}

function show_error_image_section() {
    var error_qa = $("#error_qa").val();
    if (error_qa.length == 0) {
        document.getElementById("error_image_section").classList.add("hide");
    } else {
        document.getElementById("error_image_section").classList.remove("hide");
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

function validate_qa_form() {
    var is_valid_form = true;
    var product_rename = document.getElementById("product_rename").value.trim();
    var product_alt_rename = document.getElementById("product_alt_rename").value.trim();
    var error_qa = $("#error_qa").val();
    var approve_button = document.getElementById("approve");
    var disapprove_button = document.getElementById("disapprove");
    var error_images = document.getElementById("error_images").files;
    var manu_link = document.getElementById('manu_link').value.trim();
    if (product_type == "brand" || product_type == "sku") {
        if (product_rename == "") {
            document.getElementById("product_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_rename_error").innerHTML = "";
        }
    } else if (product_type == "dvc") {
        if (product_rename == "") {
            document.getElementById("product_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_rename_error").innerHTML = "";
        }
        if (product_alt_rename == "") {
            document.getElementById("product_alt_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_alt_rename_error").innerHTML = "";
        }
    } else if (product_type != "facing") {
        is_valid_form = false;
    }
    if (!approve_button.checked && !disapprove_button.checked) {
        document.getElementById("status_error").innerHTML =
            "Status must be selected";
        is_valid_form = false;
    } else {
        document.getElementById("status_error").innerHTML = "";
    }

    if (product_type == "brand" && manu_link != org_manu_link && error_qa.length == 0) {
        document.getElementById('error_link_error').innerHTML = 'Manufactuer URL changed. Error Type must be selected';
        is_valid_form = false;
    } else {
        document.getElementById('error_link_error').innerHTML = '';
    }

    if (product_type == "brand" && manu_link == "") {
        document.getElementById('manu_error').innerHTML = 'URL cannot be empty';
        is_valid_form = false;
    } else {
        document.getElementById('manu_error').innerHTML = '';
        if (!is_url(manu_link) && product_type == "brand") {
            document.getElementById('manu_error').innerHTML = 'Invalid URL';
            is_valid_form = false;
        } else {
            document.getElementById('manu_error').innerHTML = '';
        }
    }

    if (disapprove_button.checked && error_qa.length == 0) {
        document.getElementById("error_qa_error").innerHTML = "Error Type must be selected.";
        is_valid_form = false;
    } else {
        document.getElementById("error_qa_error").innerHTML = "";
    }

    if (disapprove_button.checked && error_images.length == 0) {
        document.getElementById("image_error").innerHTML =
            "At least one image must be selected for upload";
        is_valid_form = false;
    } else {
        document.getElementById("image_error").innerHTML = "";
    }

    if (is_valid_form && !is_dupilcate && !is_dupilcate_dvc) {
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("product_type", product_type);
        formData.append("product_rename", product_rename);
        formData.append("error_image_count", error_images.length);
        formData.append("product_alt_rename", product_alt_rename);
        formData.append("error_qa", error_qa);
        formData.append("num_facings", document.getElementById("num_facings").value)
        formData.append("manu_link", manu_link);
        for (var i = 0; i < error_images.length; i++) {
            formData.append("error_images" + i, document.getElementById('error_images').files[i]);
        }
        if (disapprove_button.checked) {
            formData.append('status', 'disapproved');
        } else {
            formData.append('status', 'approved');
        }
        jQuery.ajax({
            url: "process_qa.php",
            type: "POST",
            data: formData,
            success: function (data) {
                $("#qa_probe").modal("hide");
                document.getElementById("qa_form").reset();
                $("#error_qa")
                    .val("")
                    .trigger("change");
                document.getElementById("product_rename_error").innerHTML = "";
                document.getElementById("product_alt_rename_error").innerHTML = "";
                document.getElementById("status_error").innerHTML = "";
                document.getElementById("error_qa_error").innerHTML = "";
                document.getElementById("error_facing_error").innerHTML = "";
                document.getElementById("error_link_error").innerHTML = "";
                document.getElementById("manu_error").innerHTML = "";
                document.getElementById("image_error").innerHTML = "";
                document.getElementById("name_error").innerHTML = "";
                rename_alert.classList.add("hide");
                dvc_rename_alert.classList.add("hide");
            },
            error: function (data) {
                alert("Error fetching probe information. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
        facing_num = 0;
    }
}

function compare_rename() {
    if (product_type == "brand" || "sku") {
        var product_name = document.getElementById("product_name").value.trim();
        var product_rename = document.getElementById("product_rename").value.trim();
        var rename_alert = document.getElementById("rename_alert");
        if (product_name != product_rename) {
            rename_alert.classList.remove("hide");
            var formData = new FormData();
            formData.append("project_name", p_name);
            formData.append("product_name", product_rename);
            jQuery.ajax({
                url: "check_duplicate_sku.php",
                type: "POST",
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    var row_count = parseInt(data[0].row_count, 10);
                    if (row_count != 0) {
                        is_dupilcate = true;
                        document.getElementById('product_dup_rename_error').innerHTML = 'Product Name Already Exists';
                    } else {
                        is_dupilcate = false;
                        document.getElementById('product_dup_rename_error').innerHTML = '';
                    }
                },
                error: function (data) {
                    alert("Error fetching probe information. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            rename_alert.classList.add("hide");
            is_dupilcate = false;
            document.getElementById('product_dup_rename_error').innerHTML = '';
        }
    }
}

function compare_alt_rename() {
    if (product_type == "dvc") {
        var alt_name = document.getElementById("alt_name").value.trim();
        var product_alt_rename = document
            .getElementById("product_alt_rename")
            .value.trim();
        var dvc_rename_alert = document.getElementById("dvc_rename_alert");
        if (alt_name != product_alt_rename) {
            dvc_rename_alert.classList.remove("hide");
            var formData = new FormData();
            formData.append("project_name", p_name);
            formData.append("product_name", product_alt_rename);
            jQuery.ajax({
                url: "check_duplicate_dvc.php",
                type: "POST",
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    var row_count = parseInt(data[0].row_count, 10);
                    if (row_count != 0) {
                        is_dupilcate_dvc = true;
                        document.getElementById('product_alt_dup_rename_error').innerHTML = 'DVC Name Already Exists';
                    } else {
                        is_dupilcate_dvc = false;
                        document.getElementById('product_alt_dup_rename_error').innerHTML = '';
                    }
                },
                error: function (data) {
                    alert("Error fetching probe information. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            dvc_rename_alert.classList.add("hide");
            is_dupilcate_dvc = false;
            document.getElementById('product_alt_dup_rename_error').innerHTML = '';
        }
    }
}

jQuery(document).ready(function () {
    jQuery("#project_name").select2({
        width: "100%"
    });
    jQuery("#project_name").change(function () {
        validate_project_name();
    });
    jQuery("#brand_name").select2({
        width: "100%"
    });
    jQuery("#dvc_name").select2({
        width: "100%"
    });
    jQuery("#facing_name").select2({
        width: "100%"
    })
    jQuery("#dvc_product_name").select2({
        width: "100%"
    })
    jQuery("#error_qa").select2({
        dropdownParent: $("#qa_probe"),
        width: "100%"
    });
    jQuery("#error_images").fileinput({
        maxFileSize: '1024',
        showCancel: false,
        showUpload: false,
        maxFileCount: 4,
        allowedFileExtensions: ["png", "jpg", "jpeg"]
    });
    jQuery('#ticket').select2({
        width: '50%',
    });
    jQuery('#brand_name').on("change", function (e) {
        jQuery('#current_sku_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('sku_qa_button').disabled = true;
    });
    jQuery('#facing_name').on("change", function (e) {
        jQuery('#current_facing_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('facing_qa_button').disabled = true;
    });
    jQuery('#dvc_name').on("change", function (e) {
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('dvc_qa_button').disabled = true;
    });
    jQuery('#dvc_product_name').on("change", function (e) {
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('dvc_qa_button').disabled = true;
    });
    jQuery('#ticket').on("change", function (e) {
        jQuery('#current_brand_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('dvc_qa_button').disabled = true;
        jQuery('#current_sku_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('sku_qa_button').disabled = true;
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('dvc_qa_button').disabled = true;
        jQuery('#current_facing_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>")
        document.getElementById('facing_qa_button').disabled = true;
    });
    $("#error_qa").on("change", function (e) {
        show_error_image_section();
    });
    $("#product_rename").on("change", function () {
        compare_rename();
    });
    $("#product_alt_rename").on("change", function () {
        compare_alt_rename();
    });
    setInterval(function () {
        update_project_qa_count();
    }, 1000);
    $('#ticket').on('select2:select', function (e) {
        validate_ticket_name();
    });
});
