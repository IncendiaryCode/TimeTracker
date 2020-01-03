var projectChart;
function __project_details(res) {
    var ctx = document.getElementById('main-chart').getContext('2d');
    gradient = ctx.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
    var chartData= [];
    for(var i=0; i<res["datasets"].length; i++)
    {
    chartData[i] = res["datasets"][i];
    }

    var config = {
        type: 'line',
        data: 
        {
            labels: res['labels'],  
            datasets: chartData,
        },
        options: {
            responsive: true,
            title: {
                display: true
            },
            tooltip: {
                display: false,
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
                    scaleLabel: {
                        display: true,
                        labelString: 'days',
                    },
                }],
                yAxes: [{
                    gridLines: {
                        display: true,
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
    if (projectChart) projectChart.destroy();
    projectChart = new Chart(ctx, config);
}
$(document).ready(function() {
    if (document.getElementById('main-chart')) {
        if ((document.getElementById('cur-month').value == "") || (document.getElementById('cur-month').value == " ")) {
            var month_no = (new Date().getMonth() + 1).toString();
                if(month_no == 1)
                    {
                        month_no = '0'+month_no;
                    }
            var curr_month = new Date().getFullYear().toString() + '-' + month_no;
            document.getElementById('cur-month').value = curr_month;
            console.log(curr_month);
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