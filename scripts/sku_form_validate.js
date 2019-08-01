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
        var container_type_element = document.getElementById('container_type');
        var container_type = container_type_element.options[container_type_element.selectedIndex].value;
        var container_type_error = document.getElementById('container_type_error');
        if (container_type == '') {
            container_type_error.innerHTML = 'Container Type is required';
            is_valid_form = false;
        } else {
            container_type_error.innerHTML = '';
        }
    }

    return is_valid_form;
}

function allow_submit_sku_form() {
    var product_type_element = document.getElementById('product_type');
    var product_type = product_type_element[product_type_element.selectedIndex].value;

    if (product_type != '') {
        document.getElementById('submit_sku_form').disabled = false;
        console.log('false');
    } else {
        document.getElementById('submit_sku_form').disabled = true;
    }
}

jQuery(document).ready(function () {
    // when a change is made to drop down function is called
    jQuery('#product_type').change(function () {
        allow_submit_sku_form();
    });
    // if the current page in the window is new_sku_form.php
    if (window.location.href.match("new_sku_form.php") != null) {
        var formElement = document.getElementById("sku_form");
        // Validates form on submission wont submit to server if errors are detected
        formElement.onsubmit = validate_form;
    }
});