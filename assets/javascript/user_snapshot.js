function __draw_user_chart(res)
{

var user_chart = document.getElementById('user-chart').getContext('2d');
var color = Chart.helpers.color;
if(res['status'] == false)
{
    document.getElementById('user-chart-error').innerHTML = "This project does not have any data.";
    $('#user-chart').hide();
}
else{
$('#user-chart').show();
document.getElementById('user-chart-error').innerHTML = " ";
var user_data = [];

var task_labels = [];
var user_labels = [];

var task_time_value = [];
var user_time_value = [];
var user_task_name = [];

var task_value = res['result'][0];
var user_value = res['result'][1];

var chart_color = "000000";

for(var i=0; i<task_value.length; i++)
{
task_labels[i] = task_value[i]['task_name'];
task_time_value[i] = task_value[i]['time_used']/60;

}
var user_data =  [];
var task_array = {
        type: 'bar',
        label: 'Total tasks',
        backgroundColor:color(window.chartColors.red).alpha(0.5).rgbString(),
        borderColor:window.chartColors.green,
        fill: false,
        data: task_time_value
    };
user_data[0] = task_array;
for(var j=0; j<Object.keys(user_value).length; j++)
{
    user_labels = Object.keys(user_value)[j];
    for(var k=0; k<Object.values(user_value)[0].length; k++)
    {
        user_time_value.push(Object.values(user_value)[0][k][1]/60);
    }
    chart_color= parseInt(chart_color)+123456;
    var inner_array = { 
        type: 'line',
        label: user_labels,
        backgroundColor: '#'+chart_color,
        borderColor:window.chartColors.blue,
        fill: false,
        data: user_time_value,
        };
        user_time_value = [0];
       user_data[j+1] = inner_array;
}

var chart_values = {
    labels: task_labels,
    datasets: user_data
};
var configs = {
    type: 'bar',
    data: chart_values,
    options: {
        title: {
            text: 'User snapshot',
        },
        hover: {
                enabled: false
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
                    labelString: 'Time in hours',
                }
            }]
        },
    }
};
new Chart(user_chart, configs);
}
}
$(document).ready(function() {
    //var flag = 0;
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
        data: { 'type': "get_user" },
        success: function(res) {
            var result = JSON.parse(res);
            usernames = result['result'];
            console.log(usernames.length)
            for (var j = 0; j < usernames.length; j++) {
                if((usernames[j]["project_name"] != undefined) && (usernames[j]["project_name"] != null))
                {
                    console.log(usernames[j]["project_name"]);
                    var option = $('<option>' + usernames[j]["project_name"] + '</option>');
                    $('.project-names').append(option);
                }
                }
                var p_name = document.getElementById('project-list').value;
                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
                    data: { 'project_name': p_name },
                    success: function(res) {
                        var result = JSON.parse(res);
                        __draw_user_chart(result);
                    }    
                });
            }
    	});
    $('#project-list').change(function() {
        var p_name = document.getElementById('project-list').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': p_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_user_chart(result);
            }
        });
	});
});
