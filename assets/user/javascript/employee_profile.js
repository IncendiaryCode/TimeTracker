var user_profile_chart;
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

$(document).ready(function() {
	$('#year-chart').change(function() {
		load_year_chart();
	});
	var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if (document.getElementById('profile-error')) document.getElementById('profile-error').innerHTML = ' ';

	if (document.getElementById('year-chart')) {
		load_year_chart();
	}

	// Start upload preview image
	// TODO
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
			document.getElementById('croped-pointed').value = cropped_points;
			return true;
		};
	}
});
