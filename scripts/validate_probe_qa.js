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
});