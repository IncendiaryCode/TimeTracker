function __draw_user_chart(res)
{

var user_chart = document.getElementById('user-chart').getContext('2d');
var color = Chart.helpers.color;

var user_data = [];

var task_labels = [];
var user_labels = [];

var task_time_value = [];
var user_time_value = [];
var user_task_name = [];

var task_value = res['result'][0];
var user_value = res['result'][1];

console.log("user_value", user_value, Object.keys(user_value).length, Object.keys(user_value)[0], Object.values(user_value), Object.values(user_value)[0].length, );
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

/*for user*/
var c=0;
var cnt=0;
for(var j=0; j<Object.keys(user_value).length; j++)
{
    user_labels = Object.keys(user_value)[j];
    for(var k=0; k<Object.values(user_value)[0].length; k++)
    {
    console.log(Object.values(user_value)[0][k][1])
        user_time_value[k] = Object.values(user_value)[0][k][1];
    }

console.log(user_labels, user_time_value );
    var inner_array = { 
        type: 'line',
        //label: user_value[j][0],
        backgroundColor:color(window.chartColors.red).alpha(0.5).rgbString(),
        borderColor:window.chartColors.green,
        fill: false,
        data: /*user_value[j]['time_used']/60*/user_time_value[j],

        };
       user_data[j+1] = inner_array;
}

var chart_values = {
    labels: task_labels,
    datasets: user_data 
    /*{
        type: 'bar',
        label: 'Total tasks',
        backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
        borderColor: window.chartColors.red,
        data: task_time_value,
    }, {
        type: 'line',
        label: 'user 1',
        backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
        borderColor: window.chartColors.green,
        fill: false,
        data: [5, 100, 20, 40, 10, 20],
    }, {
        type: 'line',
        label: 'user 2',
        backgroundColor: "red",
        borderColor: window.chartColors.orange,
        fill: false,
        data: [40, 20, 50, 10, 300, 50],
    }, {
        type: 'line',
        label: 'user 3',
        backgroundColor: "red",
        borderColor: window.chartColors.blue,
        fill: false,
        data: [0, 11, 20, 25, 60, 20],
    }*/
};
var configs = {
    type: 'bar',
    data: chart_values,
    options: {
        title: {
            text: 'User snapshot'
        },
        hover: {
                mode: 'index'
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
