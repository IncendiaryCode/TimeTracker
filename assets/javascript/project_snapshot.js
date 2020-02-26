function drawProjectChart() {
	var result;
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
		data : {"type":"get_project_graph"},
		success: function(res) {
			if (JSON.parse(res)['status'] == false) {
				$('#chart_div').hide();
				document.getElementById('project-snap-error').innerHTML = 'No data available';
			} else {
				result = JSON.parse(res);
				var total_time = 0;
				for (var time = 0; time < result['result'].length; time++) {
					if (result['result'][time]['t_minutes'] != null) {
						total_time = total_time + parseInt(result['result'][time]['t_minutes']);
					}
				}
				result = result['result'];
				var x = 10;
				var y = 10;
				var data = google.visualization.arrayToDataTable([ [ 'ID', 'Time spent in hrs', 'Percentage of time spent', 'radius', 'Time spent' ], [ '', 0, 0, 0, 0 ] ]);
				for (var i = 0; i < result.length; i++) {
					if (result[i]['project_name'] != null) {
						x = result[i]['t_minutes'] / 60;
						y = result[i]['t_minutes'] / 60 / (total_time / 60) * 100;
						r = result[i]['t_minutes'] / 500;
						var value = { c: [ { v: result[i]['project_name'] }, { v: x }, { v: y }, { v: r }, { v: result[i]['t_minutes'] / 60 } ] };
					}
					data['eg'][i + 1] = value;
				}
				var options = {
					colorAxis: { colors: [ 'yellow', 'red' ] },
					hAxis: { title: 'Time spent in hrs' },
					vAxis: { title: 'Percentage of time spent' }
				};

				var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
				chart.draw(data, options);
			}
		}
	});
}

$(document).ready(function() {
	if (document.getElementById('chart_div')) {
		google.charts.load('current', { packages: [ 'corechart' ] });
		google.setOnLoadCallback(drawProjectChart);
	}
});
