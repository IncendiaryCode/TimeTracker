function __draw_project_chart(res) {
	var result = res["data"];
	var project_chart = document
		.getElementById("project_time_chart")
		.getContext("2d");
	gradient = project_chart.createLinearGradient(0, 0, 0, 600);
	gradient.addColorStop(0, "#7077ff");
	gradient.addColorStop(0.5, "#e485fb");
	gradient.addColorStop(1, "#e484fb");
	var color = Chart.helpers.color;
	var label = [];
	var hours = [];
	for (var i = 0; i < result.length; i++) {
		label[i] = result[i]["task_date"];
		hours[i] = result[i]["t_minutes"] / 60;
	}
	for (var ind = 0; ind < hours.length; ind++) {
		var task_time_dec = hours[ind] - Math.floor(hours[ind]);
		task_time_dec = task_time_dec.toString().slice(0, 4);
		var total_time = Math.floor(hours[ind]) + parseFloat(task_time_dec);
		hours[ind] = total_time;
	}
	var configs = {
		type: "line",
		data: {
			labels: label,
			datasets: [
				{
					type: "line",
					label: "time spent in hrs",
					backgroundColor: gradient,
					borderColor: window.chartColors.black,
					data: hours
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
						return ("time spent in hrs "+tooltipItem['value'].split('.')[0]+':'+minutes);
					}
				}
			},
			title: {
				text: "task snapshot"
			},
			legend: {
				display: false
			},
			hover: {
				display: false
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
						}
					}
				],
				yAxes: [
					{
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
							labelString: "time in hours"
						}
					}
				]
			}
		}
	};
	/*        if (projectChart) projectChart.destroy();*/
	projectChart = new Chart(project_chart, configs);
}

$(document).ready(function() {
	if (document.getElementById("project_id")) {
		var project_id = document.getElementById("project_id").value;

		$.ajax({
			type: "POST",
			url: timeTrackerBaseURL + "index.php/admin/user_chart",
			data: { type: "project_chart", project_id: project_id },
			success: function(res) {
				var result = JSON.parse(res);
				__draw_project_chart(result);
			}
		});

		$("#project-list-datatable").DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: timeTrackerBaseURL + "index.php/admin/user_project_table",
				type: "POST",
				data: { type: "project_user", project_id: project_id },
				dataSrc: function(json) {
					//Make your callback here.
					if (json["status"] == false) {
						document.getElementById("project-datatabel-error").innerHTML =
							"No results found";
						$('#project-list-datatable_processing').hide();
					} else {
						document.getElementById("project-datatabel-error").innerHTML = " ";
						$('#project-list-datatable_processing').show();
					}
					return json.data;
				}
			},
			order: [[0, "asc"]],
			columnDefs: [
				{
					targets: 0,
					render: function(data, type, row, meta) {
						return row[0];
					}
				},
				{
					targets: 1,
					render: function(data, type, row, meta) {
						return row[1];
					}
				},
				{
					targets: 2,
					render: function(data, type, row, meta) {
						return row[2];
					}
				}
			]
		});

		$("#task-list-datatable").DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: timeTrackerBaseURL + "index.php/admin/user_task_table",
				type: "POST",
				data: { type: "project_task", project_id: project_id },
				dataSrc: function(json) {
					//Make your callback here.
					if (json["status"] == false) {
						document.getElementById("task-datatabel-error").innerHTML =
							"No results found";
						$('#task-list-datatable_processing').hide();
					} else {
						document.getElementById("task-datatabel-error").innerHTML = " ";
					}
					return json.data;
				}
			},
			order: [[0, "asc"]],
			columnDefs: [
				{
					targets: 0,
					render: function(data, type, row, meta) {
						return row[0];
					}
				},
				{
					targets: 1,
					render: function(data, type, row, meta) {
						return row[1];
					}
				},
				{
					targets: 2,
					render: function(data, type, row, meta) {
						return row[2];
					}
				}
			]
		});

		var search = document.getElementById("project-list-datatable_filter")
			.childNodes[0]["control"];
		var att = document.createAttribute("class");
		att.value = "border";
		search.setAttributeNode(att);

		var search_task = document.getElementById("task-list-datatable_filter")
			.childNodes[0]["control"];
		var att_task = document.createAttribute("class");
		att_task.value = "border";
		search_task.setAttributeNode(att_task);

		var adding_user = document.getElementById("adding-user");
		adding_user.onsubmit = function() {
			var user_name = document.getElementById("assigning-user-name").value;
			if (user_name == "select user") {
				document.getElementById("adding-user-error").innerHTML =
					"please enter user name";
				return false;
			}
		};
	}
});
