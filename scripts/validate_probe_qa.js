var p_name = '';

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
                    $('#current_brand_count').empty();
                    $('#current_brand_count').html(data[0].brand_count);
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