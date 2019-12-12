function __draw_task_chart(res)
{
var task_chart = document.getElementById('task-chart').getContext('2d');
var color = Chart.helpers.color;

		var configs = {
			type: 'bar',
			data: {
				labels: ['task1','task2','task3','task4','task5','task6'],
				datasets: [{
					type: 'bar',
					label: 'Time used',
					backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
					borderColor: window.chartColors.red,
					data: [5,10,15,12,4,5],
				}]
			},
			options: {
				title: {
					text: 'Chart.js Combo Time Scale'
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
		            var option = $('<option>' + usernames[j]["name"] + '</option>');
		            $('.project-list').append(option);
		        	}
		        }
        });
	var project_name = document.getElementById('total-project').value;

        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': project_name },
            success: function(res) {
            	var result = JSON.parse(res);
            	__draw_task_chart(res);
            }    
        });
    $('#project-list').click(function() {
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': project_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_task_chart(res);
            }
        });
	});
})