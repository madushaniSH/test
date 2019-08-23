var p_name = "";
var product_type = "";

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
        jQuery.ajax({
            url: "get_qa_brand_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                for (var i = 0; i < data[0].brand_rows.length; i++) {
                    if (
                        !$("#" + select_element).find(
                            "option[value='" + data[0].brand_rows[i].name + "']"
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
                            "option[value='" + data[0].error_rows[i].project_error_id + "']"
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

function display_qa_probe(product_type) {
    if (document.getElementById("product_name").value != "") {
        $("#qa_probe").modal("show");
        if (document.getElementById("alt_name").value == "") {
            document.getElementById("alt_name_section").classList.add("hide");
            document.getElementById("alt_rename_section").classList.add("hide");
        } else {
            document.getElementById("alt_name_section").classList.remove("hide");
            document.getElementById("alt_rename_section").classList.remove("hide");
        }
        if (product_type != "dvc") {
            document.getElementById("rename_section").classList.remove("hide");
        } else {
            document.getElementById("rename_section").classList.add("hide");
        }
    }
}

function get_probe_qa_info() {
    var project_name_element = document.getElementById("project_name");
    var project_name =
        project_name_element.options[project_name_element.selectedIndex].value;
    var sku_brand_name = $("#brand_name").val();
    var sku_dvc_name = $("#dvc_name").val();
    var formData = new FormData();
    formData.append("project_name", project_name);
    formData.append("product_type", product_type);
    formData.append("sku_brand_name", sku_brand_name);
    formData.append("sku_dvc_name", sku_dvc_name);
    jQuery.ajax({
        url: "assign_qa_product.php",
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name + "</span>";
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

            jQuery("#qa_probe_title").html(title_string);
            jQuery("#product_name").val(data[0].product_name);
            jQuery("#alt_name").val(data[0].product_alt_design_name);
            display_qa_probe(product_type);
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
    document.getElementById("image_error").innerHTML = "";
    rename_alert.classList.add("hide");
    dvc_rename_alert.classList.add("hide");
}

function update_project_qa_count() {
    if (p_name != "") {
        get_brand_list("sku", "brand_name");
        get_brand_list("dvc", "dvc_name");
        get_error_list();
        var sku_brand_name = $("#brand_name").val();
        var sku_dvc_name = $("#dvc_name").val();
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("sku_brand_name", sku_brand_name);
        formData.append("sku_dvc_name", sku_dvc_name);
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
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else if (product_type == "sku" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = true;
                        document.getElementById("sku_qa_button").disabled = false;
                        document.getElementById("brand_qa_button").disabled = true;
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else if (product_type == "dvc" && probe_count == 1) {
                        document.getElementById("dvc_qa_button").disabled = false;
                        document.getElementById("sku_qa_button").disabled = true;
                        document.getElementById("brand_qa_button").disabled = true;
                        display_message = product_type.toUpperCase() + " already assigned";
                    } else {
                        document.getElementById("sku_qa_button").disabled = false;
                        document.getElementById("dvc_qa_button").disabled = false;
                        document.getElementById("brand_qa_button").disabled = false;
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
                    $("#current_brand_count_2").empty();
                    $("#current_brand_count_2").html(data[0].brand_user_count);

                    $("#current_sku_count_2").empty();
                    $("#current_sku_count_2").html(data[0].brand_sku_count);

                    $("#current_dvc_count_2").empty();
                    $("#current_dvc_count_2").html(data[0].brand_dvc_count);
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

function validate_project_name() {
    var project_name_element = document.getElementById("project_name");
    var project_name =
        project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById("project_name_error");
    var probe_qa_options = document.getElementById("probe_qa_options");
    var probe_hunt_section = document.getElementById("probe_hunt_section");
    var counters = document.getElementById("counters");
    if (project_name == "") {
        project_name_error.innerHTML = "Project Name required for upload";
        probe_qa_options.classList.add("hide");
        probe_hunt_section.classList.add("hide");
        counters.classList.add("hide");
    } else {
        project_name_error.innerHTML = "";
        probe_qa_options.classList.remove("hide");
        probe_hunt_section.classList.remove("hide");
        counters.classList.remove("hide");
        p_name = project_name;
        get_brand_list("sku", "brand_name");
        get_brand_list("dvc", "dvc_name");
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

function validate_qa_form() {
    var is_valid_form = true;
    var product_rename = document.getElementById("product_rename").value.trim();
    var product_alt_rename = document
        .getElementById("product_alt_rename")
        .value.trim();
    var error_qa = $("#error_qa").val();
    var approve_button = document.getElementById("approve");
    var disapprove_button = document.getElementById("disapprove");
    var error_images = document.getElementById("error_images").files;
    if (product_type == "brand" || product_type == "sku") {
        if (product_rename == "") {
            document.getElementById("product_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_rename_error").innerHTML = "";
        }
    } else if (product_type == "dvc") {
        if (product_alt_rename == "") {
            document.getElementById("product_alt_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_alt_rename_error").innerHTML = "";
        }
    } else {
        is_valid_form = false;
    }
    if (!approve_button.checked && !disapprove_button.checked) {
        document.getElementById("status_error").innerHTML =
            "Status must be selected";
        is_valid_form = false;
    } else {
        document.getElementById("status_error").innerHTML = "";
    }
    if (disapprove_button.checked && error_qa.length == 0) {
        document.getElementById("error_qa_error").innerHTML =
            "Error Type must be selected";
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

    if (is_valid_form) {
        var formData = new FormData();
        formData.append("project_name", p_name);
        formData.append("product_type", product_type);
        formData.append("product_rename", product_rename);
        formData.append("error_qa", error_qa);
        formData.append("error_image_count", error_images.length);
        for (var i = 0; i < error_images.length; i++) {
            formData.append("error_images"+i, document.getElementById('error_images').files[i]);
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
                document.getElementById("image_error").innerHTML = "";
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
    }
}

function compare_rename() {
    if (product_type == "brand" || "sku") {
        var product_name = document.getElementById("product_name").value.trim();
        var product_rename = document.getElementById("product_rename").value.trim();
        var rename_alert = document.getElementById("rename_alert");
        if (product_name != product_rename) {
            rename_alert.classList.remove("hide");
        } else {
            rename_alert.classList.add("hide");
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
        } else {
            dvc_rename_alert.classList.add("hide");
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
    jQuery("#error_qa").select2({
        dropdownParent: $("#qa_probe"),
        width: "100%"
    });
    jQuery("#error_images").fileinput({
        maxFileSize: '1024',
        showCancel: false,
        showUpload: false,
        maxFileCount: 4,
        allowedFileExtensions: ["jpg", "jpeg"]
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
});
