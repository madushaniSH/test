/*
    Author: Malika Liyanage
*/

const fetch_dashboard_info = () => {
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
        cycle_end.setUTCDate(15);
        cycle_end.setUTCHours(4, 30, 0, 0);
    } else {
        cycle_start.setUTCDate(16);
        cycle_start.setUTCDate(16);
        cycle_start.setUTCHours(4, 30, 0, 0)
        cycle_end.setUTCMonth(cycle_start.getMonth() + 1);
        cycle_end.setUTCDate(15);
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
                if (data[0].current_info.Accuracy != null) {
                    $('#overall_accuracy').html(data[0].current_info.Accuracy);
                }
                if (data[0].current_info.Rank != null) {
                    $('#ranking').html(data[0].current_info.Rank + " / " + data[0].total);
                }
            }
            let max_size = data[0].hunter_summary.length;
            let flag = false;
            $('#leader_board_section').empty();
            for (let i = 0; i < max_size; i++) {
                if (i < 3) {
                    if (data[0].hunter_summary[i].Points > 5) {
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col"><p>' + data[0].hunter_summary[i].name + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
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
                        let html = '<hr class="divider my-0"> <div class="row my-2"><div class="col-md-1"><p> #' + data[0].hunter_summary[i].Rank + '</p></div><div class="col-md-1"><img class="img-profile rounded-circle leader_board_pic" src="' + data[0].hunter_summary[i].pic_location + '"></div><div class="col"><p>' + data[0].hunter_summary[i].name + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].region + '</p></div><div class="col"><p>' + data[0].hunter_summary[i].Points + '</p></div></div>';
                        $('#bottom_board_section').append(html);
                    } else {
                        break;
                    }
                }
            }
        },
        error: function (data) {
            alert("Error fetching dashboard info. Please refresh");
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

jQuery(document).ready(function () {
    setInterval(function () {
        fetch_dashboard_info();
    }, 5000);
});