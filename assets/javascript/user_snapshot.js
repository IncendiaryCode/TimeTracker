var user_detail_chart;
function __draw_user_chart(res) {
	var user_chart = document.getElementById('user-chart').getContext('2d');
	gradient = user_chart.createLinearGradient(0, 0, 0, 300);
	gradient.addColorStop(0, '#4b5bf0');
	gradient.addColorStop(1, '#ea4776');
	if (res['status'] == false) {
		document.getElementById('user-chart-error').innerHTML = 'No data availbale.';

		$('#user_chart').hide();
	} else {
		$('#user_chart').show();
		document.getElementById('user-chart-error').innerHTML = ' ';
		var task_labels = [];
		var task_time_value = [];
		for (var i = 0; i < res['result'].length; i++) {
			task_labels[i] = res['result'][i]['user_name'];
			task_time_value[i] = res['result'][i]['time_used'] / 60;
		}

		for (var ind = 0; ind < task_time_value.length; ind++) {
			var task_time_dec = task_time_value[ind] - Math.floor(task_time_value[ind]);
			task_time_dec = task_time_dec.toString().slice(0, 4);
			var total_time = Math.floor(task_time_value[ind]) + parseFloat(task_time_dec);
			task_time_value[ind] = total_time;
		}

		var configs = {
			type: 'bar',
			data: {
				labels: task_labels,
				datasets: [
					{
						label: 'users chart',
						backgroundColor: gradient,
						borderColor: window.chartColors.green,
						fill: false,
						data: task_time_value
					}
				]
			},
			options: {
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data) {
							var label = data.datasets[tooltipItem.datasetIndex].label || '';
							var value = "";
							if((tooltipItem['value'].split('.')[1]/100*60).toString() != "NaN")
							{
							value = tooltipItem['value'].split('.')[1]/100*60;
							}
							if (label) {
								label.split('.')[0] += ':'+value;
							}
							var minutes = parseInt(value);
							if(parseInt(value).toString() == "NaN")
							{
								minutes = 0;
							}
							if(minutes.toString().length == 1)
							{
								minutes = '0'+minutes;
							}
							minutes = minutes.toString().slice(0,2);
							$('#user-chart').click(function() {
								var elmnt = document.getElementById(tooltipItem.xLabel);
								elmnt.scrollIntoView({ behavior: 'smooth' });
							});

							return ("time spent in hrs "+tooltipItem['value'].split('.')[0]+':'+minutes);
						}
					}
				},
				title: {
					text: 'User snapshot'
				},
				legend: {
					display: false
				},
				hover: {
					mode: 'nearest'
				},
				scales: {
					xAxes: [
						{
							gridLines: {
								display: false,
								beginAtZero: true
							},
							ticks: {
								display: true,
								beginAtZero: true,
								stacked: true
							},
							scaleLabel: {
								display: true
							}
						}
					],
					yAxes: [
						{
							gridLines: {
								display: true,
								drawBorder: true
							},
							ticks: {
								display: true,
								beginAtZero: true,
								stacked: true
							},
							scaleLabel: {
								display: true,
								labelString: 'Time in hours'
							}
						}
					]
				}
			}
		}; 
		if (user_detail_chart) user_detail_chart.destroy();
		user_detail_chart = new Chart(user_chart, configs);
	}
}

$(document).ready(function() {
	if (document.getElementById('user-chart')) {
		$.ajax({
			type: 'POST',
			url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
			success: function(res) {
				var result = JSON.parse(res);
				__draw_user_chart(result);
			}
		});
	}

	$('#project-list').change(function() {
		var p_name = document.getElementById('project-list').value;
		var form_graph_data = '';
        if (p_name != 'All projects') form_graph_data = { project_name: p_name };
        if (user_detail_chart) user_detail_chart.destroy();
		//call for list of  project data on change of project name.
		$.ajax({
			type: 'POST',
			url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
			data: form_graph_data,
			success: function(res) {
				var result = JSON.parse(res);
				__draw_user_chart(result);
			}
		});
	});

	$('.icon-remove').click(function() {
		var user_id = this.childNodes[0].value;
		$('#delete-user').unbind().click(function() // delete call
		{
			$.ajax({
				type: 'POST',
				data: { user_id: user_id },
				url: timeTrackerBaseURL + 'index.php/admin/delete_data',
				success: function(res) {
					var result = JSON.parse(res);
					window.location.reload();
				}
			});
		});
	});
});
