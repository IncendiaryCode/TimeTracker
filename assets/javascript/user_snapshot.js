var user_chart = document.getElementById('user-chart').getContext('2d');
var color = Chart.helpers.color;

	var configs = {
		type: 'bar',
		data: {
			labels: ['task1','task2','task3','task4','task5','task6'],
			datasets: [{
				type: 'bar',
				label: 'Dataset 1',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				data: [5,10,15,12,4,5],
			}, {
				type: 'line',
				label: 'user 1',
				backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
				borderColor: window.chartColors.green,
				fill: false,
				data: [5,1,2,4,1,2],
			},{
				type: 'line',
				label: 'user 2',
				backgroundColor: "red",
				borderColor: window.chartColors.orange,
				fill: false,
				data: [4,2,5,0,3,5],
			},{
				type: 'line',
				label: 'user 3',
				backgroundColor: "red",
				borderColor: window.chartColors.blue,
				fill: false,
				data: [0,1,2,5,6,2],
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

$(document).ready(function()
{
	$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
            data: { 'type': "get_user" },
            success: function(res) {
                var result = JSON.parse(res);
                console.log(result);
                usernames = result['result'];
		        for (var j = 0; j < usernames.length; j++) {
		            var option = $('<option>' + usernames[j]["name"] + '</option>');
		            $('.project-list').append(option);
		            new Chart(user_chart, configs);
		        }
		        }
        });
})