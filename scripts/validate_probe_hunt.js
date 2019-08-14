function validate_project_name () {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var probe_hunt_options = document.getElementById('probe_hunt_options');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_options.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        probe_hunt_options.classList.remove('hide');
    }
}

function get_probe_info() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    jQuery.ajax({
        url: 'assign_probe.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var title_string = project_name;
            if (data[0].brand_name != null) {
                title_string += ' ' + data[0].brand_name;
            }            
            if (data[0].client_category_name != null) {
                title_string += ' ' + data[0].client_category_name;
            }
            if (data[0].probe_id != null) {
                title_string += ' ' + data[0].probe_id;
            }
            jQuery('#add_probe_title').html(title_string);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}


jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',     
    });
    jQuery('#project_name').change(validate_project_name);
});