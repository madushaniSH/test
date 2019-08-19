var p_name = '';
var product_type = '';

function assign_brand(){
    product_type = 'brand';
    get_probe_qa_info();
}

function assign_sku(){
    product_type = 'sku';
    get_probe_qa_info();
}

function assign_dvc(){
    product_type = 'dvc';
    get_probe_qa_info();
}

function get_probe_qa_info(){
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    formData.append('product_type', product_type);
    jQuery.ajax({
        url: 'assign_qa_product.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var title_string = '<span id="project_title">' + project_name + '</span>';
            if (data[0].brand_name != null) {
                title_string += ' <span id="brand_title">' + data[0].brand_name + '</span>';
            }
            if (data[0].client_category_name != null) {
                title_string += ' <span id="client_category_title">' + data[0].client_category_name + '</span>';
            }
            if (data[0].product_type != null) {
                title_string += ' <span id="probe_id_title">' + data[0].product_type.toUpperCase();
            }
            jQuery('#qa_probe_title').html(title_string);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
    $('#qa_probe').modal('show');
}


function update_project_qa_count() {
    if (p_name != '') {
        var formData = new FormData();
        formData.append('project_name', p_name);
        jQuery.ajax({
            url: 'fetch_probe_qa_count.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                var output_count;
                if (data[0].brand_count != null) {
                    var total_count = 0;
                    var probe_count = parseInt(data[0].processing_probe_row, 10);
                    var product_type = data[0].product_type;
                    $('#current_brand_count').empty();
                    $('#current_brand_count').html(data[0].brand_count);
                    var brand_count = parseInt(data[0].brand_count, 10);
                    if (brand_count == 0 && probe_count == 0 && product_type == 'brand') {
                        
                    }
                    $('#current_sku_count').empty();
                    $('#current_sku_count').html(data[0].sku_count);
                    $('#current_dvc_count').empty();
                    $('#current_dvc_count').html(data[0].dvc_count);

                } else {
                    $('#current_brand_count').html('XX');
                    $('#current_sku_count').html('XX');
                    $('#current_dvc_count').html('XX');
                }
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
}

function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var probe_qa_options = document.getElementById('probe_qa_options');
    var probe_qa_counter = document.getElementById('probe_qa_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_qa_options.classList.add('hide');
        probe_qa_counter.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        probe_qa_options.classList.remove('hide');
        probe_qa_counter.classList.remove('hide');
        p_name = project_name;
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#project_name').change(function () {
        validate_project_name();
    });
    setInterval(function () { update_project_qa_count(); }, 1000);
});