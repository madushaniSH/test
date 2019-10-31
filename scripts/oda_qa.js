// object used for storing user selection options
let this_selection_info = {
    project_name: '',
    ticket_selection: [],
}

const get_ticket_list = () => {
    if (this_selection_info.project_name != "") {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        jQuery.ajax({
            url: "get_project_tickets.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
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

const fetch_client_cat = () => {
    if (this_selection_info.project_name != '' && this_selection_info.ticket_selection.length != 0) {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        formData.append("project_name", this_selection_info.ticket_selection);
        jQuery.ajax({
            url: "fetch_client_cat.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
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
const validate_ticket_name = () => {
    // this makes an array  containing ticket ids
    const ticket_options = $('#ticket').val();
    const client_cat = document.getElementById('client_cat');
    $('#client_cat').empty();
    if (ticket_options.length != 0) {
        this_selection_info.ticket_selection = ticket_options;
        fetch_client_cat();
        client_cat.classList.remove('hide');
    } else {
        this_selection_info.ticket_selection = [];
        client_cat.classList.add('hide');
    }
}

const validate_project_name = () => {
    const project_name = $('#project_name').val();
    const ticket_section = document.getElementById('ticket_section');
    if (project_name != '') {
        this_selection_info.project_name = project_name;
        $('#ticket').empty();
        get_ticket_list();
        ticket_section.classList.remove('hide');
    } else {
        ticket_section.classList.add('hide');
        this_selection_info.project_name = '';
    }
}


$(document).ready(function () {
    $('#project_name').select2({
        width: '100%',
    });
    $('#ticket').select2({
        width: '50%',
    });
    $('#client_category').select2({
        width: '50%',
    });
    $("#project_name").change(function () {
        validate_project_name();
    });
    $("#ticket").change(function () {
        validate_ticket_name();
    });
});