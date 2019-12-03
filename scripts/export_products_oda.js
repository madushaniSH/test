let p_name = '';
let filter_picked = false;
let start_datetime = '';
let end_datetime = '';
let selected_ticket = '';

function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

    var CSV = '';
    //Set Report title in first row or line

    CSV += ReportTitle + '\r\n\n';

    //This condition will generate the Label/Header
    if (ShowLabel) {
        var row = "";

        //This loop will extract the label from 1st index of on array
        for (var index in arrData[0]) {

            //Now convert each value to string and comma-seprated
            row += index + ',';
        }

        row = row.slice(0, -1);

        //append Label row with line break
        CSV += row + '\r\n';
    }

    //1st loop is to extract each row
    for (var i = 0; i < arrData.length; i++) {
        var row = "";

        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData[i]) {
            row += '"' + arrData[i][index] + '",';
        }

        row.slice(0, row.length - 1);

        //add a line break after each row
        CSV += row + '\r\n';
    }

    if (CSV == '') {
        alert("Invalid data");
        return;
    }

    //Generate a file name
    var fileName = "MyReport_";
    //this will remove the blank-spaces from the title and replace it with an underscore
    fileName += ReportTitle.replace(/ /g, "_");

    //Initialize file format you want csv or xls
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

    // Now the little tricky part.
    // you can use either>> window.open(uri);
    // but this will not work in some browsers
    // or you will not get the correct file extension

    //this trick will generate a temp <a /> tag
    var link = document.createElement("a");
    link.href = uri;

    //set the visibility hidden so it will not effect on your web-layout
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";

    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

const validate_ticket_toggle = () => {
    const ticketToggleState = document.getElementById('ticket_toggle').checked;
    const ticketElement = document.getElementById('ticket_name');
    if (ticketToggleState) {
        for (let i = 0; i < ticketElement.length; i++) {
            ticketElement.options[i].selected = true;
        }
    } else {
        for (let i = 0; i < ticketElement.length; i++) {
            ticketElement.options[i].selected = false;
        }
    }
    $('#ticket_name').trigger('change');
};

const fetch_details = () => {
    if (filter_picked) {
        let formData = new FormData();
        formData.append("start_datetime", start_datetime);
        formData.append("end_datetime", end_datetime);
        formData.append("ticket", selected_ticket);
        formData.append("project_name", p_name);
        jQuery.ajax({
            url: 'fetch_products_oda.php',
            type: 'POST',
            data: formData,
            dataType: 'JSON',
            success: function (data) {
                JSONToCSVConvertor(data[0].product_details, p_name + " " + $("#ticket_name option[value='" + selected_ticket + "']").text() + " Product_Details Details " + start_datetime + " - " + end_datetime, true);
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

function validate_date_time() {
    start_datetime = $('#datetime_filter').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
    end_datetime = $('#datetime_filter').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
    let export_button = document.getElementById('export_button');
    if (start_datetime !== '' && end_datetime !== '') {
        export_button.classList.remove('hide');
        filter_picked = true;

    } else {
        export_button.classList.add('hide');
        filter_picked = false;
    }
}

function validate_ticket_id() {
    var ticket_name = $('#ticket_name').val();
    if (ticket_name != "") {
        document.getElementById('generate_csv_section').classList.remove('hide');
        selected_ticket = ticket_name;
    } else {
        document.getElementById('generate_csv_section').classList.add('hide');
    }
}

function fetch_tickets() {
    var project_name_element = document.getElementById('project_name');
    var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    var formData = new FormData();
    formData.append('project_name', project_name);
    jQuery.ajax({
        url: 'get_ticket_list.php',
        type: 'POST',
        data: formData,
        success: function (data) {
            $('#ticket_name').html(data);
            $("#ticket_name option[value='']").remove();
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

const validate_project_name = () => {
    filter_picked = false;
    let project_name = $('#project_name').val();
    let project_name_error = document.getElementById('project_name_error');
    let upload_div = document.getElementById('ticket_section');
    if (project_name === '') {
        project_name_error.innerHTML = 'Project Name required for export';
        upload_div.classList.add('hide');
        document.getElementById('generate_csv_section').classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        upload_div.classList.remove('hide');
        document.getElementById('generate_csv_section').classList.add('hide');
        fetch_tickets();
        p_name = project_name;
    }
};

jQuery(document).ready(() => {
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#ticket_name').select2({
        width: '100%',
    });
    $('#datetime_filter').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(8, 'hour'),
        locale: {
            format: 'M/DD HH:mm A',
        }
    });
    jQuery('#project_name').change(validate_project_name);
    jQuery('#ticket_name').change(validate_ticket_id);
    $('#datetime_filter').on('apply.daterangepicker', function (ev, picker) {
        validate_date_time();
    });
    $("#ticket_toggle").click(function () {
        validate_ticket_toggle();
    });
});