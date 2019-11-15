// object used for storing user selection options
let this_selection_info = {
    project_name: '',
    ticket_selection: [],
    client_cat: '',
    referenceQaSelected: false,
    selectedProductType: '',
};
let facing_num = 0;
let is_dupilcate = false;
let is_dupilcate_dvc = false;
let org_manu_link = '';

function clear_error_form() {
    document.getElementById("error_new_name").value = "";
    document.getElementById("error_new_error").innerHTML = "";
}

function validate_new_error_type() {
    let is_valid_form = true;
    const error_new_name = document.getElementById("error_new_name").value.trim();

    if (error_new_name === "") {
        document.getElementById("error_new_error").innerHTML = "Cannot be empty";
        is_valid_form = false;
    } else {
        document.getElementById("error_new_error").innerHTML = "";
    }

    if (is_valid_form) {
        let formData = new FormData();
        formData.append("error_new_name", error_new_name);
        formData.append("project_name", this_selection_info.project_name);
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

function compare_rename() {
    if (this_selection_info.selectedProductType === "brand" || "sku") {
        let product_name = document.getElementById("product_name").value.trim();
        let product_rename = document.getElementById("product_rename").value.trim();
        let rename_alert = document.getElementById("rename_alert");
        if (product_name === product_rename) {
            rename_alert.classList.add("hide");
            is_dupilcate = false;
            document.getElementById('product_dup_rename_error').innerHTML = '';
        } else {
            rename_alert.classList.remove("hide");
            let formData = new FormData();
            formData.append("project_name", this_selection_info.project_name);
            formData.append("product_name", product_rename);
            jQuery.ajax({
                url: "check_duplicate_sku.php",
                type: "POST",
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    let row_count = parseInt(data[0].row_count, 10);
                    if (row_count !== 0) {
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
        }
    }
}

function is_url(str) {
    let regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
    return regexp.test(str);
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
    let product_comment = document.getElementById('product_comment').value.trim();
    if (this_selection_info.selectedProductType === "brand" || this_selection_info.selectedProductType === "sku") {
        if (product_rename === "") {
            document.getElementById("product_rename_error").innerHTML =
                "Cannot be empty";
            is_valid_form = false;
        } else {
            document.getElementById("product_rename_error").innerHTML = "";
        }
    } else if (this_selection_info.selectedProductType === "dvc") {
        if (product_rename === "") {
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
    } else if (this_selection_info.selectedProductType != "facing") {
        is_valid_form = false;
    }
    if (!approve_button.checked && !disapprove_button.checked) {
        document.getElementById("status_error").innerHTML =
            "Status must be selected";
        is_valid_form = false;
    } else {
        document.getElementById("status_error").innerHTML = "";
    }

    if (this_selection_info.selectedProductType == "brand" && manu_link != org_manu_link && error_qa.length == 0) {
        document.getElementById('error_link_error').innerHTML = 'Manufactuer URL changed. Error Type must be selected';
        is_valid_form = false;
    } else {
        document.getElementById('error_link_error').innerHTML = '';
    }

    if (this_selection_info.selectedProductType == "brand" && manu_link == "") {
        document.getElementById('manu_error').innerHTML = 'URL cannot be empty';
        is_valid_form = false;
    } else {
        document.getElementById('manu_error').innerHTML = '';
        if (!is_url(manu_link) && this_selection_info.selectedProductType == "brand") {
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

    if (is_valid_form && !is_dupilcate && !is_dupilcate_dvc) {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        formData.append("product_type", this_selection_info.selectedProductType);
        formData.append("product_rename", product_rename);
        formData.append("product_alt_rename", product_alt_rename);
        formData.append("error_qa", error_qa);
        formData.append("product_comment", product_comment);
        formData.append("num_facings", document.getElementById("num_facings").value);
        console.log(document.getElementById("num_facings").value);
        formData.append("manu_link", manu_link);
        if (disapprove_button.checked) {
            formData.append('status', 'rejected');
        } else {
            formData.append('status', 'active');
        }
        jQuery.ajax({
            url: "process_oda_qa.php",
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

function compare_alt_rename() {
    if (this_selection_info.selectedProductType === "dvc") {
        let alt_name = document.getElementById("alt_name").value.trim();
        let product_alt_rename = document
            .getElementById("product_alt_rename")
            .value.trim();
        let dvc_rename_alert = document.getElementById("dvc_rename_alert");
        if (alt_name !== product_alt_rename) {
            dvc_rename_alert.classList.remove("hide");
            let formData = new FormData();
            formData.append("project_name", this_selection_info.project_name);
            formData.append("product_name", product_alt_rename);
            jQuery.ajax({
                url: "check_duplicate_dvc.php",
                type: "POST",
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    let row_count = parseInt(data[0].row_count, 10);
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


const display_qa_probe = () => {
    if ($('#product_name').val() !== "") {
        $("#qa_probe").modal("show");
        if (this_selection_info.selectedProductType === 'facing') {
            document.getElementById('rename_section').classList.add("hide");
            document.getElementById("alt_rename_section").classList.add("hide");
        } else {
            document.getElementById('rename_section').classList.remove("hide");
            document.getElementById("alt_rename_section").classList.remove("hide");
        }
        if (document.getElementById("alt_name").value === "") {
            document.getElementById("alt_name_section").classList.add("hide");
            document.getElementById("alt_rename_section").classList.add("hide");
        } else {
            document.getElementById("alt_name_section").classList.remove("hide");
            if (this_selection_info.selectedProductType !== 'facing') {
                document.getElementById("alt_rename_section").classList.remove("hide");
            }
        }
        if (this_selection_info.selectedProductType === "brand") {
            document.getElementById("manu_link_section").classList.remove("hide");
        } else {
            document.getElementById("manu_link_section").classList.add("hide");
        }
    }
};

const assign_brand = () => {
    this_selection_info.selectedProductType = 'brand';
    get_probe_qa_info('brand');
};
const assign_sku = () => {
    this_selection_info.selectedProductType = 'sku';
    get_probe_qa_info('sku');
};
const assign_dvc = () => {
    this_selection_info.selectedProductType = 'dvc';
    get_probe_qa_info('dvc');
};
const assign_facing = () => {
    this_selection_info.selectedProductType = 'facing';
    get_probe_qa_info('facing');
};

// function which adds the hide class the passed element
const hideElement = (elementName) => {
    const element = document.getElementById(elementName);
    element.classList.add('hide');
};

// function which removes the hide class the passed element
const showElement = (elementName) => {
    const element = document.getElementById(elementName);
    element.classList.remove('hide');
};

const get_probe_qa_info = (product_type) => {
    const sku_brand_name = $("#brand_name").val();
    let sku_dvc_name = $("#dvc_product_name").val();
    if (sku_dvc_name == null) {
        sku_dvc_name = $("#dvc_name").val() + ' %';
    }
    const sku_facing_name = $("#facing_name").val();
    let formData = new FormData();
    formData.append('project_name', this_selection_info.project_name);
    formData.append('ticket', this_selection_info.ticket_selection);
    formData.append('client_cat', this_selection_info.client_cat);
    formData.append('reference_qa', this_selection_info.referenceQaSelected);
    formData.append("sku_brand_name", sku_brand_name);
    formData.append("sku_dvc_name", sku_dvc_name);
    formData.append("sku_facing_name", sku_facing_name);
    formData.append("product_type", product_type);
    jQuery.ajax({
        url: "assign_oda_product.php",
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function (data) {
            if (data[0].hunt_type === 'probe') {
                document.getElementById('suggestion_source_button').classList.add('hide');
                let title_string = '<span id="project_title">' + this_selection_info.project_name + ' ' + data[0].probe_info.ticket_id + " " + data[0].hunt_type.toUpperCase() + "</span>";
                if (data[0].probe_info.client_category_name != null) {
                    title_string +=
                        ' <span id="client_category_title">' +
                        data[0].probe_info.client_category_name +
                        "</span>";
                } else {
                    title_string +=
                        ' <span id="client_category_title">' + 'NA' + "</span>";
                }
                if (data[0].probe_info.product_type != null) {
                    title_string +=
                        ' <span id="probe_id_title">' + data[0].probe_info.product_type.toUpperCase() + '</span>';
                }
                let dateTimeParts = data[0].probe_info.product_creation_time.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
                dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
                const dateObject = new Date(...dateTimeParts);
                title_string +=
                    ' <span id="time_title">' + dateObject.toLocaleString() + '</span>';
                jQuery("#qa_probe_title").html(title_string);
                jQuery("#product_name").val(data[0].probe_info.product_name);
                jQuery("#alt_name").val(data[0].probe_info.product_alt_design_name);
                org_manu_link = data[0].probe_info.manufacturer_link;
                jQuery('#manu_link').val(data[0].probe_info.manufacturer_link);
                var product_link = data[0].probe_info.product_link;
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
                if (data[0].probe_info.manufacturer_link == null) {
                    document.getElementById('manu_source_button').classList.add('hide');
                } else {
                    document.getElementById('manu_source_button').classList.remove('hide');
                    var str = "<i class=\"fas fa-external-link-alt\">";
                    var result = str.link(data[0].probe_info.manufacturer_link);
                    document.getElementById('manu_source_button').innerHTML = result;
                    $('#manu_source_button a').attr('target', '_blank');
                }
                document.getElementById("num_facings").value = data[0].probe_info.product_facing_count;
                facing_num = data[0].probe_info.product_facing_count;
                document.getElementById("output").innerHTML = document.getElementById("num_facings").value;
                if (data[0].probe_info.product_alt_design_previous != null) {
                    jQuery('#name_error').html('Orignal name was overwritten by an Analyst');
                }
                display_qa_probe();
            }

            if (data[0].hunt_type === 'radar') {
                var title_string = '<span id="project_title">' + this_selection_info.project_name + ' ' + data[0].radar_info.ticket_id + " " + data[0].hunt_type.toUpperCase() + "</span>";
                if (data[0].radar_info.radar_category != null) {
                    title_string +=
                        ' <span id="client_category_title">' +
                        data[0].radar_info.radar_category +
                        "</span>";
                } else {
                    title_string +=
                        ' <span id="client_category_title">' +
                        'NA' +
                        "</span>";
                }
                if (data[0].radar_info.product_type != null) {
                    title_string +=
                        ' <span id="probe_id_title">' + data[0].radar_info.product_type.toUpperCase() + '</span>';
                }

                let dateTimeParts = data[0].radar_info.product_creation_time.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
                dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
                const dateObject = new Date(...dateTimeParts);
                title_string +=
                    ' <span id="time_title">' + dateObject.toLocaleString() + '</span>';

                jQuery("#qa_probe_title").html(title_string);
                jQuery("#product_name").val(data[0].radar_info.product_name);
                jQuery("#alt_name").val(data[0].radar_info.product_alt_design_name);
                org_manu_link = data[0].radar_info.manufacturer_link;
                jQuery('#manu_link').val(data[0].radar_info.manufacturer_link);
                var product_link = data[0].radar_info.product_link;
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
                let source_link = data[0].radar_info.radar_source_link;
                if (source_link == null) {
                    source_link = '';
                    document.getElementById('suggestion_source_button').classList.add('hide');
                } else {
                    document.getElementById('suggestion_source_button').classList.remove('hide');
                    let str = "Go to Suggestion Source <i class=\"fas fa-external-link-alt\">";
                    let result = str.link(source_link);
                    document.getElementById('suggestion_source_button').innerHTML = result;
                    $('#suggestion_source_button a').attr('target', '_blank');
                }
                if (data[0].radar_info.manufacturer_link == null) {
                    document.getElementById('manu_source_button').classList.add('hide');
                } else {
                    document.getElementById('manu_source_button').classList.remove('hide');
                    var str = "<i class=\"fas fa-external-link-alt\">";
                    var result = str.link(data[0].radar_info.manufacturer_link);
                    document.getElementById('manu_source_button').innerHTML = result;
                    $('#manu_source_button a').attr('target', '_blank');
                }
                document.getElementById("num_facings").value = data[0].radar_info.product_facing_count;
                facing_num = data[0].radar_info.product_facing_count;
                document.getElementById("output").innerHTML = document.getElementById("num_facings").value;
                if (data[0].radar_info.product_alt_design_previous != null) {
                    jQuery('#name_error').html('Orignal name was overwritten by an Analyst');
                }
                display_qa_probe();
            }

            if (data[0].hunt_type === 'reference') {
                var title_string = '<span id="project_title">' + this_selection_info.project_name + ' ' + data[0].ref_info.ticket_id + " " + data[0].hunt_type.toUpperCase() + "</span>";
                if (data[0].ref_info.client_category_name != null) {
                    title_string +=
                        ' <span id="client_category_title">' +
                        data[0].ref_info.client_category_name +
                        "</span>";
                } else {
                    title_string +=
                        ' <span id="client_category_title">' +
                        +'NA' +
                        "</span>";
                }
                if (data[0].ref_info.product_type != null) {
                    title_string +=
                        ' <span id="probe_id_title">' + data[0].ref_info.product_type.toUpperCase() + '</span>';
                }

                let dateTimeParts = data[0].ref_info.product_creation_time.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
                dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
                const dateObject = new Date(...dateTimeParts);
                title_string +=
                    ' <span id="time_title">' + dateObject.toLocaleString() + '</span>';

                jQuery("#qa_probe_title").html(title_string);
                jQuery("#product_name").val(data[0].ref_info.product_name);
                jQuery("#alt_name").val(data[0].ref_info.product_alt_design_name);
                org_manu_link = data[0].ref_info.manufacturer_link;
                jQuery('#manu_link').val(data[0].ref_info.manufacturer_link);
                var product_link = data[0].ref_info.product_link;
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
                if (data[0].ref_info.manufacturer_link == null) {
                    document.getElementById('manu_source_button').classList.add('hide');
                } else {
                    document.getElementById('manu_source_button').classList.remove('hide');
                    var str = "<i class=\"fas fa-external-link-alt\">";
                    var result = str.link(data[0].ref_info.manufacturer_link);
                    document.getElementById('manu_source_button').innerHTML = result;
                    $('#manu_source_button a').attr('target', '_blank');
                }
                document.getElementById("num_facings").value = data[0].ref_info.product_facing_count;
                facing_num = data[0].ref_info.product_facing_count;
                document.getElementById("output").innerHTML = document.getElementById("num_facings").value;
                if (data[0].ref_info.product_alt_design_previous != null) {
                    jQuery('#name_error').html('Orignal name was overwritten by an Analyst');
                }
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
                display_qa_probe();
            }
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
};

function get_error_list() {
    if (this_selection_info.project_name !== "") {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
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

const unassign_probe = () => {
    if (this_selection_info.project_name !== "") {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        jQuery.ajax({
            url: "unassign_oda.php",
            type: "POST",
            data: formData,
            success: function (data) {
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
    is_dupilcate = false;
    is_dupilcate_dvc = false;
    facing_num = 0;
};

const get_product_name_list = (product_type, select_element) => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0 && this_selection_info.client_cat !== '' && this_selection_info.client_cat != null) {
        let sku_dvc_name = $("#dvc_name").val();
        if (sku_dvc_name !== '' || sku_dvc_name != null) {
            let formData = new FormData();
            formData.append('project_name', this_selection_info.project_name);
            formData.append('ticket', this_selection_info.ticket_selection);
            formData.append('client_cat', this_selection_info.client_cat);
            formData.append('reference_qa', this_selection_info.referenceQaSelected);
            formData.append('product_type', product_type);
            formData.append('dvc_name', sku_dvc_name);
            jQuery.ajax({
                url: "get_oda_dvc_products.php",
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
};


const get_brand_list = (product_type, select_element) => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0 && this_selection_info.client_cat !== '' && this_selection_info.client_cat != null) {
        let formData = new FormData();
        formData.append('project_name', this_selection_info.project_name);
        formData.append('ticket', this_selection_info.ticket_selection);
        formData.append('client_cat', this_selection_info.client_cat);
        formData.append('reference_qa', this_selection_info.referenceQaSelected);
        formData.append('product_type', product_type);
        jQuery.ajax({
            url: "get_oda_brand_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                let selected_val = $("#" + select_element).val();
                if (data[0].brand_rows !== null) {
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
};

const update_oda_qa_count = () => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0 && this_selection_info.client_cat !== '' && this_selection_info.client_cat != null) {
        get_error_list();
        let dvcProductNameSelected = "false";
        let formData = new FormData();
        const sku_brand_name = $("#brand_name").val();
        let sku_dvc_name = $("#dvc_product_name").val();
        if (sku_dvc_name === null) {
            sku_dvc_name = $("#dvc_name").val();
            dvcProductNameSelected = "true";
        }
        const sku_facing_name = $('#facing_name').val();
        formData.append('project_name', this_selection_info.project_name);
        formData.append('ticket', this_selection_info.ticket_selection);
        formData.append('client_cat', this_selection_info.client_cat);
        formData.append('reference_qa', this_selection_info.referenceQaSelected);
        formData.append("sku_brand_name", sku_brand_name);
        formData.append("sku_dvc_name", sku_dvc_name);
        formData.append("sku_facing_name", sku_facing_name);
        formData.append("dvc_flag", dvcProductNameSelected);
        jQuery.ajax({
            url: "fetch_oda_qa_count.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                let probe_count = parseInt(data[0].processing_probe_row, 10);
                let product_type = data[0].product_type;
                let display_message = "";

                if (product_type === "brand" && probe_count === 1) {
                    document.getElementById("dvc_qa_button").disabled = true;
                    document.getElementById("sku_qa_button").disabled = true;
                    document.getElementById("brand_qa_button").disabled = false;
                    document.getElementById("facing_qa_button").disabled = true;
                    display_message = product_type.toUpperCase() + " already assigned";
                } else if (product_type === "sku" && probe_count === 1) {
                    document.getElementById("dvc_qa_button").disabled = true;
                    document.getElementById("sku_qa_button").disabled = false;
                    document.getElementById("brand_qa_button").disabled = true;
                    document.getElementById("facing_qa_button").disabled = true;
                    display_message = product_type.toUpperCase() + " already assigned";
                } else if (product_type === "dvc" && probe_count === 1) {
                    document.getElementById("dvc_qa_button").disabled = false;
                    document.getElementById("sku_qa_button").disabled = true;
                    document.getElementById("brand_qa_button").disabled = true;
                    document.getElementById("facing_qa_button").disabled = true;
                    display_message = product_type.toUpperCase() + " already assigned";
                } else if (product_type === "facing" && probe_count === 1) {
                    document.getElementById("dvc_qa_button").disabled = true;
                    document.getElementById("sku_qa_button").disabled = true;
                    document.getElementById("brand_qa_button").disabled = true;
                    document.getElementById("facing_qa_button").disabled = false;
                    display_message = product_type.toUpperCase() + " already assigned";
                } else {
                    document.getElementById("sku_qa_button").disabled = false;
                    document.getElementById("dvc_qa_button").disabled = false;
                    document.getElementById("brand_qa_button").disabled = false;
                    document.getElementById("facing_qa_button").disabled = false;
                    display_message = "";
                }

                $("#probe_qa_message").html(display_message);

                $("#current_brand_count").empty();
                $("#current_brand_count").html(data[0].brand_count);
                const brand_count = parseInt(data[0].brand_filtered_count, 10);
                if (brand_count === 0 && product_type !== "brand") {
                    document.getElementById("brand_qa_button").disabled = true;
                }
                $("#current_sku_count").empty();
                $("#current_sku_count").html(data[0].sku_count);
                const sku_count = parseInt(data[0].sku_filtered_count, 10);
                if (sku_count === 0 && product_type !== "sku") {
                    document.getElementById("sku_qa_button").disabled = true;
                }
                $("#current_dvc_count").empty();
                $("#current_dvc_count").html(data[0].dvc_count);
                const dvc_count = parseInt(data[0].dvc_filtered_count, 10);
                if (dvc_count === 0 && product_type !== "dvc") {
                    document.getElementById("dvc_qa_button").disabled = true;
                }

                $("#current_facing_count").empty();
                $("#current_facing_count").html(data[0].facing_count);
                const facing_count = parseInt(data[0].facing_filtered_count, 10);
                if (facing_count === 0 && product_type !== "facing") {
                    document.getElementById("facing_qa_button").disabled = true;
                }

                $("#current_brand_count_2").empty();
                $("#current_brand_count_2").html(data[0].brand_filtered_count);

                $("#current_sku_count_2").empty();
                $("#current_sku_count_2").html(data[0].sku_filtered_count);

                $("#current_dvc_count_2").empty();
                $("#current_dvc_count_2").html(data[0].dvc_filtered_count);


                $("#current_facing_count_2").empty();
                $("#current_facing_count_2").html(data[0].facing_filtered_count);
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
};

const get_ticket_list = () => {
    if (this_selection_info.project_name !== "") {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        jQuery.ajax({
            url: "get_project_tickets.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
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
};

const fetch_client_cat = () => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0) {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        jQuery.ajax({
            url: "fetch_client_cat.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                $("#client_category").append(
                    '<option value="" selected disabled>Select</option>'
                );
                // adding missing options
                for (let i = 0; i < data[0].client_cat_info.length; i++) {
                    if (
                        !$("#client_category").find(
                            'option[value="' + data[0].client_cat_info[i].client_category_id + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#client_category").append(
                            '<option value="' +
                            data[0].client_cat_info[i].client_category_id +
                            '">' +
                            data[0].client_cat_info[i].client_category_name +
                            "</option>"
                        );
                    }
                }
                $("#client_category").val("");
                validate_client_cat();
            },
            error: function (data) {
                alert("Error assigning probe. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
};

const validate_toggle = () => {
    $("#brand_name").empty();
    $("#dvc_name").empty();
    $("#facing_name").empty();
    $("#dvc_product_name").empty();
    const toggleState = document.getElementById('qa_type_toggle').checked;
    this_selection_info.referenceQaSelected = !!toggleState;
    if (!toggleState) {
        document.getElementById('def_tab').click();
        document.getElementById('ref_tab').classList.add('hide');
    } else {
        document.getElementById('def_tab').click();
        document.getElementById('ref_tab').classList.remove('hide');
    }
};

const validate_ticket_toggle = () => {
    const ticketToggleState = document.getElementById('ticket_toggle').checked;
    const ticketElement = document.getElementById('ticket');
    if (ticketToggleState) {
        for (let i = 0; i < ticketElement.length; i++) {
            ticketElement.options[i].selected = true;
        }
    } else {
        for (let i = 0; i < ticketElement.length; i++) {
            ticketElement.options[i].selected = false;
        }
    }
    $('#ticket').trigger('change');
};

const validate_client_cat = () => {
    const client_category = $('#client_category').val();
    //console.log(client_category);
    if (client_category !== '' && client_category !== null) {
        showElement('qa_section');
        this_selection_info.client_cat = client_category;
        get_brand_list("dvc", "dvc_name");
        get_brand_list("facing", "facing_name");
        get_brand_list("sku", "brand_name");
        get_product_name_list("dvc", "dvc_product_name");
    } else {
        hideElement('qa_section');
        this_selection_info.client_cat = '';
    }
};

const validate_ticket_name = () => {
    // this makes an array  containing ticket ids
    const ticket_options = $('#ticket').val();
    $('#client_category').empty();
    if (ticket_options.length > 0) {
        this_selection_info.ticket_selection = ticket_options;
        fetch_client_cat();
        showElement('client_cat');
        hideElement('qa_section');
        validate_toggle();
    } else {
        this_selection_info.ticket_selection = [];
        hideElement('client_cat');
        hideElement('qa_section');
        validate_toggle();
    }
};

const validate_project_name = () => {
    const project_name = $('#project_name').val();
    if (project_name !== '') {
        this_selection_info.project_name = project_name;
        $('#ticket').empty();
        get_ticket_list();
        showElement('ticket_section');
        hideElement('client_cat');
        hideElement('qa_section');
    } else {
        this_selection_info.project_name = '';
        hideElement('ticket_section');
        hideElement('client_cat');
        hideElement('qa_section');
    }
};

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


$(document).ready(function () {
    $('#project_name').select2({
        width: '100%',
    });
    $('#ticket').select2({
        width: '50%',
    });
    $('#client_category').select2({
        width: '50%',
    });
    $('#brand_name').select2({
        width: '100%',
    });
    $('#dvc_name').select2({
        width: '100%',
    });
    $('#facing_name').select2({
        width: '100%',
    });
    $('#dvc_product_name').select2({
        width: '100%',
    });
    $("#project_name").change(function () {
        validate_project_name();
    });
    $("#ticket").change(function () {
        validate_ticket_name();
    });
    $("#client_category").change(function () {
        validate_client_cat();
    });
    $("#qa_type_toggle").click(function () {
        validate_toggle();
    });
    $("#ticket_toggle").click(function () {
        validate_ticket_toggle();
    });
    jQuery('#brand_name').on("change", function (e) {
        jQuery('#current_sku_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('sku_qa_button').disabled = true;
    });
    jQuery('#facing_name').on("change", function (e) {
        jQuery('#current_facing_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('facing_qa_button').disabled = true;
    });
    jQuery('#dvc_name').on("change", function (e) {
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('dvc_qa_button').disabled = true;
    });
    jQuery('#dvc_product_name').on("change", function (e) {
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('dvc_qa_button').disabled = true;
    });
    jQuery("#error_qa").select2({
        dropdownParent: $("#qa_probe"),
        width: "100%"
    });
    jQuery('#ticket').on("change", function (e) {
        jQuery('#current_brand_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('dvc_qa_button').disabled = true;
        jQuery('#current_sku_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('sku_qa_button').disabled = true;
        jQuery('#current_dvc_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('dvc_qa_button').disabled = true;
        jQuery('#current_facing_count_2').html("<div class=\"spinner-border text-success\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>");
        document.getElementById('facing_qa_button').disabled = true;
    });
    setInterval(function () {
        update_oda_qa_count();
    }, 1000);
    $("#product_rename").on("change", function () {
        compare_rename();
    });
    $("#product_alt_rename").on("change", function () {
        compare_alt_rename();
    });
    $("#dvc_name").on('select2:close', function () {
        get_brand_list("dvc", "dvc_name");
    });
    $("#facing_name").on('select2:close', function () {
        get_brand_list("facing", "facing_name");
    });
    $("#brand_name").on('select2:close', function () {
        get_brand_list("sku", "brand_name");
    });
    $("#dvc_product_name").on('select2:close', function () {
        get_product_name_list("dvc", "dvc_product_name");
    });
});