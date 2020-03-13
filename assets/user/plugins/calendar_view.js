var panel_id = 0;
var graph_id = 0;
var weeklyOrMonthlyDuration = 0;
Date.prototype.getWeek = function() {
	var onejan = new Date(this.getFullYear(), 0, 1);
	return Math.ceil(((this - onejan) / 86400000 + onejan.getDay() + 1) / 7);
};

function minutesToTime(mins) {
	var hr = parseInt(mins) / 60;
	var min = parseInt(mins) % 60;
	if (min.toString().length == 1) {
		min = '0' + min;
	}
	min = min + 'h';
	return parseInt(hr) + ':' + min;
}

var weekly_chart;

function draw_chart_cards(data, type) {
	var timings = [];
	if (data['data'].length > 0) {
		var x = data['data'].length;
		var date = data['data'][0].start_time.split(' ')[0];
		for (var y = 0; y < x; y++) {
			var cardHeader = $('<div class="card-header card-header" />');
			var cardHeaderRow = $('<div class="row pt-2" />');
			var today = getTime();
			if (data['data'][y].start_time != null) {
				var task_date = data['data'][y].start_time.slice(0, 10);
				if (today != task_date) {
					$('.alert-box').show();
				}
			}
			if (data['data'][y].start_time == null) {
				cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>Not yet started.</div>');
			} else {
				var start_time = data['data'][y].start_time;
				var start_time_utc = moment.utc(start_time).toDate();
				var serverDate = moment(start_time_utc).format('YYYY-MM-DD hh:mm a');
				if (serverDate != 'Invalid date') {
					cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + serverDate + '</div>');
					
					$(cardHeaderRow[0].childNodes[0].childNodes[0]).css("color",data['data'][y].project_color);
					var __time_for_duration = moment(start_time_utc).format('YYYY-MM-DD HH:mm');
					if (date == data['data'][y].start_time.split(' ')[0]) {
						timings.push([ parseInt(__time_for_duration.split(' ')[1].split(':')[0]) * 60 + parseInt(__time_for_duration.split(' ')[1].split(':')[1]), data['data'][y].t_minutes ]);
					} else {
						store_and_calculate_duration(timings, type);
						date = data['data'][y].start_time.split(' ')[0];
						timings = [];
						timings.push([ parseInt(__time_for_duration.split(' ')[1].split(':')[0]) * 60 + parseInt(__time_for_duration.split(' ')[1].split(':')[1]), data['data'][y].t_minutes ]);
					}
				} else {
					cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + data['data'][y].start_time + '</div>');
				}
			}
			var stopCol = $('<div class="col-6 text-right"  id="btn-stop' + data['data'][y].id + '" />');
			
			if (data['data'][y].running_task == 0) {
				/*check whether task is ended or not*/
				var timeUsed = minutesToTime(data['data'][y].t_minutes);
				stopCol.append('<i class="far fa-clock"></i> ' + timeUsed);
			} else {
				var id = data['data'][y].id;
				if (data['data'][y].start_time != null) {
					var stopButton = $('<span class=""><i class="fa fa-hourglass-1"></i> Running</span>').data('taskid', data['data'][y].id);
				}
				stopCol.append(stopButton);
			}
			cardHeaderRow.append(stopCol);
			cardHeader.append(cardHeaderRow);

			var cardInner = $("<div class='card card-style-1 animated fadeIn' />");
			cardInner.append(cardHeader);

			var cardBody = $("<div class='card-body' />");
			cardBody.append(data['data'][y].task_name);
			cardInner.append(cardBody);
			var cardFooter = $("<div class='card-footer card-footer'>");
			var footerRow = $('<div class="row" />');

			footerRow.append((data['data'][y].image_name !== null ? "<div class='col-12'> <img src=" + data['data'][y].image_name + " width='20px;'> " : '') + data['data'][y].project + '</div>');

			var footerRight = $("<div class='card-actions' id='footer-right-" + data['data'][y].id + "'>");
			//action Edit
			var actionEdit = $('<a href="#" class=" pl-2  text-white " id="action-edit"><i class="fas fa-pencil-alt action-play " data-toggle="tooltip" data-placement="top" title="edit"></i></a>');
			actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_add_task?t_id=' + data['data'][y].id);
			footerRight.append(actionEdit);
			cardFooter.append(footerRow);
			cardInner.append(cardFooter);

			//add a overlay layer
			var cardOverlay = $("<div class='card-overlay' />");
			cardInner.append(cardOverlay);

			//add action overlay
			var cardActions = $("<div class='card-action-overlay' />");
			cardActions.append(footerRight);
			cardInner.append(cardActions);
			var cardCol = $("<div class='col-lg-6 mb-4 card-col animated ' id = card-count" + panel_id + ' />');
			cardCol.append(cardInner);

			$('#attachPanels').append(cardCol);
			$('.fa-pencil-alt').tooltip('enable');
			if (type == 'daily_chart') {
				panel_id++;
			}

			var id = data['data'][y].id;
			cardCol.click(function() {
				$('.print-chart-row' + id).addClass('animated zoomIn');
			});
			if (data['data'][y].running_task == 1 && data['data'][y].start_time != null) {
				//change background of current running task entries.
				document.getElementsByClassName('title').innerText += data['data'][y].task_name;
			}
		}

		if (type == 'daily_chart') {
			var duration = __calculate_duration(timings);
			var hr = parseInt(duration / 60);
			var min = duration % 60;
			if (hr.toString().length == 1) {
				hr = '0' + hr;
			}
			if (min.toString().length == 1) {
				min = '0' + min;
			}
			document.getElementById('daily-duration').innerHTML = hr + ':' + min;
			//console.log(hr + ':' + min);
		} else {
			store_and_calculate_duration(timings, type);
		}
	}
}

function store_and_calculate_duration(timings, type) {
	var time = __calculate_duration(timings);
	weeklyOrMonthlyDuration = weeklyOrMonthlyDuration + time;
}

function bubbleSort(arr) {
	var len = arr.length;
	for (var i = len - 1; i >= 0; i--) {
		for (var j = 1; j <= i; j++) {
			if (arr[j - 1][0] > arr[j][0]) {
				var temp = arr[j - 1][0];
				var temp1 = arr[j - 1][1];

				arr[j - 1][0] = arr[j][0];
				arr[j - 1][1] = arr[j][1];

				arr[j][0] = temp;
				arr[j][1] = temp1;
			}
		}
	}
	return arr;
}
function __calculate_duration(timeArr) {
	// var timings = bubbleSort(timeArr);
	var timings = timeArr.sort();
	var total_duration = 0;
	var store_Min_Temp = 0;
	var store_Start_Temp = 0;
	if (store_Min_Temp == 0) {
		store_Start_Temp = parseInt(timings[0][0]);
		store_Min_Temp = parseInt(timings[0][1]);
		total_duration = parseInt(store_Min_Temp);
	}

	for (var i = 1; i < timings.length; i++) {
		timings[i][0] = parseInt(timings[i][0]);
		timings[i][1] = parseInt(timings[i][1]);

		if (timings[i][0] == store_Start_Temp && timings[i][1] == store_Start_Temp + store_Min_Temp) {
			store_Start_Temp = parseInt(timings[i][0]);
			store_Min_Temp = parseInt(timings[i][1]);
			continue;
		} else if (timings[i][0] > store_Start_Temp && timings[i][0] < store_Start_Temp + store_Min_Temp && timings[i][0] + timings[i][1] > store_Start_Temp + store_Min_Temp) {
			// check for end time overlaped tasks
			total_duration = total_duration + (timings[i][0] + timings[i][1] - (store_Start_Temp + store_Min_Temp));
			store_Start_Temp = parseInt(timings[i][0]);
			store_Min_Temp = parseInt(timings[i][1]);
			continue;
		} else if (timings[i][0] < store_Start_Temp && timings[i][0] + timings[i][1] > store_Start_Temp + store_Min_Temp) {
			// check for start time overlaped tasks
			total_duration = total_duration + (store_Start_Temp - timings[i][0]);
			store_Start_Temp = parseInt(timings[i][0]);
			store_Min_Temp = parseInt(timings[i][1]);
			continue;
		} else if (store_Start_Temp < timings[i][0] && store_Start_Temp + store_Min_Temp > timings[i][0] + timings[i][1]) {
			// check for fully overlaped tasks
			continue;
		} else if (store_Start_Temp > timings[i][0] && store_Start_Temp + store_Min_Temp < timings[i][0] + timings[i]) {
			// check for middile of time overlaped tasks
			total_duration = total_duration + (timings[i][0] + timings[i][1]);
			store_Start_Temp = parseInt(timings[i][0]);
			store_Min_Temp = parseInt(timings[i][1]);
			continue;
		} else if ((timings[i][0] > store_Start_Temp + store_Min_Temp && timings[i][1] + timings[i][0] > store_Start_Temp + store_Min_Temp) || (timings[i][0] < store_Start_Temp && timings[i][1] + timings[i][0] < store_Start_Temp + store_Min_Temp)) {
			// check for not overlaped tasks
			total_duration = total_duration + parseInt(timings[i][1]);
			store_Start_Temp = parseInt(timings[i][0]);
			store_Min_Temp = parseInt(timings[i][1]);
			continue;
		} else {
			if (store_Start_Temp + store_Min_Temp == timings[i][0]) {
				// check for end time equal to start time tasks
				total_duration = total_duration + parseInt(timings[i][1]);
				store_Start_Temp = parseInt(timings[i][0]);
				store_Min_Temp = parseInt(timings[i][1]);
				continue;
			}
			continue;
		}
	}

	//flushing the array
	timings = [];
	return total_duration;
}
function loadTask(type, date, filterBy) {
	$('#attachPanels').empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/load_task_data',
		data: { chart_type: type, date: date, project_filter: JSON.stringify(filterBy) },
		success: function(values) {
			if (values == 'No activity in this date.') {
				$('#attachPanels').empty();
				$('#attachPanels').empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
			} else {
				var data = JSON.parse(values);
				$('#attachPanels').empty();
				draw_chart_cards(data, type);

				var duration = weeklyOrMonthlyDuration;
				var hr = parseInt(duration / 60);
				var min = duration % 60;
				if (hr.toString().length == 1) {
					hr = '0' + hr;
				}
				if (min.toString().length == 1) {
					min = '0' + min;
				}
				if (type == 'weekly_chart') document.getElementById('weekly-duration').innerHTML = hr + ':' + min;
				if (type == 'monthly_chart') document.getElementById('monthly-duration').innerHTML = hr + ':' + min;
			}
		}
	});
}

function loadDailyChart(filterBy) {
	var date = '';
	if (document.getElementById('daily-chart')) {
		date = document.getElementById('daily-chart').value;

		if (date == '' || date == ' ' || date == null) {
			var today = new Date();
			document.getElementById('daily-chart').value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);

			date = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
			document.getElementById('daily-chart').setAttribute('max', date);

			date = document.getElementById('daily-chart').value;

			retrieveChartData('daily_chart', date, filterBy);
		} else {
			retrieveChartData('daily_chart', date, filterBy);
		}
	}
}
function loadWeeklyChart(filterBy) {
	if (document.getElementById('weekly-chart')) {
		var weekControl = document.querySelector('input[type="week"]');
		var week = document.getElementById('weekly-chart').value;
		var day_range = '';
		if (document.getElementById('current-week')) {
			day_range = document.getElementById('current-week').innerHTML;

			var year = document.getElementById('week_y').innerHTML.split('-')[0];
			if (moment(year + day_range.split('-')[0]).format('MMM') == 'Dec' && parseInt(moment(year + day_range.split('-')[0]).format('DD')) > 24) {
				day_range = year + '-' + day_range.split(' ')[0] + '-' + day_range.split(' ')[1] + '~' + (parseInt(year) + 1) + '-' + day_range.split(' ')[3] + '-' + day_range.split(' ')[4];
			} else {
				day_range = year + '-' + day_range.split(' ')[0] + '-' + day_range.split(' ')[1] + '~' + year + '-' + day_range.split(' ')[3] + '-' + day_range.split(' ')[4];
			}
			retrieveChartData('weekly_chart', day_range, filterBy);
		}
	}
}

function drawChart(type, res, date) {
	if (res['status'] == false) {
		$('.no-activities').show();
		$('#attachPanels').hide();
		if (window.myBar != undefined) {
			window.myBar.destroy();
		}
		$('#weekly').css('height', '0px');
		document.getElementById('weekly-duration').innerHTML = '00:00';
		$('#attachPanels').empty();
	} else {
		$('.no-activities').hide();
		$('#attachPanels').show();
		$('#weekly').css('height', '400px');
		var weekly_hr = parseInt(res['total_minutes'] / 60);
		var weekly_min = res['total_minutes'] % 60;
		if (weekly_hr.toString().length == 1) {
			weekly_hr = '0' + weekly_hr;
		}
		if (weekly_min.toString().length == 1) {
			weekly_min = '0' + weekly_min;
		}

		document.getElementById('weekly-duration').innerHTML = weekly_hr + ':' + weekly_min;
		var const_lable = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];
		var data = {
			labels: const_lable,
			datasets: []
		};
		for (var n = 0; n < res['data'].length; n++) {
			var ind_set = {
				label: res['data'][n]['task_name'],
				backgroundColor: res['data'][n]['project_color'],
				data: []
			};
			for (var n1 = 0; n1 < res['data'][n]['time'].length; n1++) {
				ind_set.data.push(res['data'][n]['time'][n1]);
			}
			data.datasets.push(ind_set);
		}
		if (window.myBar != undefined) {
			window.myBar.destroy();
		}

		var ctx = document.getElementById('weekly').getContext('2d');
		window.myBar = new Chart(ctx, {
			type: 'bar',
			data: data,
			options: {
				events: [ 'click', 'mousemove' ],
				title: {
					display: false
				},
				legend: {
					display: false
				},
				tooltips: {
					mode: 'index',
					intersect: true,
					callbacks: {
						label: function(tooltipItem, data) {
							var label = data.datasets[tooltipItem.datasetIndex].label || '';
							if (data.datasets[tooltipItem.datasetIndex]['data'][myBar.getElementsAtEvent(event)[0]['_index']] == '00.00') return false;
							if (label.length > 40) {
								label = label.slice(0, 40) + '...';
							}
							return label + ' ' + data.datasets[tooltipItem.datasetIndex]['data'][myBar.getElementsAtEvent(event)[0]['_index']] + ' hrs';
						}
					}
				},
				onClick: function(event, data) {
					var month = getMonth(document.getElementById('current-week').innerHTML.split('-')[0].split(' ')[0]);
					if (month.toString().length == 1) {
						month = '0' + month;
					}
					var day = parseInt(document.getElementById('current-week').innerHTML.split('-')[0].split(' ')[1]) + parseInt(myBar.getElementsAtEvent(event)[0]['_index']);
					var year = document.getElementById('week_y');
					var d_time = moment(day + '-' + month + '-' + year.innerHTML.split('-')[0]);
					document.getElementById('current-date').innerHTML = moment(year.innerHTML.split('-')[0] + '-' + month + '-' + day).format('dddd MMMM DD');
					document.getElementById('daily-chart').value = year.innerHTML.split('-')[0] + '-' + month + '-' + day;
					if (document.getElementById('daily-chart').value != 'Invalid date') {
						$('#chart-navigation a[href="#daily-view"]').tab('show');
					}
				},
				responsive: true,
				scales: {
					xAxes: [
						{
							stacked: true,
							gridLines: {
								display: false,
								drawBorder: true
							}
						}
					],
					yAxes: [
						{
							ticks: {
								//stepSize:10,
								scaleStepWidth: 60
							},
							stacked: true,
							scaleLabel: {
								display: true,
								labelString: 'hrs'
							}
						}
					]
				}
			}
		});
	}
	// window.myBar = new Chart(ctx, window.myBar);
}
function retrieveChartData(type, date, filterBy) {
	$('#print-chart').empty();
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/activity_chart',
		data: { chart_type: type, date: date, project_filter: JSON.stringify(filterBy) },
		dataType: 'json',
		success: function(res) {
			if (res['msg'] == 'No activity in this date.') {
				if (type == 'weekly_chart') {
					document.getElementById('week-error').innerHTML = res['data'];
					$('#attachPanels').empty();
					$('#weekly').hide();
				}
				if (type == 'daily_chart') {
					document.getElementById('daily-duration').innerHTML = '00:00';
					document.getElementById('daily-error').innerHTML = res['data'];
					$('#attachPanels').empty();
				}
			} else {
				if (type == 'weekly_chart') {
					loadTask(type, date, filterBy);
					document.getElementById('week-error').innerHTML = ' ';
					drawChart(type, res, date);
					$('#weekly').show();
				}
				if (type == 'daily_chart') {
					loadTask(type, date, filterBy);
					document.getElementById('daily-error').innerHTML = ' ';
					draw_customized_chart(res);
				}
			}
		}
	});
}

function dateFromDay(year, day) {
	var date = new Date(year, 0); // initialize a date in year-01-01
	return new Date(date.setDate(day)); // add the number of days
}
function draw_customized_chart(res) {
	var pixel = [];
	var pixels_print = [];
	var top = 15;
	var margin_top = 0;
	var top1 = top;
	var window_width = $('.cust_daily_chart').width();
	var daily_hr = parseInt(res['total_minutes'] / 60);
	var daily_min = res['total_minutes'] % 60;
	if (daily_hr.toString().length == 1) {
		daily_hr = '0' + daily_hr;
	}
	if (daily_min.toString().length == 1) {
		daily_min = '0' + daily_min;
	}

	document.getElementById('daily-duration').innerHTML = daily_hr + ':' + daily_min;
	var p_left = parseInt(window_width) / 6;

	if (res['data'] != 'No activity in this date.') {
		$('.no-activities').hide();
		$('#attachPanels').show();
		$('.cust_daily_chart').show();
		var v = 15;
		var count = 0;
		for (var i = 0; i < res['data'][1].length; i++) {
			var start_time_utc = moment.utc(res['data'][1][i]['start_time']).toDate();
			var start_time_local = moment(start_time_utc).format('YYYY-MM-DD HH:mm:ss');
			var end_time_utc = moment.utc(res['data'][1][i]['end_time']).toDate();
			var end_time_local = moment(end_time_utc).format('YYYY-MM-DD HH:mm:ss');

			var start_time = start_time_local.slice(10, 16);
			var end_time = end_time_local.slice(10, 16);
			var start_time_min = start_time.slice(0, 3) * 60 + parseInt(start_time.slice(4, 6));
			var end_time_min = end_time.slice(0, 3) * 60 + parseInt(end_time.slice(4, 6));
			//calculate width for the graph.
			var interval = res['data'][1][i]['total_minutes'];
			var task_name = res['data'][2][i];
			var color = res['data'][3][i];
			var width = interval / 240 * p_left;

			var start_time_pixel = start_time_min / 240 * p_left;
			var end_time_pixel = end_time_min / 240 * p_left;

			for (var k = 0; k < pixel.length; k++) {
				if (parseInt(start_time_pixel) >= parseInt(pixel[k][0]) && parseInt(start_time_pixel) <= parseInt(pixel[k][1])) {
					count++;
					break;
				} else if (parseInt(start_time_pixel) <= parseInt(pixel[k][0]) && parseInt(start_time_pixel) <= parseInt(pixel[k][1])) {
					count++;
					break;
				}
			}
			if (start_time_pixel + width >= window_width) {
				width = window_width - start_time_pixel;
			}
			pixels_print.push([ start_time_pixel, width, margin_top + v * count, task_name, res['data'][0][i], moment(start_time_local).format('hh:mm a'), moment(end_time_local).format('hh:mm a'), color ]);

			pixel.push([ start_time_pixel, end_time_pixel ]);
			if (pixel.length == 0) {
				pixel.push([ start_time_pixel, end_time_pixel ]);
			}
		}
		for (var j = 0; j < pixels_print.length; j++) {
			printChart(pixels_print[j][0], pixels_print[j][1], pixels_print[j][2], pixels_print[j][3], pixels_print[j][4], pixels_print[j][5], pixels_print[j][6], pixels_print[j][7]);
		}
	} else {
		$('.no-activities').show();
		$('#attachPanels').hide();
		$('.cust_daily_chart').hide();
		$('#print-chart').css('height', '0px');
	}
	width = 0;
	start_time_pixel = 0;
}
var last_index;
var last_task_name = [];
var same_task = 0;
var same_task_count = 0;
function printChart(start, width, top, task_name, id, start_time, end_time, color) {
	if (top > 75) {
		$('#print-chart').css('height', top + 25);
	} else {
		$('#print-chart').css('height', 50);
	}
	var row = $(
		"<span class='positon-chart animated fadeInLeft print-chart-row " +
			id +
			"' data-html='true' data-toggle='tooltip' data-placement='top' title='" +
			'<b>' +
			start_time +
			' - ' +
			end_time +
			'</b><br>' +
			task_name +
			"' id='new-daily-chart" +
			graph_id +
			"'><input type = 'hidden' value = " +
			graph_id +
			'></span>'
	);
	$(row).css('margin-left', start);
	if (top > 350) {
		$(row).css('display', 'none');
	}
	$(row).css('bottom', top);
	$(row).css('width', width);
	$(row).css('backgroundColor', color);
	$('#print-chart').append(row);
	$('#new-daily-chart' + graph_id).mouseover(function() {
		var str = this.id;
		var matches = str.match(/(\d+)/);
		var card = document.getElementById('card-count' + matches[0]);
		card.className += ' shake';
	});
	$('#new-daily-chart' + graph_id).mouseout(function() {
		var str = this.id;
		var matches = str.match(/(\d+)/);
		var card = document.getElementById('card-count' + matches[0]);
		card.classList.remove('shake');
	});

	$('#new-daily-chart' + graph_id).click(function() {
		var classIndex = this.id.match(/(\d+)/);
		var elmnt = document.getElementById('card-count' + classIndex[0]);
		elmnt.scrollIntoView({ behavior: 'smooth' });
	});
	$('.positon-chart').tooltip('enable');
	graph_id++;
	last_task_name.push({ task_name, color });
}

last_index = undefined;

function getMonth(month) {
	var month_no = 0;
	switch (month) {
		case 'Jan':
			month_no = 1;
			break;
		case 'Feb':
			month_no = 2;
			break;
		case 'Mar':
			month_no = 3;
			break;
		case 'Apr':
			month_no = 4;
			break;
		case 'May':
			month_no = 5;
			break;
		case 'Jun':
			month_no = 6;
			break;
		case 'Jul':
			month_no = 7;
			break;
		case 'Aug':
			month_no = 8;
			break;
		case 'Sep':
			month_no = 9;
			break;
		case 'Oct':
			month_no = 10;
			break;
		case 'Nov':
			month_no = 11;
			break;
		case 'Dec':
			month_no = 12;
			break;
	}
	return month_no;
}

function getDay(day) {
	var day_no = 0;
	switch (day) {
		case 'Sun':
			day_no = 0;
			break;
		case 'Mon':
			day_no = 1;
			break;
		case 'Tue':
			day_no = 2;
			break;
		case 'Wed':
			day_no = 3;
			break;
		case 'Thu':
			day_no = 4;
			break;
		case 'Fri':
			day_no = 5;
			break;
		case 'Sat':
			day_no = 6;
			break;
	}
	return day_no;
}
function __get_date(w, y) {
	var d = 1 + (w - 1) * 7; // 1st of January + 7 days for each week
	return new Date(y, 0, d);
}
function next() {
	var filterBy = get_filterElement();
	weeklyOrMonthlyDuration = 0;
	var currentYear = parseInt(document.getElementById('monthly-chart').value.split(' ')[1]);
	var currentMonth = parseInt(document.getElementById('monthly-chart').value.split(' ')[0]);
	var cur_MY = new Date().getMonth() + ' ' + new Date().getFullYear();
	if (parseInt(cur_MY.split(' ')[0]) - 1 == currentMonth && currentYear == parseInt(cur_MY.split(' ')[1])) {
		$('#next-year').css('color', '#ccc');
	}
	if (!(parseInt(cur_MY.split(' ')[0]) == currentMonth && currentYear == parseInt(cur_MY.split(' ')[1]))) {
		currentYear = currentMonth === 11 ? currentYear + 1 : currentYear;
		currentMonth = (currentMonth + 1) % 12;
		document.getElementById('monthly-chart').value = currentMonth + ' ' + currentYear;
		if (filterBy.length > 0) showCalendar(currentMonth, currentYear, filterBy);
		else showCalendar(currentMonth, currentYear);
	}
}
function previous() {
	var filterBy = get_filterElement();
	weeklyOrMonthlyDuration = 0;
	var currentYear = parseInt(document.getElementById('monthly-chart').value.split(' ')[1]);
	var currentMonth = parseInt(document.getElementById('monthly-chart').value.split(' ')[0]);
	currentYear = currentMonth === 0 ? currentYear - 1 : currentYear;
	currentMonth = currentMonth === 0 ? 11 : currentMonth - 1;
	document.getElementById('monthly-chart').value = currentMonth + ' ' + currentYear;
	$('#next-year').css('color', '#666');
	if (filterBy.length > 0) showCalendar(currentMonth, currentYear, filterBy);
	else showCalendar(currentMonth, currentYear);
}
function hexToRgb(hex) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result
		? {
				r: parseInt(result[1], 16),
				g: parseInt(result[2], 16),
				b: parseInt(result[3], 16)
			}
		: null;
}

function showCalendar(month, year, filterBy) {
	let months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
	let firstDay = new Date(year, month).getDay();
	let daysInMonth = 32 - new Date(year, month, 32).getDate();

	let tbl = document.getElementById('calendar-body'); // body of the calendar

	// clearing all previous cells
	tbl.innerHTML = ''; // filing data about month and in the page via DOM.
	document.getElementById('current-year').innerHTML = months[month] + ' ' + year; // creating all cells
	let date = 1;
	for (let i = 0; i < 6; i++) {
		let row = document.createElement('tr');
		//creating individual cells, filing them up with data.
		for (let j = 0; j < 7; j++) {
			if (i === 0 && j < firstDay) {
				let cell = document.createElement('td');
				let cellText = document.createTextNode('');
				cell.appendChild(cellText);
				row.appendChild(cell);
			} else if (date > daysInMonth) {
				break;
			} else {
				let cell = document.createElement('td');
				let cellText = document.createTextNode(date);
				cell.appendChild(cellText);
				row.appendChild(cell);
				date++;
			}
		}
		tbl.appendChild(row); // appending each row into calendar body.
	}
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/activity_chart',
		data: { chart_type: 'monthly_chart', date: month + 1 + ' ' + year, project_filter: JSON.stringify(filterBy) },
		dataType: 'json',
		success: function(result) {
			$('.card').show();
			if (result['status'] == false) {
				$('.no-activities').show();
				$('#attachPanels').hide();
			} else {
				$('.no-activities').hide();
				$('#attachPanels').show();
				document.getElementById('monthly-chart-error').innerHTML = ' ';
			}
			var monthly_hr = parseInt(result['total_minutes'] / 60);
			var monthly_min = result['total_minutes'] % 60;

			if (monthly_hr.toString().length == 1) {
				monthly_hr = '0' + monthly_hr;
			}
			if (monthly_min.toString().length == 1) {
				monthly_min = '0' + monthly_min;
			}
			document.getElementById('monthly-duration').innerHTML = monthly_hr + ':' + monthly_min;
			for (var i = 0; i < result['data'].length; i++) {
				var res_date = result['data'][i][0].split('-')[2];
				var cal_date = document.getElementsByTagName('td');
				for (var j = 0; j < cal_date.length; j++) {
					if (parseInt(cal_date[j].innerText) == parseInt(res_date)) {
						var table_date = parseInt(cal_date[j].innerText);

						var opacity = 1;
						var scale = 1.3;
						scale = parseInt(result['data'][i][1]) * 1.3 / 9;
						opacity = parseInt(result['data'][i][1]) / 9;
						if (scale < 0.5) {
							scale = 0.5;
						}
						if (scale > 1.3) {
							scale = 1.3;
						}
						if (opacity < 0.5) {
							opacity = 0.5;
						}
						if (opacity > 1) {
							opacity = 0.85;
						}
						var rgb = hexToRgb(result['data'][i][2]);
						var rgb_color = 'rgb(' + rgb['r'] + ',' + rgb['g'] + ',' + rgb['b'] + ',' + opacity + ')';
						cal_date[j].innerText = '';
						// $(cal_date[j]).css('position', 'relative');
						let innerCell = document.createElement('span');
						$(innerCell).css('border-radius', '100%');
						$(innerCell).css('background-color', rgb_color);
						$(innerCell).css('display', 'inline-block');
						$(innerCell).css('height', '50px');
						$(innerCell).css('width', '50px');
						$(innerCell).css('padding', '14px');
						$(innerCell).css('position', 'absolute');
						$(innerCell).css('top', '0');
						$(innerCell).css('left', '28.5%');
						$(innerCell).css('transform', 'scale(' + scale + ')');
						$(innerCell).css('z-index', '0');
						let innerSpan = document.createElement('span');
						innerSpan.classList.add('monthly-action');

						$(innerSpan).attr('data-toggle', 'tooltip');
						$(innerSpan).attr('title', result['data'][i][1] + ' hrs');

						innerCell.classList.add('monthly-action1');
						$(innerSpan).css('border-radius', '100%');
						$(innerSpan).css('height', '50px');
						$(innerSpan).css('width', '50px');
						$(innerSpan).css('top', '0');
						$(innerSpan).css('padding', '14px');
						$(innerSpan).css('position', 'absolute');
						$(innerSpan).css('left', '28.5%');
						$(innerSpan).css('z-index', '1');
						let cellText1 = document.createTextNode(table_date);
						cal_date[j].appendChild(innerCell);
						cal_date[j].appendChild(innerSpan);

						innerSpan.appendChild(cellText1);
						$('.monthly-action').tooltip('enable');
						cal_date[j].appendChild(innerCell);
						$(innerSpan).click(function() {
							var date = year + '-' + (month + 1) + '-' + this.innerText;
							$.ajax({
								type: 'POST',
								url: timeTrackerBaseURL + 'index.php/user/load_task_data',
								data: {
									chart_type: 'daily_chart',
									data: date,
									project_filter: JSON.stringify(filterBy)
								},
								success: function(values) {
									if (JSON.parse(values)['status'] == true) {
										document.getElementById('daily-chart').value = date;
										document.getElementById('current-date').innerHTML = moment(moment(new Date(document.getElementById('daily-chart').value))).format('dddd MMMM DD');
										$('#chart-navigation a[href="#daily-view"]').tab('show');
									}
								}
							});
						});
					}
				}
			}
			loadTask('monthly-chart', month + 1 + ' ' + year, filterBy);
		}
	});
}
function loadCalendarChart(filterBy) {
	if (document.getElementById('monthly-chart')) {
		showCalendar(parseInt(document.getElementById('monthly-chart').value.split(' ')[0]), parseInt(document.getElementById('monthly-chart').value.split(' ')[1]), filterBy);
	} else {
		let today = new Date();
		let currentMonth = today.getMonth();
		let currentYear = today.getFullYear();
		showCalendar(currentMonth, currentYear, filterBy);
	}
}

function get_filterElement() {
	var filterBy = [];
	if (document.getElementById('activity-filtering')) {
		var user_filtering = document.getElementById('activity-filtering').getElementsByTagName('input');
		for (var i = 0; i < user_filtering.length; i++) {
			if (user_filtering[i].checked == true) {
				filterBy.push(user_filtering[i + 1].value);
			}
		}
	}
	return filterBy;
}
$(document).ready(function() {
	//Tab Change
	weeklyOrMonthlyDuration = 0;
	$('#task-detail').css('display', 'none');
	$('#weekly-chart').hide();

	// if (document.getElementById('chart-navigation')) window.location.hash = 'daily-tab';
	var currentTab = '';
	$('#chart-navigation a').on('shown.bs.tab', function(event) {
		event.preventDefault();
		currentTab = $(event.target).attr('href'); // active tab
		var y = $(event.relatedTarget); // previous tab

		// window.location.hash = this.id;
		var filterBy = [];
		if (document.getElementById('activity-filtering')) {
			var user_filtering = document.getElementById('activity-filtering').getElementsByTagName('input');
			for (var i = 0; i < user_filtering.length; i++) {
				if (user_filtering[i].checked == true) {
					filterBy.push(user_filtering[i + 1].value);
				}
			}
		}
		if (filterBy.length > 0) {
			if (currentTab == '#daily-view') {
				weeklyOrMonthlyDuration = 0;
				loadDailyChart(filterBy);
			}
			if (currentTab == '#weekly-view') {
				weeklyOrMonthlyDuration = 0;
				loadWeeklyChart(filterBy);
			}
			if (currentTab == '#monthly-view') {
				weeklyOrMonthlyDuration = 0;
				loadCalendarChart(filterBy);
			}
		} else {
			if (currentTab == '#daily-view') {
				weeklyOrMonthlyDuration = 0;
				loadDailyChart();
			}
			if (currentTab == '#weekly-view') {
				weeklyOrMonthlyDuration = 0;
				loadWeeklyChart();
			}
			if (currentTab == '#monthly-view') {
				weeklyOrMonthlyDuration = 0;
				loadCalendarChart();
			}
		}
	});

	let today = new Date();
	let currentMonth = today.getMonth();
	let currentYear = today.getFullYear();
	if (document.getElementById('monthly-chart')) {
		document.getElementById('monthly-chart').value = currentMonth + ' ' + currentYear;
	}
	/*daily_value.*/
	var win_width = $('.cust_daily_chart').width();
	var p_l = parseInt(win_width) / 6 - 50;
	$('.cust_chart').css('padding-left', p_l);
	var tday = moment(new Date());
	if (document.getElementById('current-date')) {
		document.getElementById('current-date').innerHTML = moment(tday).format('dddd MMMM DD');
	}
	if (document.getElementById('daily-chart')) {
		document.getElementById('daily-chart').value = moment(tday).format('YYYY-MM-DD');
	}
	var daily_chart_date = '';
	if (document.getElementById('daily-chart')) {
		daily_chart_date = document.getElementById('daily-chart').value;
	}

	loadDailyChart();
	var day = new Date(daily_chart_date);
	var nextDay = new Date(day);
	nextDay.setDate(day.getDate() + 1);
	if (moment(nextDay).format('YYYY-MM-DD') > moment(new Date()).format('YYYY-MM-DD')) {
		$('#next-date').css('color', '#ccc');
	}
	$('#next-date').unbind().click(function() {
		var filterBy = get_filterElement();
		weeklyOrMonthlyDuration = 0;
		var daily_chart_date = document.getElementById('daily-chart').value;
		var day = new Date(daily_chart_date);
		var nextDay = new Date(day);
		nextDay.setDate(day.getDate() + 1);
		if (moment(nextDay).format('YYYY-MM-DD') > moment(new Date()).format('YYYY-MM-DD')) {
			$('#next-date').css('color', '#ccc');
		} else {
			$('#next-date').css('color', '#a280fc');
			document.getElementById('daily-chart').value = moment(nextDay).format('YYYY-MM-DD');
			document.getElementById('current-date').innerHTML = moment(nextDay).format('dddd MMMM DD');
			if (filterBy.length > 0) loadDailyChart(filterBy);
			else loadDailyChart();
		}
		var check_for_ancher = nextDay;
		check_for_ancher.setDate(day.getDate() + 2);
		if (moment(check_for_ancher).format('YYYY-MM-DD') > moment(new Date()).format('YYYY-MM-DD')) {
			$('#next-date').css('color', '#ccc');
		}
	});

	$('#previous-date').unbind().click(function() {
		var filterBy = get_filterElement();
		weeklyOrMonthlyDuration = 0;
		var daily_chart_date = document.getElementById('daily-chart').value;
		var day = new Date(daily_chart_date);
		var nextDay = new Date(day);
		$('#next-date').css('color', '#666');
		nextDay.setDate(day.getDate() - 1);
		document.getElementById('daily-chart').value = moment(nextDay).format('YYYY-MM-DD');
		document.getElementById('current-date').innerHTML = moment(nextDay).format('dddd MMMM DD');
		if (filterBy.length > 0) loadDailyChart(filterBy);
		else loadDailyChart();
	});

	var t_day = new Date();
	var day = parseInt(moment(t_day).format('E'));
	t_day.setDate(t_day.getDate() - (day - 1));
	var s_date = moment(t_day).format('MMM DD');
	t_day.setDate(t_day.getDate() + 6);
	var e_date = moment(t_day).format('MMM DD');
	t_day.setDate(t_day.getDate() - 6);
	$('#next-week').css('color', '#ccc');
	$('#next-year').css('color', '#ccc');

	if (document.getElementById('current-week')) document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
	if (document.getElementById('week_y')) document.getElementById('week_y').innerHTML = moment(t_day).format('YYYY-MM-DD');

	if (document.getElementById('weekly-chart')) document.getElementById('weekly-chart').value = moment(t_day).format('YYYY') + '-W' + (parseInt(moment(t_day).format('W')) + 1);

	var daily_chart_date1 = '';
	if (document.getElementById('weekly-chart')) {
		daily_chart_date1 = document.getElementById('weekly-chart').value;
	}
	$('#next-week').unbind().click(function() {
		var filterBy = get_filterElement();
		weeklyOrMonthlyDuration = 0;
		var daily_chart_date1 = document.getElementById('weekly-chart').value;
		var week_no = parseInt(daily_chart_date1.slice(6, 8));
		var c_week = moment(new Date()).format('W');
		if (week_no <= parseInt(c_week) && parseInt(daily_chart_date1.slice(0, 4)) == parseInt(moment(new Date()).format('YYYY'))) {
			if (c_week == week_no) {
				$('#next-week').css('color', '#ccc');
			}
			week_no++;
			if (week_no.toString().length == 1) {
				week_no = '0' + week_no;
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			} else {
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			}
			document.getElementById('week_y').innerHTML = moment(document.getElementById('week_y').innerHTML).add(7, 'days').format('YYYY-MM-DD');
			t_day.setDate(t_day.getDate() + 7);
			var s_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() + 6);
			var e_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() - 6);
			document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
			if (filterBy.length > 0) loadWeeklyChart(filterBy);
			else loadWeeklyChart();
		} else {
			$('#next-week').css('color', '#ccc');
		}
	});
	$('#previous-week').unbind().click(function() {
		var filterBy = get_filterElement();
		weeklyOrMonthlyDuration = 0;
		var daily_chart_date1 = document.getElementById('weekly-chart').value;
		var week_no = parseInt(daily_chart_date1.slice(6, 8));

		if (parseInt(daily_chart_date1.slice(0, 4)) == parseInt(moment(new Date()).format('YYYY'))) {
			week_no--;
			$('#next-week').css('color', '#666');
			if (week_no.toString().length == 1) {
				week_no = '0' + week_no;
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			} else {
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			}
			document.getElementById('week_y').innerHTML = moment(document.getElementById('week_y').innerHTML).subtract(7, 'days').format('YYYY-MM-DD');
			t_day.setDate(t_day.getDate() - 1);
			var e_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() - 6);
			var s_date = moment(t_day).format('MMM DD');
			document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
			if (filterBy.length > 0) loadWeeklyChart(filterBy);
			else loadWeeklyChart();
		}
	});

	var toggleFilter = 0;
	if (document.getElementById('activity-filter')) {
		var filter_form = document.getElementById('activity-filter');
		filter_form.onsubmit = function() {
			weeklyOrMonthlyDuration = 0;
			var user_filtering = document.getElementById('activity-filtering').getElementsByTagName('input');
			var filterBy = [];
			for (var i = 0; i < user_filtering.length; i++) {
				if (user_filtering[i].checked == true) {
					filterBy.push(user_filtering[i + 1].value);
				}
			}
			if (filterBy.length > 0) {
				if (currentTab == '' || currentTab == '#daily-view') {
					loadDailyChart(filterBy);
				} else if (currentTab == '#weekly-view') {
					loadWeeklyChart(filterBy);
				} else if (currentTab == '#monthly-view') {
					loadCalendarChart(filterBy);
				}
				$('.alert-filter').show();
			} else {
				if (currentTab == '' || currentTab == '#daily-view') {
					loadDailyChart();
				} else if (currentTab == '#weekly-view') {
					loadWeeklyChart();
				} else if (currentTab == '#monthly-view') {
					loadCalendarChart();
				}
				$('.alert-filter').hide();
			}

			$('.activity-filter').hide();
			toggleFilter = 0;
			document.getElementById('project-filtering').childNodes[1].classList.add('fa-sliders-h');
			document.getElementById('project-filtering').childNodes[1].classList.remove('fa-times-circle');
			document.getElementById('project-filtering').classList.add('fadeIn');
			return false;
		};
	}

	if (document.getElementById('activity-filtering')) {
		var user_filtering = document.getElementById('activity-filtering').getElementsByTagName('input');
		for (var i = 0; i < user_filtering.length; i++) {
			if (user_filtering[i].checked == true) {
				$('.alert-filter').show();
			}
		}
	}
	$('.alert-filter').click(function() {
		if (toggleFilter == 0) {
			$('.activity-filter').show();
			toggleFilter = 1;
			document.getElementById('project-filtering').childNodes[1].classList.remove('fa-sliders-h');
			document.getElementById('project-filtering').childNodes[1].classList.add('fa-times-circle');
			document.getElementById('project-filtering').classList.add('fadeIn');
		} else {
			$('.activity-filter').hide();
			toggleFilter = 0;
			document.getElementById('project-filtering').childNodes[1].classList.add('fa-sliders-h');
			document.getElementById('project-filtering').childNodes[1].classList.remove('fa-times-circle');
			document.getElementById('project-filtering').classList.add('fadeIn');
		}
	});
	$('#project-filtering').click(function() {
		if (toggleFilter == 0) {
			$('.activity-filter').show();
			toggleFilter = 1;
			document.getElementById('project-filtering').childNodes[1].classList.remove('fa-sliders-h');
			document.getElementById('project-filtering').childNodes[1].classList.add('fa-times-circle');
			document.getElementById('project-filtering').classList.add('fadeIn');
		} else {
			$('.activity-filter').hide();
			toggleFilter = 0;
			document.getElementById('project-filtering').childNodes[1].classList.add('fa-sliders-h');
			document.getElementById('project-filtering').childNodes[1].classList.remove('fa-times-circle');
			document.getElementById('project-filtering').classList.add('fadeIn');
		}
	});
});
