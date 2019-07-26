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
                $('#manu_results').html(data);
                $('#new_manufacturer')[0].reset();
                document.getElementById('clear-manu-logo').click();
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