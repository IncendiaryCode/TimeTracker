var color = Chart.helpers.color;
		var config = {
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
					label: 'username 1',
					backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
					borderColor: window.chartColors.green,
					fill: false,
					data: [5,1,2,4,1,2],
				},{
					type: 'line',
					label: 'username 2',
					backgroundColor: "red",
					borderColor: window.chartColors.orange,
					fill: false,
					data: [4,2,5,0,3,5],
				},{
					type: 'line',
					label: 'username 3',
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

		window.onload = function() {
			var ctx = document.getElementById('user-chart').getContext('2d');
			window.myLine = new Chart(ctx, config);

		};