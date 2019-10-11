function validate_project_form() {
    $('.server-results').empty();
    var is_valid_form = true;
    var project_name = document.getElementById('project_name').value;
    var project_name_error = document.getElementById('project_name_error');
    var project_region_element = document.getElementById('project_region');
    var project_region = project_region_element.options[project_region_element.selectedIndex].value;
    var project_region_error = document.getElementById('project_region_error');
    var project_database_name;
    const english_project = document.getElementById('english_project').checked;
    const non_english_project = document.getElementById('non_english_project').checked;
    let project_type = '';

    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name cannot be empty';
        is_valid_form = false;
    } else {
        project_name_error.innerHTML = '';
        project_database_name = project_name.replace(/\s/g,'');
    }

    if (project_region == '') {
        project_region_error.innerHTML = 'Project Region cannot be empty';
        is_valid_form = false;
    } else {
        project_region_error.innerHTML = '';
    }
    if (!english_project && !non_english_project) {
        document.getElementById('project_lang_error').innerHTML = 'Project Language must be selected';
        is_valid_form = false;
    } else {
        document.getElementById('project_lang_error').innerHTML = '';
        if (english_project) {
            project_type = 'english';
        } else if (non_english_project) {
            project_type = 'non_english';
        }
    }

    if (is_valid_form) {
        var formData = new FormData();
        formData.append('project_name', project_name);
        formData.append('project_region', project_region);
        formData.append('project_database', project_database_name);
        formData.append('project_type', project_type);
        jQuery.ajax({
            url: 'process_new_project.php',
            type: 'POST',
            data: formData,
            success: function (data) {
                $('.server-results').empty();
                $('.server-results').html(data);
                $('#project_region').val('').trigger('change');
                $('#create-project-form')[0].reset();
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
