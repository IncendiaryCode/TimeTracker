function __project_details(res)
{
    var ctx = document.getElementById('main-chart').getContext('2d'); 
    gradient = ctx.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
    var project_names = [];
    var data = [];
    var color = [];
    for(var i=0;i<res.length; i++)
    {
        if((res[i]["project_name"] != undefined) && (res[i]["project_name"] != null))
                {
        project_names[i] = res[i]['project_name'];
        data[i] = res[i]['t_minutes']/60;
        color[i] = res[i]['color_code'];
        }
    }
   
    for(var ind=0; ind<data.length; ind++)
        {
            var task_time_dec = data[ind] - Math.floor(data[ind]);
            task_time_dec = task_time_dec.toString().slice(0,4);
            var total_time = Math.floor(data[ind]) + parseFloat(task_time_dec);
            data[ind] = total_time;
        }
    var project_data = {
            labels: project_names,
            borderColor: color,
            backgroundColor: color,
            datasets: [{
                label: 'Projects',
                data: data,
                fill: false,
            }]
        };
    var config = {
        type: 'line',
        data: project_data,
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
                        },ticks: {
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