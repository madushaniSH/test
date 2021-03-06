function get_extension(file_path) {
    var parts = file_path.split('.');
    return parts[parts.length - 1];
}

function is_image(file_path) {
    var ext = get_extension(file_path);
    switch (ext.toLowerCase()) {
        case 'jpg':
            return true;
    }
    return false;
}

function check_image_upload(file_name, error_message_name) {
    var file = document.getElementById(file_name).files[0];
    var file_path = document.getElementById(file_name).value;
    var error_message_element = document.getElementById(error_message_name);
    var is_image_ok = true;
    error_message_element.innerHTML = '';
    if (is_image(file_path)) {
        // new image object
        var image = new Image();
        image.src = image.src = window.URL.createObjectURL(file);
        image.onload = function () {
            if (image.height < 300 || image.width < 300) {
                error_message_element.innerHTML = '<br>Must be at least be 300 x 300';
                is_image_ok = false;
            }
            if ((file.size / 1024 / 1024) > 3) {
                error_message_element.innerHTML = '<br>Must be smaller than 3 MB';
            }
        }
    } else {
        error_message_element.innerHTML = '<br>File must be of type jpg';
        is_image_ok = false;
    }
    return is_image_ok;
}

function check_file_uploaded(file_name) {
    if (document.getElementById(file_name).value != '') {
        return true;
    }
    return false;
}


// function is used to validate the sku form
function validate_form() {
    var formData = new FormData();
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
    var container_type_element = document.getElementById('container_type');
    var container_type = container_type_element.options[container_type_element.selectedIndex].value;
    var container_type_error = document.getElementById('container_type_error');
    var smart_caption = document.getElementById('smart_caption').value;
    var item_code = document.getElementById('item_code').value;
    var global_code = document.getElementById('global_code').value;
    var sub_packages = document.getElementById('sub_packages').value;
    var size = document.getElementById('size').value;
    var unit = document.getElementById('unit').value;
    var height = document.getElementById('height').value;
    var width = document.getElementById('width').value;
    var depth = document.getElementById('depth').value;
    var measurement_unit_element = document.getElementById('measurement_unit');
    var measurement_unit = measurement_unit_element.options[measurement_unit_element.selectedIndex].value;
    var trax_category_element = document.getElementById('trax_category');
    var trax_category = trax_category_element.options[trax_category_element.selectedIndex].value;
    var short_name = document.getElementById('short_name').value;
    var local_name = document.getElementById('local_name').value;
    var ean = document.getElementById('ean').value;
    var client_sub_catergory_element = document.getElementById('client_sub_category');
    var client_sub_catergory = client_sub_catergory_element.options[client_sub_catergory_element.selectedIndex].value;


    if (check_file_uploaded('file-input-front')) {
        document.getElementById('front_image_error').innerHTML = '';
        if (check_image_upload('file-input-front', 'front_image_error')) {
            document.getElementById('front_image_error').innerHTML = '';
            formData.append('file-input-front', document.getElementById('file-input-front').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('front_image_error').innerHTML = '<br>Required';
        is_valid_form = false;
    }

    if (check_file_uploaded('file-input-top')) {
        if (check_image_upload('file-input-top', 'top_image_error')) {
            document.getElementById('top_image_error').innerHTML = '';
            formData.append('file-input-top', document.getElementById('file-input-top').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('top_image_error').innerHTML = '';
    }

    if (check_file_uploaded('file-input-back')) {
        if (check_image_upload('file-input-back', 'back_image_error')) {
            document.getElementById('back_image_error').innerHTML = '';
            formData.append('file-input-back', document.getElementById('file-input-back').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('back_image_error').innerHTML = '';
    }

    if (check_file_uploaded('file-input-bottom')) {
        if (check_image_upload('file-input-bottom', 'bottom_image_error')) {
            document.getElementById('bottom_image_error').innerHTML = '';
            formData.append('file-input-bottom', document.getElementById('file-input-bottom').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('bottom_image_error').innerHTML = '';
    }

    if (check_file_uploaded('file-input-side1')) {
        if (check_image_upload('file-input-side1', 'side1_image_error')) {
            document.getElementById('side1_image_error').innerHTML = '';
            formData.append('file-input-side1', document.getElementById('file-input-side1').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('side1_image_error').innerHTML = '';
    }

    if (check_file_uploaded('file-input-side2')) {
        if (check_image_upload('file-input-side2', 'side2_image_error')) {
            document.getElementById('side2_image_error').innerHTML = '';
            formData.append('file-input-side2', document.getElementById('file-input-side2').files[0]);
        } else {
            is_valid_form = false;
        }
    } else {
        document.getElementById('side2_image_error').innerHTML = '';
    }

    if (smart_caption == '') {
        document.getElementById('smart_caption_error').innerHTML = 'Required';
        is_valid_form = false;
    } else {
        document.getElementById('smart_caption_error').innerHTML = '';
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
            formData.append('container_type_id', container_type);
        }
    }

    if (trax_category == '') {
        document.getElementById('trax_category_error').innerHTML = 'Trax Category is required';
        is_valid_form = false;
    } else {
        document.getElementById('trax_category_error').innerHTML = '';
    }

    // if valid form submits it to the php file for processing
    if (is_valid_form) {
        formData.append('name',name);
        formData.append('client_category_id', client_category);
        formData.append('brand_id', brand);
        formData.append('trax_category', trax_category);
        if (item_code != '') {
            formData.append('item_code',item_code);
        }
        if (global_code != '') {
            formData.append('global_code', global_code);
        }
        if (size != '') {
            formData.append('size', size);
        }
        if (sub_packages != '') {
            formData.append('sub_packages', sub_packages);
        }
        if (measurement_unit != '') {
            formData.append('measurement_unit',measurement_unit);
        }
        if (unit != '') {
            formData.append('unit', unit);
        }
        if (height != '') {
            formData.append('height', height);
        }
        if (width != '') {
            formData.append('width', width);
        }
        if (depth != '') {
            formData.append('depth', depth);
        }
        if (short_name != '') {
            formData.append('short_name', short_name);
        }
        if (local_name != '') {
            formData.append('local_name', local_name);
        }
        if (ean != '') {
            formData.append('ean', ean);
        }
        if (client_sub_catergory != '') {
            formData.append('client_sub_catergory', client_sub_catergory);
        }

        jQuery.ajax({
            url: 'process_sku_form.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                $('#dupliacte_error').html(data);
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

function add_attribute_new() {
    var new_attribute = document.getElementById('new_attribute').value;
    if (new_attribute != '') {
        var formData = new FormData();
        formData.append('new_attribute', new_attribute);
        jQuery.ajax({
            url: 'process_attribute.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                $('#attribute_error').html(data);
                $('#edit_attribute')[0].reset();
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

function allow_create_attribute() {
    var new_attribute = document.getElementById('new_attribute').value;

    if (new_attribute != '') {
        document.getElementById('add_attribute').disabled = false;
    } else {
        document.getElementById('add_attribute').disabled = true;
    }
}

function apply_attribute_list() {
    var attribute_elements = document.getElementsByClassName('form-check-attribute');
    //document.getElementById('new_attribute_entry').innerHTML = '';
    for (var i = 0; i < attribute_elements.length; i++) {
        if (attribute_elements[i].checked) {
            const div = document.createElement('div');
            div.className = 'col col-md-6';
            const form_div = document.createElement('div');
            form_div.className = 'form-group';
            form_div.innerHTML = `
            <label for="`+ attribute_elements[i].value + `">` + attribute_elements[i].name + `</label>
            <input type="text" id="`+ attribute_elements[i].value + `" name="` + attribute_elements[i].value + `" class="form-control">
            `;
            div.appendChild(form_div);
            document.getElementById('new_attribute_entry').appendChild(div);
            document.getElementById('clear_attribute').style.display = 'inline-block';
        }
    }

    reset_attribute_form();
    document.getElementById('close_edit_attribute').click();
}

function clear_attribute_list() {
    document.getElementById('new_attribute_entry').innerHTML = '';
    document.getElementById('clear_attribute').style.display = 'none';
}

function check_smart_level() {
    if (document.getElementById('smart_level_one').value == '') {
        document.getElementById('smart_level_one_error').innerHTML = 'Please populate Client category';
    } else {
        document.getElementById('smart_level_one_error').innerHTML = '';
    }
    if (document.getElementById('smart_level_two').value == '') {
        document.getElementById('smart_level_two_error').innerHTML = 'Please populate Brand';
    } else {
        document.getElementById('smart_level_two_error').innerHTML = '';
    }
}

jQuery(document).ready(function () {
    // when a change is made to drop down function is called
    jQuery('#product_type').change(function () {
        allow_submit_sku_form();
    });
    jQuery('#sku_form').on('submit', function (e) {
        e.preventDefault();
    });
    jQuery('#new_attribute_form').on('submit', function (e) {
        e.preventDefault();
    });
    jQuery('#new_attribute').on('keyup', function () {
        allow_create_attribute();
    });
    check_smart_level();
    jQuery('#trax_category').change(function () {
        document.getElementById('smart_level_one').value = document.getElementById('trax_category').options[document.getElementById('trax_category').selectedIndex].text;
        check_smart_level();
    });
    jQuery('#brand').change(function () {
        document.getElementById('smart_level_two').value = document.getElementById('brand').options[document.getElementById('brand').selectedIndex].text;
        check_smart_level();
    });
});
