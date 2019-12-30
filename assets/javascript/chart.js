function __project_details(res) {
    console.log(res);
    var ctx = document.getElementById('main-chart').getContext('2d');
    gradient = ctx.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
    var project_names = [];
    var data_values = [];
    var date = [];
    var color = [];
    var dataset = [];
    var project_data;
/*    for (var i = 0; i < res.length; i++) {
        color[i] = res[i]['color_code'];
        for (var j = 0; j < res[i].length; j++) {
            if ((res[i][j]["project_name"] == null)) {
                date[j] = res[i][j]['task_date'];
                data_values[j] = 0;
            } else {
                date[j] = res[i][j]['task_date'];
                data_values[j] = res[i][j]['time_used'] / 60;
                project_names[i] = res[i][j]['project_name'];
            }
        }
        for (var ind = 0; ind < data_values.length; ind++) {
            var task_time_dec = data_values[ind] - Math.floor(data_values[ind]);
            task_time_dec = task_time_dec.toString().slice(0, 4);
            var total_time = Math.floor(data_values[ind]) + parseFloat(task_time_dec);
            data_values[ind] = total_time;
        }
        project_data = {
            type: 'line',
            label: project_names[i],
            borderColor: window.chartColors.blue,
            borderWidth: 2,
            fill: false,
            data: data_values,
        };
        data_values = 0;
        dataset[i] = project_data;        
    }*/
    var chartData = {
        labels: res['labels'],
        datasets: res["datasets"]
    };

    var config = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            title: {
                display: true
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        beginAtZero: true,
                    },
                    ticks: {
                        display: true,
                        beginAtZero: true,
                        stacked: true
                    },
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: true
                    },
                    ticks: {
                        display: true,
                        beginAtZero: true,
                        stacked: true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Total time in hrs',
                    }
                }]
            }
        }
    };
    new Chart(ctx, config);
}
$(document).ready(function() {
    if (document.getElementById('main-chart')) {
        if ((document.getElementById('cur-month').value == "") || (document.getElementById('cur-month').value == " ")) {
            var curr_month = new Date().getFullYear().toString() + '-' + (new Date().getMonth() + 1).toString();
            document.getElementById('cur-month').value = curr_month;
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
                data: { 'type': "get_graph_data", "month": curr_month },
                success: function(res) {
                    var result = JSON.parse(res);
                    usernames = result['result'];
                    __project_details(usernames);
                }
            });
        }
    }
    $('#view-dashboard-chart').click(function() {
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
            data: { 'type': "get_graph_data", "month": document.getElementById('cur-month').value },
            success: function(res) {
                var result = JSON.parse(res);
                usernames = result['result'];
                __project_details(usernames);
            }
        });
    });
});