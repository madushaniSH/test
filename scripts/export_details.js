//Malika Liyanage
var p_name = "";
var filter_picked = false;
var start_datetime;
var end_datetime
var selected_option = '';
var selected_ticket;
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

const special = () => {
    let formData = new FormData();
    formData.append("start_datetime", '2019-08-01 04:30:00');
    formData.append("end_datetime", '2019-10-31 04:30:00');
    jQuery.ajax({
        url: 'special.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            JSONToCSVConvertor(data[0].hunter_summary, " Monthly Probe Summary 2019-08-01 04:30:00 to 2019-10-31 04:30:00", true);
            JSONToCSVConvertor(data[0].project_info_radar, " Monthly Radar Summary 2019-08-01 04:30:00 to 2019-10-31 04:30:00", true);
            JSONToCSVConvertor(data[0].project_info_ref, " Monthly Reference Summary 2019-08-01 04:30:00 to 2019-10-31 04:30:00", true);
            JSONToCSVConvertor(data[0].date, " Date Work Summary 2019-08-01 04:30:00 to 2019-10-31 04:30:00", true);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

const fetch_details_productivity = () => {
    let formData = new FormData();
    formData.append("start_datetime", start_datetime );
    formData.append("end_datetime", end_datetime);
    jQuery.ajax({
        url: 'fetch_productivity_final.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            JSONToCSVConvertor(data[0].hunter_summary, "Productivity Final Count " + start_datetime + ' ' + end_datetime, true);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

const fetch_details_productivity_single = () => {
    let formData = new FormData();
    formData.append("start_datetime", start_datetime );
    formData.append("end_datetime", end_datetime);
    jQuery.ajax({
        url: 'fetch_productivity_single.php',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            JSONToCSVConvertor(data[0].summary, "Productivity Single Count " + start_datetime + ' ' + end_datetime, true);
        },
        error: function (data) {
            alert("Error assigning probe. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}


// gets projects details from server in json format
function fetch_details() {
    if (filter_picked) {
        var formData = new FormData();
        formData.append("start_datetime", start_datetime);
        formData.append("end_datetime", end_datetime);
        if (selected_option == 'product') {
            formData.append("ticket", selected_ticket);
            formData.append("project_name", p_name);
            jQuery.ajax({
                url: 'fetch_details_product.php',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    JSONToCSVConvertor(data[0].hunted_product_info, p_name + " " + $("#ticket_name option[value='" + selected_ticket + "']").text() + " Hunted Products " + start_datetime + " - " + end_datetime, true);
                },
                error: function (data) {
                    alert("Error assigning probe. Please refresh");
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
        if (selected_option == 'probe') {
            formData.append("ticket", selected_ticket);
            formData.append("project_name", p_name);
            jQuery.ajax({
                url: 'fetch_details_probe.php',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    JSONToCSVConvertor(data[0].probe_details, p_name + " " + $("#ticket_name option[value='" + selected_ticket + "']").text() + " Probe Details " + start_datetime + " - " + end_datetime, true);
                    console.log(data[0].warning)
                    if (data[0].reference_details.length != 0) {
                        JSONToCSVConvertor(data[0].reference_details, p_name + " " + $("#ticket_name option[value='" + selected_ticket + "']").text() + " Reference Details " + start_datetime + " - " + end_datetime, true);
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
        if (selected_option == 'hunter') {
            formData.append("project_name", p_name);
            jQuery.ajax({
                url: 'fetch_details_hunter.php',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                success: function (data) {
                    JSONToCSVConvertor(data[0].hunter_summary, p_name.join("_") + " Hunter Summary " + start_datetime + " - " + end_datetime, true);
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
}

function validate_date_time() {
    start_datetime = $('#datetime_filter').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
    end_datetime = $('#datetime_filter').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
    var export_button = document.getElementById('export_button');
    if (start_datetime != '' && end_datetime != '') {
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

function show_product_info() {
    const generate_productivity_section = document.getElementById('generate_productivity_section');
    generate_productivity_section.classList.add('hide');
    var project_select = document.getElementById('project_select');
    document.getElementById('export_button_productivity').classList.add('hide');
    document.getElementById('export_button_productivity_single').classList.add('hide');
    $("#project_name").select2({
        width: '100%',
        multiple: false
    });
    project_select.classList.remove('hide');
    selected_option = 'product';
    $("#project_name").select2().val("").trigger("change");
}

const show_productivity_info = () => {
    const generate_productivity_section = document.getElementById('generate_productivity_section');
    generate_productivity_section.classList.remove('hide');
    const project_select = document.getElementById('project_select');
    project_select.classList.add('hide');
    document.getElementById('ticket_section').classList.add('hide');
    document.getElementById('generate_csv_section').classList.add('hide');
    document.getElementById('export_button').classList.add('hide');
}

function show_probe_info() {
    const generate_productivity_section = document.getElementById('generate_productivity_section');
    generate_productivity_section.classList.add('hide');
    var project_select = document.getElementById('project_select');
    document.getElementById('export_button_productivity').classList.add('hide');
    document.getElementById('export_button_productivity_single').classList.add('hide');
    $("#project_name").select2({
        width: '100%',
        multiple: false
    });
    project_select.classList.remove('hide');
    selected_option = 'probe';
    $("#project_name").select2().val("").trigger("change");
}

function show_hunter_info() {
    const generate_productivity_section = document.getElementById('generate_productivity_section');
    generate_productivity_section.classList.add('hide');
    var project_select = document.getElementById('project_select');
    document.getElementById('export_button_productivity').classList.add('hide');
    document.getElementById('export_button_productivity_single').classList.add('hide');
    $("#project_name").select2({
        width: '100%',
        multiple: true
    });
    project_select.classList.remove('hide');
    selected_option = 'hunter';
    document.getElementById('generate_csv_section').classList.remove('hide');
    $("#project_name").prop("selectedIndex", 1).trigger("change");
}

function validate_project_name() {
    export_button.classList.add('hide');
    filter_picked = false;
    if (selected_option == 'product' || selected_option == 'probe') {
        var project_name_element = document.getElementById('project_name');
        var project_name = project_name_element.options[project_name_element.selectedIndex].value;
    } else if (selected_option == 'hunter') {
        var project_name = $('#project_name').val();
    }
    var project_name_error = document.getElementById('project_name_error');
    var upload_div = document.getElementById('ticket_section');
    if (project_name == '') {
        project_name_error.innerHTML = 'Project Name required for export';
        upload_div.classList.add('hide');
        document.getElementById('generate_csv_section').classList.add('hide');
    } else {
        project_name_error.innerHTML = '';
        if (selected_option != 'hunter') {
            upload_div.classList.remove('hide');
            document.getElementById('generate_csv_section').classList.add('hide');
            fetch_tickets();
        } else {
            upload_div.classList.add('hide');
            document.getElementById('generate_csv_section').classList.remove('hide');
        }
        p_name = project_name;
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
    jQuery('#ticket_name').select2({
        width: '100%',
    });
    jQuery('#ticket_name').change(validate_ticket_id);
    jQuery('#project_name').change(validate_project_name);
    $('#datetime_filter').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(8, 'hour'),
        locale: {
            format: 'M/DD HH:mm A',
        }
    });
    $('#datetime_filter_productivity').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(8, 'hour'),
        locale: {
            format: 'M/DD HH:mm A',
        }
    });
    $('#datetime_filter').on('apply.daterangepicker', function (ev, picker) {
        validate_date_time();
    });
    $('#datetime_filter_productivity').on('apply.daterangepicker', function (ev, picker) {
        start_datetime = $('#datetime_filter_productivity').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
        end_datetime = $('#datetime_filter_productivity').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
        let export_button = document.getElementById('export_button_productivity');
        let export_button_single = document.getElementById('export_button_productivity_single');
        if (start_datetime != '' && end_datetime != '') {
            export_button.classList.remove('hide');
            export_button_single.classList.remove('hide');
            filter_picked = true;

        } else {
            export_button.classList.add('hide');
            export_button_single.classList.add('hide');
            filter_picked = false;
        }
    });
});
