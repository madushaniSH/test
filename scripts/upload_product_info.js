let user_selection = {
    project_name: '',
    missing_product_json: {}
};

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

function upload_product_info(event) {
    jQuery('#upload_message').html('');
    jQuery('#display_message').html('');
    let file = event.target.files[0];
    if (user_selection.project_name != '') {
        jQuery('#upload_message').html('');
        jQuery('#display_message').html('');
        $('#load_section').removeClass('hide');
        document.getElementById('project_name').disabled = true;
        var file_name = jQuery(this).val();
        var file_name = file_name.split('\\').pop();
        var extension = file_name.split('.').pop();
        if (extension != 'csv') {
            $('#load_section').addClass('hide');
            $('#display_message').html('Only CSV files can be uploaded');
            document.getElementById('project_name').disabled = false;
        } else {
            $('#display_message').html('');
            let formData = new FormData();
            formData.append('csv', file);
            formData.append('db_name', user_selection.project_name);
            jQuery('#upload_message').html('Please wait ' + file_name + ' is now being processed.<br>');
            jQuery.ajax({
                url: 'process_product_info.php',
                type: 'POST',
                data: formData,
                dataType: "JSON",
                success: function (data) {
                    if (data[0].result.length > 0) {
                        JSONToCSVConvertor(data[0].result, "Missed Product List", true);
                        $('#display_message').html(data[0].result.length + ' products have been not found. Please check the downloaded csv file for more info');
                    }
                    $('#load_section').addClass('hide');
                    $('#upload_message').html('File has been processed');
                },
                error: function (data) {
                    alert("AJAX error");
                },
                cache: false,
                contentType: false,
                processData: false
            });
            document.getElementById('project_name').disabled = false;
            $('#load_section').addClass('hide');
        }
    }
}

const validate_project_name = () => {
    const project_name = $('#project_name').val();
    jQuery('#upload_message').html('');
    jQuery('#display_message').html('');
    $('#selected_project_name').html(project_name);
    if (project_name != '') {
        user_selection.project_name = project_name;
        $('#product_csv_upload_label').removeClass('hide');
    } else {
        user_selection.project_name = '';
        $('#product_csv_upload_label').addClass('hide');
    }

}

$(document).ready(function () {
    $('#project_name').select2({
        width: '100%',
    });
    $("#project_name").change(function () {
        validate_project_name();
    });
    $('#product_csv_upload').change(upload_product_info);
});