// object used for storing user selection options
let this_selection_info = {
    project_name: '',
    ticket_selection: [],
    client_cat: '',
    referenceQaSelected: false,
    selectedProductType: '',
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
            console.log(data);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

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
}


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
}

const update_oda_qa_count = () => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0 && this_selection_info.client_cat !== '' && this_selection_info.client_cat != null) {
        get_brand_list("dvc", "dvc_name");
        get_brand_list("facing", "facing_name");
        get_brand_list("sku", "brand_name");
        get_product_name_list("dvc", "dvc_product_name");
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
                const brand_count = parseInt(data[0].brand_user_count, 10);
                if (brand_count === 0 && product_type !== "brand") {
                    document.getElementById("brand_qa_button").disabled = true;
                }
                $("#current_sku_count").empty();
                $("#current_sku_count").html(data[0].sku_count);
                const sku_count = parseInt(data[0].brand_sku_count, 10);
                if (sku_count === 0 && product_type !== "sku") {
                    document.getElementById("sku_qa_button").disabled = true;
                }
                $("#current_dvc_count").empty();
                $("#current_dvc_count").html(data[0].dvc_count);
                const dvc_count = parseInt(data[0].brand_dvc_count, 10);
                if (dvc_count === 0 && product_type !== "dvc") {
                    document.getElementById("dvc_qa_button").disabled = true;
                }

                $("#current_facing_count").empty();
                $("#current_facing_count").html(data[0].facing_count);
                const facing_count = parseInt(data[0].facing_sku_count, 10);
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
    const toggleState = document.getElementById('qa_type_toggle').checked;
    if (toggleState) {
        this_selection_info.referenceQaSelected = true;
    } else {
        this_selection_info.referenceQaSelected = false;
    }
};

const validate_client_cat = () => {
    const client_category = $('#client_category').val();
    //console.log(client_category);
    if (client_category !== '' && client_category !== null) {
        showElement('qa_section');
        this_selection_info.client_cat = client_category;
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
    } else {
        this_selection_info.ticket_selection = [];
        hideElement('client_cat');
        hideElement('qa_section');
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
    setInterval(function () {
        update_oda_qa_count();
    }, 1000);
});