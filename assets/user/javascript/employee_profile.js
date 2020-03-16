var user_profile_chart;
var table;
function __draw_profile_chart(res) {
	var user_chart = document.getElementById('user_prof_chart').getContext('2d');
	gradient = user_chart.createLinearGradient(0, 0, 0, 300);

	gradient.addColorStop(0, '#4b5bf0');
	gradient.addColorStop(1, '#ea4776');
	var data = JSON.parse(res);
	if (data['status'] == false) {
		$('#user_prof_chart').hide();
		$('.profile-chart-nodata').show();
		document.getElementById('profile-chart-error').innerHTML = 'No work is done in this period';
	} else {
		$('.profile-chart-nodata').hide();
		document.getElementById('profile-chart-error').innerHTML = ' ';
		var configs = {
			type: 'bar',
			data: {
				labels: [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
				datasets: [
					{
						label: 'time spent',
						backgroundColor: '#e485fb',
						borderColor: window.chartColors.green,
						fill: true,
						data: data['res']
					}
				]
			},
			options: {
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data) {
							return data['datasets'][0].label + ' ' + tooltipItem.yLabel + ' hrs';
						}
					}
				},
				title: {
					text: 'User profile'
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
		if (user_profile_chart) user_profile_chart.destroy();
		user_profile_chart = new Chart(user_chart, configs);
	}
}

function load_year_chart() {
	var year = document.getElementById('year-chart').value;
	if (year == '' || year == ' ' || year == null) {
		var cur_year = parseInt(new Date().toString().slice(10, 15));
		document.getElementById('year-chart').value = cur_year;
		// document.getElementById('chart-of-year').innerText = cur_year;
		year = cur_year;
		document.getElementById('year-chart').setAttribute('max', cur_year);
	}
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/user_chart',
		data: { date: year },
		success: function(res) {
			__draw_profile_chart(res);
		}
	});
}

function callLoginTableData(start_date, end_date) {
	table = $('#login-lists-datatable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		bDestroy: true,
		"searching": false,
		ajax: {
			url: timeTrackerBaseURL + 'index.php/user/user_login_data',
			type: 'POST',
			data: { from: start_date, to: end_date },
			dataSrc: function(res) {
				if (res['status'] == false) {
					$('#login-lists-datatable').empty();
					document.getElementById('login-tabel-error').innerHTML = 'No results found';
				} else {
					document.getElementById('login-tabel-error').innerHTML = ' ';
				}
				return res.log_data;
			}
		},
		order: [ [ 0, 'desc' ] ],
		columnDefs: [
			{
				targets: 0,
				render: function(data, type, row, meta) {
					return row['login_date'];
				}
			},
			{
				targets: 1,
				render: function(data, type, row, meta) {
					return row['login_time'];
				}
			},
			{
				targets: 2,
				render: function(data, type, row, meta) {
					if (row['logout_time'] != '--') return row['logout_time'].split(' ')[1];
					else {
						return 'Not ended';
					}
				}
			},
			{
				targets: 3,
				render: function(data, type, row, meta) {
					return row['total_time'];
				}
			}
		]
	});
	var filteredData = table.columns([ 0, 1 ]).data().flatten().filter(function(value, index) {
		return value > 20 ? true : false;
	});
}

function validate_login_filters() {
	var check_dates = moment(document.getElementById('dateStart').value).isAfter(document.getElementById('dateEnd').value);
	var check_end_date = moment(document.getElementById('dateEnd').value).isAfter(moment());
	var check_start_date = moment(document.getElementById('dateStart').value).isAfter(moment());

	document.getElementById('dateEnd').value = moment().format('YYYY-MM-DD');
	document.getElementById('start-time-error').innerHTML = '';

	if (check_dates) {
		return 'Start date cannot be greater than end date';
	} else if (check_start_date) {
		return 'Start date cannot be greater than today';
	} else if (check_end_date) {
		return 'End date cannot be greater than today';
	} else {
		return true;
	}
}

$(document).ready(function() {
	$('#year-chart').change(function() {
		load_year_chart();
	});

	// data table for login_activtities
	callLoginTableData();
	$('#login-date-filter .input-group.date').datepicker({
		weekStart: 1,
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	if (document.getElementById('dateStart')) {
		$('#dateStart').change(function() {
			var validate_filter = validate_login_filters();
			if (validate_filter != true) {
				document.getElementById('start-time-error').innerHTML = validate_filter;
			} else {
				document.getElementById('start-time-error').innerHTML = '';
				callLoginTableData(document.getElementById('dateStart').value, document.getElementById('dateEnd').value);
			}
		});
		$('#dateEnd').change(function() {
			if (document.getElementById('dateStart').value == '') {
				document.getElementById('start-time-error').innerHTML = 'Please enter Start date';
			} else {
				var validate_filter_e = validate_login_filters();
				if (validate_filter_e != true) {
					document.getElementById('start-time-error').innerHTML = validate_filter_e;
				} else {
					document.getElementById('start-time-error').innerHTML = '';
					callLoginTableData(document.getElementById('dateStart').value, document.getElementById('dateEnd').value);
				}
			}
		});
	}

	var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if (document.getElementById('profile-error')) document.getElementById('profile-error').innerHTML = ' ';

	if (document.getElementById('year-chart')) {
		load_year_chart();
	}

	// Start upload preview image
	var $uploadCrop, tempFilename, rawImg, imageId, cropped_points;
	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('.upload-demo').addClass('ready');
				$('#cropImagePop').modal('show');
				rawImg = e.target.result;
			};
			reader.readAsDataURL(input.files[0]);
		} else {
			swal("Sorry - you're browser doesn't support the FileReader API");
		}
	}
	$uploadCrop = $('#upload-demo').croppie({
		viewport: {
			width: 200,
			height: 200,
			type: 'circle'
		},
		boundary: {
			width: 300,
			height: 300
		},
		enforceBoundary: false,
		enableExif: true
	});
	$('#cropImagePop').on('shown.bs.modal', function() {
		$uploadCrop
			.croppie('bind', {
				url: rawImg
			})
			.then(function() {});
	});

	$('.item-img').on('change', function() {
		imageId = $(this).data('id');
		tempFilename = $(this).val();
		$('#cancelCropBtn').data('id', imageId);
		readFile(this);
	});

	$('#cropImagePop').on('update.croppie', function(ev, cropData) {
		cropped_points = cropData['points'];
	});

	$('#cropImageBtn').on('click', function(ev) {
		$uploadCrop
			.croppie('result', {
				type: 'base64',
				format: 'jpeg',
				size: { width: 200, height: 200 }
			})
			.then(function(resp) {
				$('#item-img-output').attr('src', resp);
				$('#cropImagePop').modal('hide');
			});
	});

	if (document.getElementById('edit-profile') != undefined) {
		var profile = document.getElementById('edit-profile');
		profile.onsubmit = function() {
			var user_name = document.getElementById('profile-name').value;
			var user_ph = document.getElementById('profile-ph').value;
			if (user_name == ' ' || user_name == '') {
				document.getElementById('profile-error').innerHTML = 'Empty name';
				return false;
			}
			if (user_ph.length != 0) {
				if (user_ph.length != 10) {
					document.getElementById('profile-error').innerHTML = 'Invalid phone number';
					return false;
				}
			}
			document.getElementById('profile-error').innerHTML = ' ';
			document.getElementById('cropped-points').value = cropped_points;
			return true;
		};
	}
});
