var project_chart = document.getElementById('project-chart');

var chartColors = window.chartColors;
		var color = Chart.helpers.color;
		var config = {
			data: {
				datasets: [{
					data: [100,20,50,25,45],
					backgroundColor: [
						color(chartColors.red).alpha(0.5).rgbString(),
						color(chartColors.black).alpha(0.5).rgbString(),
						color(chartColors.yellow).alpha(0.5).rgbString(),
						color(chartColors.green).alpha(0.5).rgbString(),
						color(chartColors.blue).alpha(0.5).rgbString(),
					],
					label: 'My dataset' // for legend
				}],
				labels: ['project1','project2','project3','project4','project5']
			},
			options: {
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'Chart.js Polar Area Chart'
				},
				scale: {
					reverse: false
				},
				animation: {
					animateRotate: false,
					animateScale: true
				}
			}
		};

		window.onload = function() {
			window.myPolarArea = Chart.PolarArea(project_chart, config);
		};