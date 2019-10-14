/*
    Filename: process_forms.js
    Author: Malika Liyanage
    Created: 31/07/2019
*/

function get_brand_list() {
    jQuery.ajax({
        type: "POST",
        url: "brand_list.php",
        success: function (data) {
            jQuery(".brand-list").html(data);
        }
    });
}

function get_client_category_list() {
    jQuery.ajax({
        type: "POST",
        url: "client_category_list.php",
        success: function (data) {
            jQuery(".client-category-list").html(data);
        }
    });
}

function get_client_sub_category_list() {
    jQuery.ajax({
        type: "POST",
        url: "client_sub_category_list.php",
        success: function (data) {
            jQuery(".client-sub-category-list").html(data);
        }
    });
}

function get_trax_category_list() {
    jQuery.ajax({
        type: "POST",
        url: "trax_category_list.php",
        success: function (data) {
            jQuery(".trax-category-list").html(data);
        }
    });
}

function  get_container_type_list() {
    jQuery.ajax({
        type: "POST",
        url: "container_type_list.php",
        success: function (data) {
            jQuery(".container-type-list").html(data);
        }
    });
}

function get_measurement_unit() {
    jQuery.ajax({
        type: "POST",
        url: "measurement_unit_list.php",
        success: function (data) {
            jQuery(".measurement-unit-list").html(data);
        }
    });
}

function get_attribute_list() {
    jQuery.ajax({
        type: "POST",
        url: "attribute_list.php",
        success: function (data) {
            jQuery("#edit_attribute .attribute-list").html(data);
        },
        error: function (data) {
            alert("AJAX error");
        },
    });
}

jQuery(document).ready(function () {
    jQuery('#brand').select2({
        width: '100%'
    });
    get_brand_list();
    jQuery('#client_category').select2({
        width: '100%'
    });
    get_client_category_list();
    jQuery('#client_sub_category').select2({
        width: '100%'
    });
    get_client_sub_category_list();
    jQuery('#trax_category').select2({
        width: '100%'
    })
    get_trax_category_list();
    jQuery('#product_type').select2({
        width: '100%'
    })
    jQuery('#container_type').select2({
        width: '100%'
    })
    get_container_type_list();
    jQuery('#measurement_unit').select2({
        width: '100%'
    })
    get_measurement_unit();
    get_attribute_list();
});