/*
    Filename: process_forms.js
    Author: Malika Liyanage
    Created: 25/07/2019
*/

function submit_client_category_form() {
    var client_category_name = document.getElementById('client_category_name').value;
    var client_category_local_name = document.getElementById('client_category_local_name').value;
    if (client_category_name == '') {
        document.getElementById('client_category_name_error').innerHTML = 'Name is required';
    } else {
        document.getElementById('client_category_name_error').innerHTML = '';
        jQuery.post("process_client_category.php", {
            client_category_name,
            client_category_local_name
        },
            function (data) {
                $('#results').html(data);
                $('#new_client_category')[0].reset();
            });
    }
}

function reset_client_category_form() {
    jQuery('#new_client_category')[0].reset();
    document.getElementById('client_category_name_error').innerHTML = '';
    jQuery('#results').empty();
}

function submit_client_sub_category_form() {
    var client_sub_category_name = document.getElementById('client_sub_category_name').value;
    var client_sub_category_local_name = document.getElementById('client_sub_category_local_name').value;
    if (client_sub_category_name == '') {
        document.getElementById('client_sub_category_name_error').innerHTML = 'Name is required';
    } else {
        document.getElementById('client_sub_category_name_error').innerHTML = '';
        jQuery.post("process_client_sub_category.php", {
            client_sub_category_name,
            client_sub_category_local_name
        },
            function (data) {
                $('#sub_results').html(data);
                $('#new_client_sub_category')[0].reset();
            });
    }
}

function reset_client_sub_category_form() {
    jQuery('#new_client_sub_category')[0].reset();
    document.getElementById('client_sub_category_name_error').innerHTML = '';
    jQuery('#sub_results').empty();
}

function reset_manufacturer_form() {
    jQuery('#new_manufacturer')[0].reset();
    document.getElementById('manufacturer_name_error').innerHTML = '';
    document.getElementById('manufacturer_source_error').innerHTML = '';
    document.getElementById('manufacturer_image_error').innerHTML = '';
    document.getElementById('manufacturer_image_size_error').innerHTML = '';
    jQuery('#manu_results').empty();
}

function submit_manufacturer_form() {
    var manufacturer_name = document.getElementById('manufacturer_name').value;
    var manufacturer_local_name = document.getElementById('manufacturer_local_name').value;
    var manufacturer_source = document.getElementById('manufacturer_source').value;
    var manu_logo = document.getElementById('file-input-manu-logo').files[0];
    var form_ok = true;

    // checks if manufacturer name is not empty
    if (manufacturer_name == '') {
        document.getElementById('manufacturer_name_error').innerHTML = 'Name is required';
        form_ok = false;
    } else {
        document.getElementById('manufacturer_name_error').innerHTML = '';
    }

    // checks if manufacturer source is not empty
    if (manufacturer_source == '') {
        document.getElementById('manufacturer_source_error').innerHTML = 'Source is required';
        form_ok = false;
    } else {
        document.getElementById('manufacturer_source_error').innerHTML = '';
    }

    // checks if a manufacturer image was uploaded
    if (document.getElementById('file-input-manu-logo').value == '') {
        document.getElementById('manufacturer_image_error').innerHTML = 'Required';
        form_ok = false;
    } else {
        document.getElementById('manufacturer_image_error').innerHTML = '';
        // gets the image size in mega bytes
        var image_size_mb = document.getElementById('file-input-manu-logo').files[0].size / 1024 / 1024;
        // checks if the image is large than 3 MB
        if (image_size_mb > 3) {
            document.getElementById('manufacturer_image_size_error').innerHTML = 'Image Size cannot exceed 3 MB';
            form_ok = false;
        } else {
            document.getElementById('manufacturer_image_size_error').innerHTML = '';
        }
    }

    if (form_ok) {
        var formData = new FormData();
        formData.append('manufacturer_name', manufacturer_name);
        formData.append('manufacturer_local_name', manufacturer_local_name);
        formData.append('manufacturer_source', manufacturer_source);
        formData.append('manu_logo', manu_logo);

        jQuery.ajax({
            url: 'process_manufacturer.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                jQuery('#new_manufacturer')[0].reset();
                document.getElementById('clear-manu-logo').click();
                jQuery('#manu_results').html(data);
            },
            error: function (data) {
                alert("AJAX error");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

// gets updated manufacturer list from the database when the user clicks on the drop down list
// usign AJAX
function get_manufacturer_list() {
    jQuery.ajax({
        type: "POST",
        url: "manufacturer_list.php",
        success: function (data) {
            jQuery(".manu-list").html(data);
        }
    });
}

function reset_brand_form() {
    jQuery('#new_brand')[0].reset();
    document.getElementById('brand_name_error').innerHTML = '';
    document.getElementById('brand_manufacturer_error').innerHTML = '';
    document.getElementById('brand_source_error').innerHTML = '';
    document.getElementById('brand_image_size_error').innerHTML = '';
    document.getElementById('brand_image_error').innerHTML = '';
    jQuery('#brand_results').empty();
}

function submit_brand_form() {
    var form_ok = true;
    var brand_name = document.getElementById('brand_name').value;
    var brand_manufacturer_dropdown = document.getElementById('brand_manufacturer');
    var brand_manufacturer = brand_manufacturer_dropdown.options[brand_manufacturer_dropdown.selectedIndex].value;
    var brand_source = document.getElementById('brand_source').value;
    var recognition_option = document.getElementsByName('recognition_option');
    var recognition_value;
    var brand_logo = document.getElementById('file-input-brand-logo').files[0];
    var brand_local_name = document.getElementById('brand_local_name').value;
    var brand_global_code = document.getElementById('brand_global_code').value;

    // checks if brand name is not empty
    if (brand_name == '') {
        document.getElementById('brand_name_error').innerHTML = 'Name is required';
        form_ok = false;
    } else {
        document.getElementById('brand_name_error').innerHTML = '';
    }

    // checks if a brand manufacturer has been selected from the drop down list
    if (brand_manufacturer == '') {
        document.getElementById('brand_manufacturer_error').innerHTML = 'Manufacturer must be selected';
        form_ok = false;
    } else {
        document.getElementById('brand_manufacturer_error').innerHTML = '';
    }

    if (brand_source == '') {
        document.getElementById('brand_source_error').innerHTML = 'Source is required';
        form_ok = false;
    } else {
        document.getElementById('brand_source_error').innerHTML = '';
    }

    // checks if a brand image was uploaded
    if (document.getElementById('file-input-brand-logo').value == '') {
        document.getElementById('brand_image_error').innerHTML = 'Required';
        form_ok = false;
    } else {
        document.getElementById('brand_image_error').innerHTML = '';
        // gets the image size in mega bytes
        var image_size_mb = document.getElementById('file-input-brand-logo').files[0].size / 1024 / 1024;
        // checks if the image is large than 3 MB
        if (image_size_mb > 3) {
            document.getElementById('brand_image_size_error').innerHTML = 'Image Size cannot exceed 3 MB';
            form_ok = false;
        } else {
            document.getElementById('brand_image_size_error').innerHTML = '';
        }
    }

    if (form_ok) {
        for (var i = 0; i < recognition_option.length; i++) {
            if (recognition_option[i].checked) {
                recognition_value = recognition_option[i].value;
                break;
            }
        }
        var formData = new FormData();
        formData.append('brand_name', brand_name);
        formData.append('brand_local_name', brand_local_name);
        formData.append('brand_manufacturer', brand_manufacturer);
        formData.append('brand_source', brand_source);
        formData.append('brand_global_code', brand_global_code);
        formData.append('recognition_value', recognition_value);
        formData.append('brand_logo', brand_logo);

        jQuery.ajax({
            url: 'process_brand.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                $('#brand_results').html(data);
                $('#new_brand')[0].reset();
                document.getElementById('clear-brand-logo').click();
            },
            error: function (data) {
                alert("AJAX error");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

jQuery(document).ready(function () {
    jQuery('#brand_manufacturer').select2({
        dropdownParent: $("#suggest_brand"),
        width: '100%'
    })
});