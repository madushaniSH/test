
function handleFileSelect(evt) {
    var file = evt.target.files[0];
    var file_name = jQuery(this).val();
    var file_name = file_name.split('\\').pop();
    var extension = file_name.split('.').pop();
    if (extension != 'csv') {
        jQuery('#probe_upload_success').html('');
        jQuery('#probe_upload_error').html('Error. Only CSV files can be uploaded');
    } else {
        Papa.parse(file, {
            header: true,
            dynamicTyping: true,
            complete: function (results) {
                var formData = new FormData();
                var probe_percentage = document.getElementById('probe_percentage');
                for (var i = 0; i < results.data.length; i++) {
                    var brand = results.data[i].brand;
                    var category = results.data[i].category;
                    var probe_list = results.data[i].probes_with_high_other_percent;
                    if (probe_list != null) {
                        var formData = new FormData();
                        formData.append('brand', brand);
                        formData.append('category', category);
                        formData.append('probe_list', probe_list);
                        jQuery.ajax({
                            url: 'process_probe.php',
                            type: 'POST',
                            data: formData,
                            success: function (data) {
                                $('#probe_server_error').html(data);
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
            }
        });
        jQuery('#probe_upload_success').html('Processed ' + file_name);
        jQuery('#probe_upload_error').html('');
    }
}


jQuery(document).ready(function () {
    jQuery("#csv-file").change(handleFileSelect);
});