function validate_form(){
    var is_valid_form = true;
    var name = document.getElementById('name').value;
    var name_error = document.getElementById('name_error');
    var brand_element = document.getElementById('brand');
    var brand = brand_element.options[brand_element.selectedIndex].value;
    var brand_error = document.getElementById('brand_error');
    var client_category_element = document.getElementById('client_category');
    var client_category = client_category_element.options[client_category_element.selectedIndex].value;
    var client_category_error = document.getElementById('client_category_error');
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element.options[product_type_element.selectedIndex].value;
    var front_image = document.getElementById('file-input-front').files[0];
    var container_type_element = document.getElementById('container_type');
    var container_type = container_type_element.options[container_type_element.selectedIndex].value;
    var container_type_error = document.getElementById('container_type_error');

    if (document.getElementById('file-input-front').value == '') {
        document.getElementById('front_image_error').innerHTML = 'Required';
        is_valid_form = false;
    } else {
        document.getElementById('front_image_error').innerHTML = '';
        document.getElementById('front_image_error').innerHTML = '';
        // gets the image size in mega bytes
        var image_size_mb = document.getElementById('file-input-front').files[0].size / 1024 / 1024;
        // checks if the image is large than 3 MB
        if (image_size_mb > 3) {
            document.getElementById('front_image_error').innerHTML = 'Image Size cannot exceed 3 MB';
            form_ok = false;
        } else {
            document.getElementById('front_image_error').innerHTML = '';
        }
    }

    if (name == '') { 
        name_error.innerHTML = 'Name is required';
        is_valid_form = false;
    } else {
        name_error.innerHTML = '';
    }

    if (brand == '') {
        brand_error.innerHTML = 'Brand is required';
        is_valid_form = false;
    } else {
        brand_error.innerHTML = '';
    }

    if (client_category == '') {
        client_category_error.innerHTML = 'Client Category is required';
        is_valid_form = false;
    } else {
        client_category_error.innerHTML = '';
    }

    if (product_type == 'sku') {
        if (container_type == '') {
            container_type_error.innerHTML = 'Container Type is required';
            is_valid_form = false;
        } else {
            container_type_error.innerHTML = '';
        }
    }

    if (is_valid_form) {
        var formData = new FormData();
        formData.append('name', name);
        formData.append('client_category_id', client_category);
        formData.append('brand_id', brand);
        formData.append('container_type_id', container_type);
        formData.append('front_image', front_image)

        jQuery.ajax({
            url: 'process_sku_form.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                $('#dupliacte_error').html(data);
                $('#sku_form')[0].reset();
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

function allow_submit_sku_form() {
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element[product_type_element.selectedIndex].value;

    if (product_type != '') {
        document.getElementById('submit_sku_form').disabled = false;
    } else {
        document.getElementById('submit_sku_form').disabled = true;
    }
}

function allow_create_attribute() {
    var new_attribute = document.getElementById('new_attribute').value;
    
    if (new_attribute != '') {
        document.getElementById('add_attribute').disabled = false;
    } else {
        document.getElementById('add_attribute').disabled = true;
    }
}


jQuery(document).ready(function () {
    // when a change is made to drop down function is called
    jQuery('#product_type').change(function () {
        allow_submit_sku_form();
    });

    jQuery('#sku_form').on('submit', function(e) {
        e.preventDefault();
    });
    jQuery('#new_attribute').on('keyup', function(){
        allow_create_attribute();
    });
});