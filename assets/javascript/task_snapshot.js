var taskChart;
function __draw_task_chart(res)
{
var task_chart = document.getElementById('task-chart').getContext('2d');
var color = Chart.helpers.color;
var label = [];
var time = [];
if(res['status'] == false)
{
    document.getElementById('task-chart-error').innerHTML = "This project does not have any data.";
    $('#task-chart').hide();
}
else{
$('#task-chart').show();
document.getElementById('task-chart-error').innerHTML = " ";

var data = res['result'][0];

for(var i=0;i<data.length; i++)
{
label[i] = data[i]['task_name'];
time[i] = data[i]['time_used']/60;
}

for(var ind=0; ind<time.length; ind++)
    {
        var task_time_dec = time[ind] - Math.floor(time[ind]);
        task_time_dec = task_time_dec.toString().slice(0,4);
        var total_time = Math.floor(time[ind]) + parseFloat(task_time_dec);
        time[ind] = total_time;
    }
	var configs = {
		type: 'bar',
		data: {
			labels: label,
			datasets: [{
				type: 'bar',
				label: 'Time used',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				data: time,
			}]
		},
		options: {
			title: {
				text: 'Task snapshot'
			},hover: {
                display: false
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
                        labelString: 'Time in hours',
                    }
                }]
            },
		}
	};
    if(taskChart) taskChart.destroy();
	taskChart = new Chart(task_chart, configs);
	}
}

$(document).ready(function()
{
var dataSet = [];
$.ajax({
type: 'POST',
url: timeTrackerBaseURL + 'index.php/admin/load_snapshot',
data: { 'type': "task" },
success: function(res) {
    var result = JSON.parse(res);
    result = result['data']
    for(var i=0; i<result.length; i++)
    {

        var task_time = (result[i]['total_minutes']/60) - (Math.floor(result[i]['total_minutes']/60));
        task_time = task_time.toString().slice(0,4);
        var total_task_time = Math.floor(result[i]['total_minutes']/60) + parseFloat(task_time);

        var element = [result[i]['task_name'],result[i]['description'],
        result[i]['start_time'],
        result[i]['end_time'],
        total_task_time,
        "<i class='fas fa-trash-alt icon-plus remove-tasks text-danger' data-toggle='modal' data-target='#delete-task'><input type='hidden' class='task_id' value='"+result[i]['task_id']+"'></i>" ];
        dataSet[i] = element;
    }
        $('#example').DataTable( {
        data: dataSet,
        columns: [
            { title: "Task name" },
            { title: "Description" },
            { title: "Start date" },
            { title: "End date" },
            { title: "Time spent" },
            { title: "Action" }
        ]
    } );
}
});


$(".remove-tasks").click(function()
{
console.log("dgsjhfs"); 
var task_id = this.childNodes[0].value;
$("#delete-task").click(function()
    {
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/delete_data',
        data: { 'task_id': task_id },
            success: function(res) {
                var result = JSON.parse(res);
                window.location.reload();
            }
        });
    });
});


if(document.getElementById('task-chart'))
{
$.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
        data: { 'type': "get_user" },
        success: function(res) {
			var result = JSON.parse(res);
            usernames = result['result'];
	        for (var j = 0; j < usernames.length; j++) {
                if(usernames[j]["project_name"] != null)
                {
	            var option = $('<option>' + usernames[j]["project_name"] + '</option>');
	            $('.project-name-list').append(option);
	        	}
            }
            var project_name = document.getElementById('total-project').value;
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
                data: { 'project_name': project_name },
                success: function(res) {
                    var result = JSON.parse(res);
                    __draw_task_chart(result);
                }    
            });
        }
    });
}

$('#total-project').change(function() {
    var project_name = document.getElementById('total-project').value;
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
        data: { 'project_name': project_name },
        success: function(res) {
            var result = JSON.parse(res);
            __draw_task_chart(result);
            }
        });
    });
});