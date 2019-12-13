function __draw_task_chart(res)
{
var task_chart = document.getElementById('task-chart').getContext('2d');
var color = Chart.helpers.color;
var label = [];
var time = [];
var data = res['result'][0];

for(var i=0;i<data.length; i++)
{
label[i] = data[i]['task_name'];
time[i] = data[i]['time_used']/60;
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
		new Chart(task_chart, configs);
	}

$(document).ready(function()
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
	
    $('#total-project').change(function() {
        var project_name = document.getElementById('total-project').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': project_name },
            success: function(res) {
                var result = JSON.parse(res);
                console.log(result)
                __draw_task_chart(result);
            }
        });
	});
})