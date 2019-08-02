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
    get_attribute_list();
});