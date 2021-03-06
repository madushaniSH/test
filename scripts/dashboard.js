/*
    Author: Malika Liyanage
*/

Chart.plugins.unregister(ChartDataLabels);
let table;
let hunter_table;
let result_table_product;
/*
    This function fetches the project list from the db using an ajax call according to the region selected in
    "project_region" select box. The fetched list is then rendered on the multiple selection
    select box "project_name".
*/
const fetch_project_list = (region_element, project_name_element) => {
    const project_region = $('#' + region_element).val();
    if (project_region != '') {
        let formData = new FormData();
        formData.append('project_region', project_region);
        jQuery.ajax({
            url: "fetch_project_list_by_region.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                // removes all current options present in the select element
                $('#' + project_name_element).empty();
                for (let i = 0; i < data[0].project_info.length; i++) {
                    $('#' + project_name_element).append('<option value="' + data[0].project_info[i].name + '">' + data[0].project_info[i].name + "</option>");
                }
            },
            error: function (data) {
                alert("Error fetching project info. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

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
                if (data[0].current_info.rename_accuracy != null) {
                    $('#rename_accuracy').html(data[0].current_info.rename_accuracy + " %");
                }
            }
            let max_size = data[0].hunter_summary.length;
            let project_size = data[0].project_summary.length;
            $('#project_section').empty();
            for (let i = 0; i < project_size; i++) {
                let rank_style = '';
                switch (data[0].project_summary[i].Rank) {
                    case 1: rank_style = '<span style="color:#D6AF36;">'; break;
                    case 2: rank_style = '<span style="color:silver;">'; break;
                    case 3: rank_style = '<span style="color:#824A02;">'; break;
                    default: rank_style = '<span>'; break;
                }
                let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].project_summary[i].Rank + '</p></div><div class="col-md-2"><p>' + rank_style + data[0].project_summary[i].styling + data[0].project_summary[i].name + '</span>' + '</p></div><div class="col"><p>' + data[0].project_summary[i].productivity + '</p></div><div class="col"><p>' + data[0].project_summary[i].resubmit_count + '</p></div><div class="col"><p> ' + data[0].project_summary[i].accuracy + ' %' + '</p></div><div class="col-md-2"><p> ' + data[0].project_summary[i].rename_accuracy + ' %' + '</p></div><div class="col"><p> ' + data[0].project_summary[i].qa_accuracy + ' %' + '</p></div><div class="col-md-2"><p> ' + data[0].project_summary[i].qa_rename_accuracy + ' %' + '</p></div><div class="col"><p>' + data[0].project_summary[i].points + '</p></div></div>';
                $('#project_section').append(html);
            }
            let flag = false;
            $('#leader_board_section').empty();
            for (let i = 0; i < max_size; i++) {
                if (i < 3) {
                    if (data[0].hunter_summary[i].Points > 5) {
                        let rank_style = '';
                        switch (data[0].hunter_summary[i].Rank) {
                            case 1: rank_style = '<span style="color:#D6AF36;"><i class="fas fa-crown"></i> '; break;
                            case 2: rank_style = '<span style="color:silver;"> '; break;
                            case 3: rank_style = '<span style="color:#824A02;"> '; break;
                            default: rank_style = '<span>'; break;
                        }
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col-md-3"><p>' + rank_style + data[0].hunter_summary[i].name + '</span>' + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col-md-3"><p> ' + data[0].hunter_summary[i].rename_accuracy + ' %' + '</p></div><div class="col"><p> ' + data[0].hunter_summary[i].Accuracy + ' %' + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
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
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col-md-3"><p>' + data[0].hunter_summary[i].name + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col-md-3"><p> ' + data[0].hunter_summary[i].rename_accuracy + ' %' + '</p></div><div class="col"><p> ' + data[0].hunter_summary[i].Accuracy + ' %' + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
                        $('#bottom_board_section').append(html);
                    } else {
                        break;
                    }
                }
            }
            if (data[0].is_super !== 'yes') {
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
                    myChart.render();
                } else {
                    document.getElementById('display_message_chart').classList.remove('hide');
                    document.getElementById('error_type_chart').classList.add('hide');
                }
            } else {
                let ctx = document.getElementById('product_chart').getContext('2d');
                let myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                        ],
                        datasets: []
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        responsive: true,
                        legend: {
                            position: "bottom",
                            labels: {
                                fontColor: "white",
                            }
                        },
                        title: {
                            display: true,
                            text: "Product Count (without Project Language Weight) " + cycle_start.toISOString().slice(0, 19).replace('T', ' ') + " to " + cycle_end.toISOString().slice(0, 19).replace('T', ' '),
                            fontColor: "white"
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: "white"
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontColor: "white"
                                }
                            }]
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: 'white',
                                formatter: Math.round,
                                font: {
                                    weight: 'bold',
                                },
                            }
                        }

                    }
                });
                color = getRandomColor();
                let this_dataset = {
                    label: 'Brand Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    myChart.data.labels.push(data[0].project_summary[i].name);
                    this_dataset.data.push(data[0].project_summary[i].brand)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'SKU Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].sku)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'DVC Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].dvc)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'Facing Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].facing)
                }
                myChart.data.datasets.push(this_dataset);
                myChart.update();
                myChart.render();
            }
            if (data[0].is_admin != '') {
                hunter_table.clear().draw();
                for (let i = 0; i < data[0].hunter_summary.length; i++) {
                    hunter_table.row.add([
                        data[0].hunter_summary[i].Rank,
                        data[0].hunter_summary[i].name,
                        data[0].hunter_summary[i].region,
                        data[0].hunter_summary[i].productivity,
                        data[0].hunter_summary[i].rename_accuracy,
                        data[0].hunter_summary[i].Accuracy,
                        data[0].hunter_summary[i].Points,
                    ]).draw(false);
                    // if hunter gid doesnot exist in the select box adds it
                    if (!($("#hunter_filter_rank option[value='" + data[0].hunter_summary[i].name + "']").length > 0)) {
                        $('#hunter_filter_rank').append('<option value="' + data[0].hunter_summary[i].name + '">' + data[0].hunter_summary[i].name + "</option>");
                    }
                    if (!($("#hunter_filter_region option[value='" + data[0].hunter_summary[i].region + "']").length > 0)) {
                        $('#hunter_filter_region').append('<option value="' + data[0].hunter_summary[i].region + '">' + data[0].hunter_summary[i].region + "</option>");
                    }
                }
                let ctx = document.getElementById('error_type_chart_project_comp').getContext('2d');
                let myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                        ],
                        datasets: []
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        responsive: true,
                        legend: {
                            position: "bottom",
                            labels: {
                                fontColor: "white",
                            }
                        },
                        title: {
                            display: true,
                            text: "Overall Errors from " + cycle_start.toISOString().slice(0, 19).replace('T', ' ') + " to " + cycle_end.toISOString().slice(0, 19).replace('T', ' '),
                            fontColor: "white"
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: "white"
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontColor: "white"
                                }
                            }]
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: 'white',
                                formatter: Math.round,
                                font: {
                                    weight: 'bold',
                                },
                            }
                        }

                    }
                });
                color = getRandomColor();
                let this_dataset = {
                    label: 'Error Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    myChart.data.labels.push(data[0].project_summary[i].name);
                    this_dataset.data.push(data[0].project_summary[i].error_count - data[0].project_summary[i].system_error_count)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'Rename Error Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].rename_count)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'System Error Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].system_error_count)
                }
                myChart.data.datasets.push(this_dataset);
                color = getRandomColor();
                this_dataset = {
                    label: 'Resubmit Count',
                    borderWidth: 2,
                    backgroundColor: color,
                    data: []
                }
                for (let i = 0; i < project_size; i++) {
                    this_dataset.data.push(data[0].project_summary[i].resubmit_count)
                }
                myChart.data.datasets.push(this_dataset);
                myChart.update();
                myChart.render();
            }
            document.getElementById("loader").style.display = "none";
            document.getElementById("main_div").style.display = "block";
        },
        error: function (data) {
            alert("Error fetching main dashboard info. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}
const getRandomColor = () => {
    let letters = '0123456789ABCDEF'.split('');
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

const fetch_project_error_lists = () => {
    document.getElementById('display_message_error_type_chart').classList.add('hide');
    const project_region = $('#project_region_error_type').val();
    const project_list = $('#project_name_error_type').val();
    const start_datetime = $('#datetime_filter_error_type').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm:ss');
    const end_datetime = $('#datetime_filter_error_type').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm:ss');
    const load_section = document.getElementById('load_section_error_type');
    if (project_region != '' && start_datetime != '' && end_datetime != '') {
        load_section.classList.remove('hide');
        let formData = new FormData();
        formData.append('project_region', project_region);
        formData.append('project_list', project_list);
        formData.append('start_datetime', start_datetime);
        formData.append('end_datetime', end_datetime);
        jQuery.ajax({
            url: "fetch_dashboard_error_list.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                document.getElementById("chart-container").innerHTML = '&nbsp;';
                document.getElementById("chart-container").innerHTML = '<canvas id="error_type_chart_project"></canvas>';
                if (data[0].region_error_count == 0 && data[0].summary.length == 0) {
                    document.getElementById('display_message_error_type_chart').classList.remove('hide');
                    document.getElementById('error_type_chart_project').classList.add('hide');
                } else {
                    if (data[0].summary.length > 0) {
                        document.getElementById('display_message_error_type_chart').classList.add('hide');
                        document.getElementById('error_type_chart_project').classList.remove('hide');
                        document.getElementById('error_type_chart_project').innerHTML = '';
                        let ctx = document.getElementById('error_type_chart_project').getContext('2d');
                        let myChart = new Chart(ctx, {
                            data: [],
                            type: 'horizontalBar',
                            options: {
                                scales: {
                                    xAxes: [
                                        {
                                            stacked: true,
                                            ticks: {
                                                fontColor: "white",
                                            }
                                        }
                                    ],
                                    yAxes: [
                                        {
                                            stacked: true,
                                            ticks: {
                                                fontColor: "white",
                                            }
                                        }
                                    ],
                                },
                                responsive: true,
                                maintainAspectRatio: false,
                                legend: {
                                    position: 'right',
                                    labels: {
                                        fontColor: "white",
                                    }
                                },
                            },

                        });
                        let this_dataset = [];
                        for (let m = 0; m < data[0].summary.length; m++) {
                            myChart.data.labels.push(data[0].summary[m].error_name);
                            for (let n = 0; n < data[0].summary[m].hunter_info.length; n++) {
                                this_dataset = {
                                    label: [],
                                    data: [],
                                };
                                let found = false;
                                myChart.data.datasets.forEach(dataset => {
                                    if (dataset.label == data[0].summary[m].hunter_info[n].hunter_gid) {
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    this_dataset.label.push(data[0].summary[m].hunter_info[n].hunter_gid);
                                    myChart.data.datasets.push(this_dataset);
                                }
                            }
                        }
                        myChart.data.datasets.forEach(dataset => {
                            let color = getRandomColor();
                            dataset.backgroundColor = color;
                            for (let m = 0; m < data[0].summary.length; m++) {
                                let found = false;
                                for (let n = 0; n < data[0].summary[m].hunter_info.length; n++) {
                                    if (dataset.label == data[0].summary[m].hunter_info[n].hunter_gid) {
                                        found = true;
                                        dataset.data.push(data[0].summary[m].hunter_info[n].error_count);
                                        break;
                                    }
                                }
                                if (!found) {
                                    dataset.data.push(0);
                                }
                            }
                        });
                        myChart.update();
                        myChart.render();
                    }
                    if (data[0].project_summary.length > 0) {
                        document.getElementById('display_message_error_type_chart').classList.add('hide');
                        document.getElementById('error_type_chart_project').classList.remove('hide');
                        document.getElementById('error_type_chart_project').innerHTML = '';
                        let ctx = document.getElementById('error_type_chart_project').getContext('2d');
                        let myChart = new Chart(ctx, {
                            data: [],
                            type: 'horizontalBar',
                            options: {
                                scales: {
                                    xAxes: [
                                        {
                                            stacked: true,
                                            ticks: {
                                                fontColor: "white",
                                            }
                                        }
                                    ],
                                    yAxes: [
                                        {
                                            stacked: true,
                                            ticks: {
                                                fontColor: "white",
                                            }
                                        }
                                    ],
                                },
                                responsive: true,
                                maintainAspectRatio: false,
                                legend: {
                                    position: 'right',
                                    labels: {
                                        fontColor: "white",
                                    }
                                },
                            },

                        });
                        let this_dataset = [];
                        for (let m = 0; m < data[0].project_summary.length; m++) {
                            this_dataset = {
                                label: [],
                                data: [],
                                backgroundColor: ''
                            };
                            let color = getRandomColor();
                            this_dataset.backgroundColor = color;
                            this_dataset.label.push(data[0].project_summary[m].region);
                            for (let n = 0; n < data[0].project_summary[m].errors.length; n++) {
                                let found = false;
                                myChart.data.labels.forEach(label => {
                                    if (label == data[0].project_summary[m].errors[n].error_name) {
                                        found = true;
                                    }
                                })
                                if (!found) {
                                    myChart.data.labels.push(data[0].project_summary[m].errors[n].error_name);
                                }
                            }
                            myChart.data.datasets.push(this_dataset);
                        }
                        let this_data = [];
                        for (let m = 0; m < data[0].project_summary.length; m++) {
                            this_data = [];
                            myChart.data.labels.forEach(label => {
                                let label_found = false;
                                for (let n = 0; n < data[0].project_summary[m].errors.length; n++) {
                                    if (label == data[0].project_summary[m].errors[n].error_name) {
                                        this_data.push(data[0].project_summary[m].errors[n].error_count);
                                        label_found = true;
                                    }
                                }
                                if (!label_found) {
                                    this_data.push(0);
                                }
                            });
                            myChart.data.datasets.forEach(dataset => {
                                if (dataset.label == data[0].project_summary[m].region) {
                                    dataset.data = this_data;
                                }
                            });
                        }
                        myChart.update();
                        myChart.render();
                    }
                }
                load_section.classList.add('hide');
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


const fetch_hunter_products = () => {
    const project_region = $('#project_region').val();
    const project_list = $('#project_name').val();
    const start_datetime = $('#datetime_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const end_datetime = $('#datetime_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    const load_section = document.getElementById('load_section');
    const table_section = document.getElementById('table_section');
    if (project_region != '' && start_datetime != '' && end_datetime != '') {
        load_section.classList.remove('hide');
        table_section.classList.add('hide');
        let formData = new FormData();
        formData.append('project_region', project_region);
        formData.append('project_list', project_list);
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
                let rename_total_count = 0;
                for (let i = 0; i < data[0].summary.length; i++) {
                    table.row.add([
                        data[0].summary[i].project_name,
                        data[0].summary[i].date,
                        data[0].summary[i].user_gid,
                        data[0].summary[i].brand,
                        data[0].summary[i].sku,
                        data[0].summary[i].dvc,
                        data[0].summary[i].facing,
                        data[0].summary[i].errors,
                        data[0].summary[i].rename,
                        data[0].summary[i].product_info,
                    ]).draw(false);
                    sku_total_count += data[0].summary[i].sku;
                    brand_total_count += data[0].summary[i].brand;
                    dvc_total_count += data[0].summary[i].dvc;
                    facing_total_count += data[0].summary[i].facing;
                    error_total_count += data[0].summary[i].errors;
                    rename_total_count += data[0].summary[i].rename;
                    // if hunter gid doesnot exist in the select box adds it
                    if (!($("#hunter_filter option[value='" + data[0].summary[i].user_gid + "']").length > 0)) {
                        $('#hunter_filter').append('<option value="' + data[0].summary[i].user_gid + '">' + data[0].summary[i].user_gid + "</option>");
                    }
                }
                document.getElementById('sku_count').innerHTML = sku_total_count;
                document.getElementById('brand_count').innerHTML = brand_total_count;
                document.getElementById('dvc_count').innerHTML = dvc_total_count;
                document.getElementById('facing_count').innerHTML = facing_total_count;
                document.getElementById('error_count').innerHTML = error_total_count;
                document.getElementById('rename_count').innerHTML = rename_total_count;
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

const search_product_information = () => {
    const product_name_section = document.getElementById('product_name_filter');
    const product_name = product_name_section.value.trim();
    const load_section_explorer = document.getElementById('load_section_explorer');
    const result_table_product_section = document.getElementById('result_section_products');
    if (product_name !== '') {
        load_section_explorer.classList.remove('hide');
        product_name_section.disabled = true;
        let formData = new FormData();
        formData.append('product_name', product_name);
        jQuery.ajax({
            url: "search_product_information.php",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function (data) {
                product_name_section.disabled = false;
                result_table_product.clear().draw();
                for (let i = 0; i < data[0].return_details.length; i++) {
                    result_table_product.row.add([
                        data[0].return_details[i].project_name,
                        data[0].return_details[i].ticket_id,
                        data[0].return_details[i].product_name,
                        data[0].return_details[i].alt_product_name,
                        data[0].return_details[i].product_hunt_type,
                        data[0].return_details[i].hunter_gid,
                        data[0].return_details[i].qa_gid,
                        data[0].return_details[i].status,
                    ]).draw(false);
                    if (!($("#project_filter_result option[value='" + data[0].return_details[i].project_name + "']").length > 0)) {
                        $('#project_filter_result').append('<option value="' + data[0].return_details[i].project_name + '">' + data[0].return_details[i].project_name + "</option>");
                    }
                }
                load_section_explorer.classList.add('hide');
                result_table_product_section.classList.remove('hide');
            },
            error: function (data) {
                alert("Error fetching product_information. Please refresh");
            },
            cache: false,
            contentType: false,
            processData: false
        });
    } else {
        load_section_explorer.classList.add('hide');
        product_name_section.disabled = false;
        result_table_product_section.classList.add('hide');
    }
};

jQuery(document).ready(function () {
    hunter_table = $('#dataTableHunter').DataTable({
    });
    result_table_product = $('#result_table_product').DataTable({
    });
    fetch_project_list('project_region', 'project_name');
    fetch_project_list('project_region_error_type', 'project_name_error_type');
    // when a new project region is selected fetches an updated project list
    $('#project_region').on('select2:select', function (e) {
        fetch_project_list('project_region', 'project_name');
    });
    $('#project_region_error_type').on('select2:select', function (e) {
        fetch_project_list('project_region_error_type', 'project_name_error_type');
    });
    fetch_dashboard_info();
    setInterval(function () {
        fetch_dashboard_info();
    }, 36000000);
    $('#datetime_filter').daterangepicker({
        "opens": "right",
        "drops": "up"
    });
    $('#datetime_filter_explorer').daterangepicker({
        "opens": "right",
        "drops": "up"
    });
    $('#datetime_filter_error_type').daterangepicker({
        "opens": "right",
        "drops": "up",
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(8, 'hour'),
        locale: {
            format: 'M/DD HH:mm A',
        }
    });
    jQuery('#project_region').select2({
        width: '100%',
    });
    jQuery('#project_region_error_type').select2({
        width: '100%',
    });
    jQuery('#project_name').select2({
        width: '100%',
    });
    jQuery('#project_name_error_type').select2({
        width: '100%',
    });
    jQuery('#hunter_filter').select2({
        width: '50%',
    });
    jQuery('#hunter_filter_rank').select2({
        width: '50%',
    });
    jQuery('#hunter_filter_region').select2({
        width: '25%',
    });
    jQuery('#project_region_explorer').select2({
        width: '100%',
    });
    jQuery('#project_name_explorer').select2({
        width: '100%',
    });
    jQuery('#project_filter_result').select2({
        width: '50%',
    });
    $('#fetch_details_hunter').click(() => {
        $('#hunter_filter').empty();
        $('#hunter_filter').append('<option value="">None</option>');
        $("#hunter_filter").val('').trigger("change");
        fetch_hunter_products();
    });
    $('#fetch_project_error_lists').click(() => {
        fetch_project_error_lists();
    });
    $('#fetch_project_explorer').click(() => {
        search_product_information();
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
    $("#hunter_filter_rank").change(function () {
        const hunter_gid = $('#hunter_filter_rank').val();
        if (hunter_gid == "" || hunter_gid == null) {
            hunter_table
                .column(1)
                .search("", true, false)
                .draw();
        } else {
            hunter_table
                .column(1)
                .search(hunter_gid, true, false)
                .draw();
        }
    });
    $("#hunter_filter_region").change(function () {
        const hunter_gid = $('#hunter_filter_region').val();
        if (hunter_gid == "" || hunter_gid == null) {
            hunter_table
                .column(2)
                .search("", true, false)
                .draw();
        } else {
            hunter_table
                .column(2)
                .search(hunter_gid, true, false)
                .draw();
        }
    });
    // searches for hunter gid and redraws table
    $("#hunter_filter").change(function () {
        const hunter_gid = $('#hunter_filter').val();
        if (hunter_gid == "" || hunter_gid == null) {
            table
                .column(2)
                .search("", true, false)
                .draw();
        } else {
            table
                .column(2)
                .search(hunter_gid, true, false)
                .draw();
        }
        let data = table.rows({ search: 'applied' }).data()
        let sku_total_count = 0;
        let brand_total_count = 0;
        let dvc_total_count = 0;
        let facing_total_count = 0;
        let error_total_count = 0;
        let rename_total_count = 0;
        if (hunter_gid != '') {
            for (let i = 0; i < data.length; i++) {
                sku_total_count += data[i][4];
                brand_total_count += data[i][3];
                dvc_total_count += data[i][5];
                facing_total_count += data[i][6];
                error_total_count += data[i][7];
                rename_total_count += data[i][8];
            }
        } else {
            data = table.rows().data();
            if (data.length != 0) {
                for (let i = 0; i < data.length; i++) {
                    sku_total_count += data[i][4];
                    brand_total_count += data[i][3];
                    dvc_total_count += data[i][5];
                    facing_total_count += data[i][6];
                    error_total_count += data[i][7];
                    rename_total_count += data[i][8];
                }
            }
        }
        if (data.length != 0) {
            document.getElementById('sku_count').innerHTML = sku_total_count;
            document.getElementById('brand_count').innerHTML = brand_total_count;
            document.getElementById('dvc_count').innerHTML = dvc_total_count;
            document.getElementById('facing_count').innerHTML = facing_total_count;
            document.getElementById('error_count').innerHTML = error_total_count;
            document.getElementById('rename_count').innerHTML = rename_total_count;
        }
    });
    $('#dataTable tbody').on('click', 'button', function () {
        let data = table.row($(this).parents('tr')).data();
        $('#product_detail_modal_title').html(data[0] + ' ' + data[1] + ' ' + data[2]);
        product_table.clear().draw();
        for (let i = 0; i < data[9][0].length; i++) {
            product_table.row.add([
                data[9][0][i].product_name,
                data[9][0][i].product_alt_design_name,
                data[9][0][i].product_type,
                data[9][0][i].product_creation_time,
                data[9][0][i].product_qa_datetime,
                data[9][0][i].product_qa_status,
                data[9][0][i].error_string,
                data[9][0][i].error_url,
            ]).draw(false);
        }
        $('#product_detail_modal').modal('show');
    });
    $('#product_data_table tbody').on('click', 'button', function () {
        let data = product_table.row($(this).parents('tr')).data();
        for (let j = 0; j < data[7].length; j++) {
            window.open(data[7][j].project_error_image_location);
        }
    });
    $("#project_filter_result").change(function () {
        const project_name = $('#project_filter_result').val();
        if (project_name == "" || project_name == null) {
            result_table_product
                .column(0)
                .search("", true, false)
                .draw();
        } else {
            result_table_product
                .column(0)
                .search(project_name, true, false)
                .draw();
        }
    });
});
