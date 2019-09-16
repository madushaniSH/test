let p_name = '';

const fetch_user_list = () => {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "fetch_active.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                $('#active').empty();
                data[0].summary.forEach(function(i){
                    $('#active').append('<li>'+i.name+'</li>');
                })
            },
            error: function (data) {
                alert("Error assigning probe. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

const validate_project_name = () => {
    const project_name_element = document.getElementById("project_name");
    const project_name =
        project_name_element.options[project_name_element.selectedIndex].value;
    const project_name_error = document.getElementById("project_name_error");
    const result = document.getElementById('result');
    if (project_name == "") {
        project_name_error.innerHTML = "Project Name required for upload";
        result.classList.add('hide');
    } else {
        project_name_error.innerHTML = "";
        p_name = project_name;
        result.classList.remove('hide');
    }
}
jQuery(document).ready(function () {
    jQuery("#project_name").select2({
        width: "100%"
    });
    jQuery("#project_name").change(function () {
        validate_project_name();
    });
    setInterval(function () {
        fetch_user_list();
    }, 500);
});