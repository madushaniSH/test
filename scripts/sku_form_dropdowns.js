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


jQuery(document).ready(function () {
    jQuery('#brand').select2({
        width: '100%'
    });
    get_brand_list();
});