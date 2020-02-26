var taskChart;
var table;
function __draw_task_chart(res) {
	var result = res['result'];
	var task_chart = document.getElementById('task-chart').getContext('2d');
	gradient = task_chart.createLinearGradient(0, 0, 0, 600);
	gradient.addColorStop(0, '#7077ff');
	gradient.addColorStop(0.5, '#e485fb');
	gradient.addColorStop(1, '#e484fb');
	var color = Chart.helpers.color;
	var label = [];
	var count = [];
	if (res['status'] == false) {
		document.getElementById('task-chart-error').innerHTML = 'This project does not have any data.';
		$('#task-chart').hide();
	} else {
		$('#task-chart').show();
		document.getElementById('task-chart-error').innerHTML = ' ';
		for (var i = 0; i < result.length; i++) {
			label[i] = result[i]['task_date'];
			count[i] = result[i]['tasks_count'];
		}
		var configs = {
			type: 'line',
			data: {
				labels: label,
				datasets: [
					{
						type: 'line',
						label: 'total tasks',
						backgroundColor: gradient,
						borderColor: window.chartColors.black,
						data: count
					}
				]
			},
			options: {
				title: {
					text: 'task snapshot'
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
								labelString: 'task count'
							}
						}
					]
				}
			}
		};
		if (taskChart) taskChart.destroy();
		taskChart = new Chart(task_chart, configs);
	}
}

function callTaskTableData(start_date, end_date, project, user) {
	//$('#task-lists-datatable').empty();
	table = $('#task-lists-datatable')
		.DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			scrollX: true,
			bDestroy: true,
			ajax: {
				url: timeTrackerBaseURL + 'index.php/admin/load_snapshot',
				type: 'POST',
				data: { "type": 'task',"start_date": start_date, "end_date": end_date, "project_id":project, "user_id":user },
				dataSrc: function(json) {
					//Make your callback here.
					if (json['status'] == false) {
						$('#task-lists-datatable').empty();
						document.getElementById('task-tabel-error').innerHTML = 'No results found';
					} else {
						document.getElementById('task-tabel-error').innerHTML = ' ';
					}
					return json.data;
				}
			},
			order: [ [ 4, 'desc' ] ],
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
					},
					orderable: false
				},
				{
					targets: 2,
					render: function(data, type, row, meta) {
						return '<a href="../admin/load_project_detail?project_id=' + row[7] + '">' + row[2] + '</a>';
					}
				},
				{
					targets: 3,
					render: function(data, type, row, meta) {
						return row[8];
					}
				},
				{
					targets: 4,
					render: function(data, type, row, meta) {
						return row[3];
					}
				},
				{
					targets: 5,
					render: function(data, type, row, meta) {
						return row[4];
					}
				},
				{
					targets: 6,
					render: function(data, type, row, meta) {
						return row[5];
					}
				},
				{
					targets: 7,
					render: function(data, type, row, meta) {
						return "<a href='#' data-id='" + row[6] + "' class='text-danger delete-task' data-toggle='modal' data-target='#delete-task-modal'><i class='fas fa-trash-alt icon-plus del-tasks'></i></a>";
					},
					orderable: false
				}
			]
		})
		.on('init.dt', function() {
			$('.delete-task').click(function() {
				var task_id = this.getAttribute('data-id');
				$('#delete-task').click(function() {
					$.ajax({
						type: 'POST',
						url: timeTrackerBaseURL + 'index.php/admin/delete_data',
						data: { task_id: task_id },
						success: function(res) {
							var result = JSON.parse(res);
							window.location.reload();
						}
					});
				});
			});
		});
	var filteredData = table.columns([ 0, 1 ]).data().flatten().filter(function(value, index) {
		return value > 20 ? true : false;
	});
}
$(document).ready(function() {
	//rendering datatable
	//initialize date picker
	var dashPrjDtPicker1 = $('#curr-month').datepicker({
		minViewMode: 1,
		todayHighlight: true
	});
	if (document.getElementById('task-chart')) {
		if (document.getElementById('curr-month').value == '' || document.getElementById('curr-month').value == ' ') {
			var month_no = (new Date().getMonth() + 1).toString();
			if (month_no == 1) {
				month_no = '0' + month_no;
			}
			var curr_month = new Date().getFullYear().toString() + '-' + month_no;
			document.getElementById('curr-month').value = curr_month;
		}
		document.getElementById('curr-month').value;
		$.ajax({
			type: 'POST',
			url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
			data: { month: document.getElementById('curr-month').value },
			success: function(res) {
				if (JSON.parse(res)['status'] == false) {
					document.getElementById('task-chart-error').innerHTML = 'No data available';
					$('#task-chart').hide();
				} else {
					var result = JSON.parse(res);
					$('#task-chart').show();
					__draw_task_chart(result);
				}
			}
		});
		callTaskTableData();
		$('#curr-month').change(function() {
			if (document.getElementById('curr-month').value != '') {
				$.ajax({
					type: 'POST',
					url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
					data: { month: document.getElementById('curr-month').value },
					success: function(res) {
						if (JSON.parse(res)['status'] == false) {
							document.getElementById('task-chart-error').innerHTML = 'No data available';
							$('#task-chart').hide();
						} else {
							var result = JSON.parse(res);
							$('#task-chart').show();
							__draw_task_chart(result);
						}
					}
				});
				//callTaskTableData();
			}
		});

		var search = document.getElementById('task-lists-datatable_filter').childNodes[0]['control'];
		var att = document.createAttribute('class');
		att.value = 'border';
		search.setAttributeNode(att);
	}

	$('.datepicker').datepicker({
		showOn: 'button',
		buttonImage: 'assets/images/calendar.gif',
		buttonImageOnly: true,
		autoclose: true,
		format: 'yyyy-mm-dd'
	});

    $('.clear-filter').click(function(e)
    {
        e.preventDefault();
        document.getElementById('dateStart').value = "";
        document.getElementById('dateEnd').value = "";
        document.getElementById('select-prt').value = "Select project";
        document.getElementById('select-user').value = "Select user";
        $(this).hide();
        callTaskTableData();
    });
    $('#task-snapshot-filter').click(function(e)
    {
        e.preventDefault();
        var start_date = document.getElementById('dateStart').value;
        var end_date = document.getElementById('dateEnd').value;
		var project = "";
		var user = "";
		if((end_date == " "|| end_date == "") && (start_date != ""))
		{
			document.getElementById('dateEnd').value = moment().format('YYYY-MM-DD');
		}
		if(start_date == " "|| start_date == "" && (end_date != ""))
		{
			document.getElementById('task-filter-error').innerHTML = "Please enter start date";
		}
		else{
						if(document.getElementById('select-prt').value != "Select project")
			{   
				project = document.getElementById('select-prt').value;
			}
			if(document.getElementById('select-user').value != "Select user")
			{
				user = document.getElementById('select-user').value;
			}
			if((start_date == '' || start_date == '') && (end_date== ''|| end_date== '') && (project =='') && (user==''))
			{
				document.getElementById('task-filter-error').innerHTML = "Apply some filters..";
			}else{
				callTaskTableData(start_date,end_date,project,user);
				document.getElementById('task-filter-error').innerHTML = " ";
				$('.clear-filter').show();
			}
		}
    });
});