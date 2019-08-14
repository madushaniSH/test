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


jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',     
    });
    jQuery('#project_name').change(validate_project_name);
});