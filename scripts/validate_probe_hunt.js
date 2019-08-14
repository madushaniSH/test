var count = 0;
var request;
function validate_project_name() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var project_name_error = document.getElementById('project_name_error');
    var probe_hunt_options = document.getElementById('probe_hunt_options');
    var probe_hunt_counter = document.getElementById('probe_hunt_counter');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_options.classList.add('hide');
        probe_hunt_counter.classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        probe_hunt_options.classList.remove('hide');
        probe_hunt_counter.classList.remove('hide');
        is_project_name_set = true;
        request = setInterval(function(){update_project_count(project_name);}, 2000);
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

function update_project_count (project_name) {
    var formData = new FormData();
    formData.append('project_name', project_name);
    jQuery.ajax({
        url: 'fetch_probe_count.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            var output_count;
            if (data[0].number_of_rows != null) {
                $('#current_probe_count').empty();
                $('#current_probe_count').html(data[0].number_of_rows);
                var count = parseInt(data[0].number_of_rows, 10);
                if (count == 0) {
                    document.getElementById('continue_btn').add('hide');
                }
            } else {
                $('#current_probe_count').html('XX');
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

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#project_name').change( function(){
        validate_project_name();
        if (count > 0) {
            clearInterval(request);
            count = 0;
        }
        count++;
    });  
});