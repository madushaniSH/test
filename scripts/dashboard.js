/*
    Author: Malika Liyanage
*/

let table;

const fetch_dashboard_info = () => {
    document.getElementById("main_div").style.display = "none";
    document.getElementById("loader").style.display = "block";
    let formData = new FormData();
    let d = new Date();
    d.setHours(0, 0, 0, 0);
    d.setDate(15);
    let cycle_start = new Date();
    let cycle_end = new Date();
    cycle_start.setUTCHours(0, 0, 0, 0);
    cycle_end.setUTCHours(0, 0, 0, 0);
    if (cycle_start.getUTCDate() <= 15) {
        cycle_start.setUTCDate(16);
        cycle_start.setUTCHours(4, 30, 0, 0)
        cycle_start.setUTCMonth(cycle_start.getMonth() - 1);
        cycle_end.setUTCDate(16);
        cycle_end.setUTCHours(4, 30, 0, 0);
    } else {
        cycle_start.setUTCDate(16);
        cycle_start.setUTCDate(16);
        cycle_start.setUTCHours(4, 30, 0, 0)
        cycle_end.setUTCMonth(cycle_start.getMonth() + 1);
        cycle_end.setUTCDate(16);
        cycle_end.setUTCHours(4, 30, 0, 0)
    }
    formData.append('start_datetime', cycle_start.toISOString().slice(0, 19).replace('T', ' '));
    formData.append('end_datetime', cycle_end.toISOString().slice(0, 19).replace('T', ' '));
    jQuery.ajax({
        url: "fetch_dashboard_info.php",
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function (data) {
            if (data[0].current_info != null) {
                if (data[0].current_info.Points != null) {
                    $('#total_points').html(data[0].current_info.Points);
                }
                if (data[0].current_info.productivity != null) {
                    $('#productivity').html(data[0].current_info.productivity);
                }
                if (data[0].current_info.Accuracy != null) {
                    $('#overall_accuracy').html(data[0].current_info.Accuracy + ' %');
                }
                if (data[0].current_info.Rank != null) {
                    $('#ranking').html(data[0].current_info.Rank + " / " + data[0].total);
                }
            }
            let max_size = data[0].hunter_summary.length;
            let project_size = data[0].project_summary.length;
            $('#project_section').empty();
            for (let i = 0; i < project_size; i++) {
                let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].project_summary[i].Rank + '</p></div><div class="col"><p>' + data[0].project_summary[i].name + '</p></div><div class="col"><p>' + data[0].project_summary[i].productivity + '</p></div><div class="col"><p> ' + data[0].project_summary[i].accuracy + ' %' + '</p></div><div class="col"><p>' + data[0].project_summary[i].points + '</p></div>';
                $('#project_section').append(html);
            }
            let flag = false;
            $('#leader_board_section').empty();
            for (let i = 0; i < max_size; i++) {
                if (i < 3) {
                    if (data[0].hunter_summary[i].Points > 5) {
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col"><p>' + data[0].hunter_summary[i].name + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].productivity + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
                        $('#leader_board_section').append(html);
                    } else {
                        flag = true;
                    }
                } else {
                    break;
                }
            }
            $('#bottom_board_section').empty();
            if (!flag) {
                for (let i = max_size - 1; i > 0; i--) {
                    if (i > max_size - 4) {
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col"><p>' + data[0].hunter_summary[i].name + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].productivity + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
                        $('#bottom_board_section').append(html);
                    } else {
                        break;
                    }
                }
            }
            if (data[0].error_chart.length > 0) {
                document.getElementById('display_message_chart').classList.add('hide');
                document.getElementById('error_type_chart').classList.remove('hide');
                let ctx = document.getElementById('error_type_chart').getContext('2d');
                let myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            label: '# of Errors',
                            backgroundColor: [],
                            hoverBackgroundColor: [],
                            borderColor: [],
                        }],
                    },
                    options: {
                        animation: {
                            animateRotate: true,
                        },
                    }
                });
                for (let m = 0; m < data[0].error_chart.length; m++) {
                    myChart.data.labels.push(data[0].error_chart[m].error_name);
                    let color = getRandomColor();
                    myChart.data.datasets.forEach((dataset) => {
                        dataset.data.push(data[0].error_chart[m].count);
                    });
                    myChart.data.datasets[0].backgroundColor.push(color);
                    myChart.data.datasets[0].hoverBackgroundColor.push(color);
                    myChart.data.datasets[0].borderColor.push(color);
                }
                myChart.update();
            } else {
                document.getElementById('display_message_chart').classList.remove('hide');
                document.getElementById('error_type_chart').classList.add('hide');
            }
            document.getElementById("loader").style.display = "none";
            document.getElementById("main_div").style.display = "block";
        },
        error: function (data) {
            alert("Error fetching dashboard info. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}
const getRandomColor = () => {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

const fetch_hunter_products = () => {
    const project_region = $('#project_region').val();
    const start_datetime = $('#datetime_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const end_datetime = $('#datetime_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    const load_section = document.getElementById('load_section');
    const table_section = document.getElementById('table_section');
    if (project_region != '' && start_datetime != '' && end_datetime != '') {
        load_section.classList.remove('hide');
        table_section.classList.add('hide');
        let formData = new FormData();
        formData.append('project_region', project_region);
        formData.append('start_datetime', start_datetime);
        formData.append('end_datetime', end_datetime);
        jQuery.ajax({
            url: "fetch_hunter_product_summary.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                table.clear().draw();
                let sku_total_count = 0;
                let brand_total_count = 0;
                let dvc_total_count = 0;
                let facing_total_count = 0;
                let error_total_count = 0;
                for (let i = 0; i < data[0].summary.length; i++) {
                    table.row.add([
                        data[0].summary[i].project_name,
                        data[0].summary[i].date,
                        data[0].summary[i].brand,
                        data[0].summary[i].sku,
                        data[0].summary[i].dvc,
                        data[0].summary[i].facing,
                        data[0].summary[i].errors,
                        data[0].summary[i].product_info,
                    ]).draw(false);
                    sku_total_count += data[0].summary[i].sku;
                    brand_total_count += data[0].summary[i].brand;
                    dvc_total_count += data[0].summary[i].dvc;
                    facing_total_count += data[0].summary[i].facing;
                    error_total_count += data[0].summary[i].errors;
                }
                document.getElementById('sku_count').innerHTML = sku_total_count;
                document.getElementById('brand_count').innerHTML = brand_total_count;
                document.getElementById('dvc_count').innerHTML = dvc_total_count;
                document.getElementById('facing_count').innerHTML = facing_total_count;
                document.getElementById('error_count').innerHTML = error_total_count;
                load_section.classList.add('hide');
                table_section.classList.remove('hide');
            },
            error: function (data) {
                alert("Error fetching product_information. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

jQuery(document).ready(function () {
    fetch_dashboard_info();
    setInterval(function () {
        fetch_dashboard_info();
    }, 36000000);
    $('#datetime_filter').daterangepicker({
        "opens": "right",
        "drops": "up"
    });
    jQuery('#project_region').select2({
        width: '100%',
    });
    jQuery('#project_name').select2({
        width: '100%',
    });
    $('#fetch_details_hunter').click(() => {
        fetch_hunter_products();
    });
    table = $('#dataTable').DataTable({
        "columnDefs": [
            {
                "data": null,
                "defaultContent": "<button class='btn btn-success'><i class='fas fa-search-plus'></i></button>",
                "targets": -1
            }
        ]
    });
    let product_table = $('#product_data_table').DataTable({
        "columnDefs": [
            {
                "data": null,
                "defaultContent": "<button class='btn btn-danger'><i class='fas fa-glasses'></i></button>",
                "targets": -1
            }
        ]
    });
    $('#dataTable tbody').on('click', 'button', function () {
        let data = table.row($(this).parents('tr')).data();
        $('#product_detail_modal_title').html(data[0] + ' ' + data[1]);
        product_table.clear().draw();
        for (let i = 0; i < data[7][0].length; i++) {
            product_table.row.add([
                data[7][0][i].product_name,
                data[7][0][i].product_alt_design_name,
                data[7][0][i].product_type,
                data[7][0][i].product_creation_time,
                data[7][0][i].product_qa_datetime,
                data[7][0][i].product_qa_status,
                data[7][0][i].error_string,
                data[7][0][i].error_url
            ]).draw(false);
        }
        $('#product_detail_modal').modal('show');
    });
    $('#product_data_table tbody').on('click', 'button', function () {
        let data = product_table.row($(this).parents('tr')).data();
        if (data[7] != '') {
            window.open(data[7]);
        }
    });
});
