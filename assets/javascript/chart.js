var projectChart;
function __project_details(res) {
	if(document.getElementById("main-chart"))
	{
		
	var ctx = document.getElementById("main-chart").getContext("2d");
	gradient = ctx.createLinearGradient(0, 0, 0, 600);
	gradient.addColorStop(0, "#7077ff");
	gradient.addColorStop(0.5, "#e485fb");
	gradient.addColorStop(1, "#e484fb");
	var chartData = [];
	for (var i = 0; i < res["datasets"].length; i++) {
		chartData[i] = res["datasets"][i];
	}

	var config = {
		type: "line",
		data: {
			labels: res["labels"],
			datasets: chartData
		},
		options: {
			responsive: true,
			title: {
				display: true
			},
			tooltip: {
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
						},
						scaleLabel: {
							display: true,
							labelString: "days"
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
							labelString: "Total time in hrs"
						}
					}
				]
			}
		}
	};	


	if (projectChart) projectChart.destroy();
	projectChart = new Chart(ctx, config);
}
}
$(document).ready(function() {
	var loadDashProjectChart = function(dateStr) {
		var dateObj = dateStr;
		if (typeof dateStr === "undefined") {
			dateObj = moment().format("YYYY-MM");
		}

		$.ajax({
			type: "POST",
			url: timeTrackerBaseURL + "index.php/admin/get_project_list",
			data: { type: "get_graph_data", month: dateObj },
			success: function(res) {
				var result = JSON.parse(res);
				usernames = result["result"];
				__project_details(usernames);
			}
		});
	};

	//default load current month
	loadDashProjectChart();
	//initialize date picker
	var dashPrjDtPicker = $('#dash-prj-dtpicker .input-group.date').datepicker({
		minViewMode: 1,
		autoclose: true,
		format: "MM yyyy"
	});

	//change event
	dashPrjDtPicker.datepicker().on("changeMonth", function(e) {
		loadDashProjectChart(moment(e.date).format("Y-MM"));
	});

	$("#update-prj-dtpicker").keydown(false);
});
