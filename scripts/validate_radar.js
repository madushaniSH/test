let p_name = '';
const get_ticket_list = () => {
    if (p_name != "") {
        var formData = new FormData();
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: "get_project_tickets.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                $("#ticket").append(
                    '<option value="" selected disabled>Select</option>'
                );
                // adding missing options
                for (var i = 0; i < data[0].ticket_list.length; i++) {
                    if (
                        !$("#ticket").find(
                            'option[value="' + data[0].ticket_list[i].project_ticket_system_id + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#ticket").append(
                            '<option value="' +
                            data[0].ticket_list[i].project_ticket_system_id +
                            '">' +
                            data[0].ticket_list[i].ticket_id +
                            "</option>"
                        );
                    }
                }
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
    const project_name_element = document.getElementById('project_name');
    const project_name = project_name_element.options[project_name_element.selectedIndex].value;
    const project_name_error = document.getElementById('project_name_error');
    const probe_hunt_counter = document.getElementById('probe_hunt_counter');
    const hunter_counter = document.getElementById('hunter_counter');
    const ticket_section = document.getElementById('ticket_section');
    const probe_hunt_options = document.getElementById('probe_hunt_options');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for upload';
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
        probe_hunt_options.classList.add('hide');
    } else {
        $('#ticket').empty();
        project_name_error.innerHTML = '';
        probe_hunt_counter.classList.add('hide');
        hunter_counter.classList.add('hide')
        ticket_section.classList.remove('hide');
        probe_hunt_options.classList.add('hide');
        p_name = project_name;
        get_ticket_list();
    }
}
const validate_ticket_name = () => {
    if (p_name != "") {
        const ticket = jQuery('#ticket').val();
        const probe_hunt_options = document.getElementById('probe_hunt_options');
        const probe_hunt_counter = document.getElementById('probe_hunt_counter');
        const hunter_counter = document.getElementById('hunter_counter');
        if (ticket == "" || ticket == null) {
            probe_hunt_options.classList.add('hide');
            probe_hunt_counter.classList.add('hide');
            hunter_counter.classList.add('hide');
        } else {
            probe_hunt_options.classList.remove('hide');
            probe_hunt_counter.classList.remove('hide');
            hunter_counter.classList.remove('hide');
            selected_ticket = ticket;
        }
    }
}

jQuery(document).ready(function () {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#ticket').select2({
        width: '50%',
    });
    jQuery('#ticket').on('select2:select', function () {
        validate_ticket_name();
    });
    jQuery('#project_name').on('select2:select', function () {
        validate_project_name();
    });
    $("#show_button").mouseleave(function () {
        $("#counters").fadeOut();
        document.getElementById('arrow_sec').classList.add('bounce');
    });
    $("#show_button").mouseenter(function () {
        document.getElementById('counters').classList.remove('hide');
        document.getElementById('arrow_sec').classList.remove('bounce');
        $("#counters").fadeIn();
    });
});