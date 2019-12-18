var myChart;
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

    var task_value = res['result'][0];
    var user_value = res['result'][1];

    var chart_color = "000000";

for(var i=0; i<task_value.length; i++)
{
task_labels[i] = task_value[i]['task_name'];
task_time_value[i] = task_value[i]['time_used']/60;
}
var user_data =  [];

for(var ind=0; ind<task_time_value.length; ind++)
{
    var task_time_dec = task_time_value[ind] - Math.floor(task_time_value[ind]);
    task_time_dec = task_time_dec.toString().slice(0,4);
    var total_time = Math.floor(task_time_value[ind]) + parseFloat(task_time_dec);
    task_time_value[ind] = total_time;
}


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

    for(var k=0; k<Object.values(user_value)[j].length; k++)
    {
        for(var e=0; e<task_labels.length; e++)
        {
        if(Object.values(user_value)[j][k][0] == task_labels[e])
        {
        user_time_value[e] = Object.values(user_value)[j][k][1]/60;
        }
        }
    }
    for(var index=0; index<user_time_value.length; index++)
    {
        if(user_time_value[index] == undefined)
        {
            user_time_value[index] = 0;
        }
    }


    for(var ind=0; ind<user_time_value.length; ind++)
    {
        var task_time_dec = user_time_value[ind] - Math.floor(user_time_value[ind]);
        task_time_dec = task_time_dec.toString().slice(0,4);
        var total_time = Math.floor(user_time_value[ind]) + parseFloat(task_time_dec);
        user_time_value[ind] = total_time;
    }

    chart_color= parseInt(chart_color)+123456;
    var inner_array = { 
        type: 'line',
        label: user_labels,
        backgroundColor: '#'+chart_color,
        borderColor: '#'+chart_color,
        fill: false,
        data: user_time_value,
        };
       user_data[j+1] = inner_array;
       user_time_value = [0];
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
                mode: "nearest"
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
if (myChart) myChart.destroy();
 myChart = new Chart(user_chart, configs);
}
}
$(document).ready(function() {
    if(document.getElementById('user-chart'))
    {
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
        data: { 'type': "get_user" },
        success: function(res) {
            var result = JSON.parse(res);
            usernames = result['result'];
            for (var j = 0; j < usernames.length; j++) {
                if((usernames[j]["project_name"] != undefined) && (usernames[j]["project_name"] != null))
                {
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
    }
    $('#project-list').change(function() {
        var p_name = document.getElementById('project-list').value;
        //call for list of  project data on change of project name.
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

     $('.icon-remove').click(function()
    {
        $(this.parentNode.parentNode.parentNode.parentNode).remove();
        $(this.parentNode.parentNode.parentNode.childNodes).remove();
        // delete call
        /*$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': p_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_user_chart(result);
                window.location.reload();
            }
        });*/
    });


});