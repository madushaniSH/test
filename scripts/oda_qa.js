// object used for storing user selection options
let this_selection_info = {
    project_name: '',
    ticket_selection: [],
    client_cat: '',
};

// function which adds the hide class the passed element
const hideElement = (elementName) => {
    const element = document.getElementById(elementName);
    element.classList.add('hide');
};

// function which removes the hide class the passed element
const showElement = (elementName) => {
    const element = document.getElementById(elementName);
    element.classList.remove('hide');
};

const update_oda_qa_count = () => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0 && this_selection_info.client_cat !== '') {
        let formData = new FormData();
        formData.append('project_name', this_selection_info.project_name);
        formData.append('ticket', this_selection_info.ticket_selection);
        formData.append('client_cat', this_selection_info.client_cat);
        jQuery.ajax({
            url: "fetch_oda_qa_count.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                console.log(data);
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
};

const get_ticket_list = () => {
    if (this_selection_info.project_name !== "") {
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
};

const fetch_client_cat = () => {
    if (this_selection_info.project_name !== '' && this_selection_info.ticket_selection.length !== 0) {
        let formData = new FormData();
        formData.append("project_name", this_selection_info.project_name);
        jQuery.ajax({
            url: "fetch_client_cat.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // adding missing options
                for (let i = 0; i < data[0].client_cat_info.length; i++) {
                    if (
                        !$("#client_category").find(
                            'option[value="' + data[0].client_cat_info[i].client_category_name + '"]'
                        ).length
                    ) {
                        // Append it to the select
                        $("#client_category").append(
                            '<option value="' +
                            data[0].client_cat_info[i].client_category_name +
                            '">' +
                            data[0].client_cat_info[i].client_category_name +
                            "</option>"
                        );
                    }
                }
                validate_client_cat();
            },
            error: function (data) {
                alert("Error assigning probe. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
};

const validate_client_cat = () => {
    const client_category = $('#client_category').val();
    if (client_category !== '') {
        showElement('qa_section');
        this_selection_info.client_cat = client_category;
    } else {
        hideElement('qa_section');
        this_selection_info.client_cat = '';
    }
};

const validate_ticket_name = () => {
    // this makes an array  containing ticket ids
    const ticket_options = $('#ticket').val();
    $('#client_category').empty();
    if (ticket_options.length > 0) {
        this_selection_info.ticket_selection = ticket_options;
        fetch_client_cat();
        showElement('client_cat');
        hideElement('qa_section');
    } else {
        this_selection_info.ticket_selection = [];
        hideElement('client_cat');
        hideElement('qa_section');
    }
};

const validate_project_name = () => {
    const project_name = $('#project_name').val();
    if (project_name !== '') {
        this_selection_info.project_name = project_name;
        $('#ticket').empty();
        get_ticket_list();
        showElement('ticket_section');
        hideElement('client_cat');
        hideElement('qa_section');
    } else {
        this_selection_info.project_name = '';
        hideElement('ticket_section');
        hideElement('client_cat');
        hideElement('qa_section');
    }
};


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
    $("#client_category").change(function () {
        validate_client_cat();
    });
});