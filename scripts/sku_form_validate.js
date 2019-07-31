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
    jQuery('#product_type').change(function (){
        allow_submit_sku_form();
    });
});