var panel_id = 0;
var graph_id = 0;
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
	if (data['data'].length != undefined || data['data'].length != null) {
		var x = data['data'].length;
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
				var timeZone = moment.tz.guess();
				var date = data['data'][y].start_time.slice(0, 10);
				var start_time = data['data'][y].start_time;
				var start_time_utc = moment.utc(start_time).toDate();
				var serverDate = moment(start_time_utc).format('YYYY-MM-DD hh:mm a');
				if (serverDate != 'Invalid date') {
					cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + serverDate + '</div>');
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
			var cardCol = $("<div class='col-lg-6 mb-4 card-col animated card-count" + panel_id + "' />");
			cardCol.append(cardInner);

			$('#attachPanels').append(cardCol);

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
	}
}
function loadTask(type, date) {
	$('#attachPanels').empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/load_task_data',
		data: { chart_type: type, date: date },
		success: function(values) {
			if (values == 'No activity in this date.') {
				$('#attachPanels').empty();

				$('#attachPanels').empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
			} else {
				var data = JSON.parse(values);
				$('#attachPanels').empty();
				draw_chart_cards(data, type);
			}
		}
	});
}

function loadDailyChart() {
	var date = "";
	if(document.getElementById('daily-chart'))
	{
		date =  document.getElementById('daily-chart').value;
	
	if (date == '' || date == ' ' || date == null) {
		var today = new Date();
		document.getElementById('daily-chart').value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);

		date = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
		document.getElementById('daily-chart').setAttribute('max', date);

		date = document.getElementById('daily-chart').value;

		retrieveChartData('daily_chart', date);
	} else {
		retrieveChartData('daily_chart', date);
	}
}
}

function loadWeeklyChart() {
	if (document.getElementById('weekly-chart')) {
		var weekControl = document.querySelector('input[type="week"]');
		var week = document.getElementById('weekly-chart').value;
		var day_range = '';
		if (document.getElementById('current-week')) {
			day_range = document.getElementById('current-week').innerHTML;


			var year = document.getElementById('week_y').innerHTML.split('-')[0];
			day_range = year + '-' + day_range.split(' ')[0] + '-' + day_range.split(' ')[1] + '~' + moment().format('YYYY') + '-' + day_range.split(' ')[3] + '-' + day_range.split(' ')[4];
			retrieveChartData('weekly_chart', day_range);
		}
	}
}

function drawChart(type, res, date) {
	if (res['status'] == false) {
		document.getElementById('week-error').innerHTML = 'No activity in this week.';
		window.myBar.destroy();
		$('#weekly').hide();
		document.getElementById('weekly-duration').innerHTML = '00:00';
		$('#attachPanels').empty();
	} else {
		var weekly_hr = parseInt(res['total_minutes'] / 60);
		var weekly_min = res['total_minutes'] % 60;
		if (weekly_hr.toString().length == 1) {
			weekly_hr = '0' + weekly_hr;
		}
		if (weekly_min.toString().length == 1) {
			weekly_min = '0' + weekly_min;
		}

		document.getElementById('weekly-duration').innerHTML = weekly_hr + ':' + weekly_min;
		var const_lable = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
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
					enabled: true
				},
				onClick: function(event, data) {
					var month = getMonth(document.getElementById('current-week').innerHTML.split('-')[0].split(' ')[0]);
					if (month.toString().length == 1) {
						month = '0' + month;
					}
					var day = parseInt(document.getElementById('current-week').innerHTML.split('-')[0].split(' ')[1]) + parseInt(myBar.getElementsAtEvent(event)[0]['_index']);
					var year = document.getElementById('week_y');
					var d_time = moment(month + '-' + day + '-' + year.innerHTML.split('-')[0]);
					document.getElementById('current-date').innerHTML = d_time.format('dddd MMMM DD');
					document.getElementById('daily-chart').value = d_time.format('YYYY-MM-DD');
					$('#chart-navigation a[href="#daily-view"]').tab('show');
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
							stacked: true
						}
					]
				}
			}
		});
	}
	// window.myBar = new Chart(ctx, window.myBar);
}

function retrieveChartData(type, date) {
	$('#print-chart').empty();
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/activity_chart',
		data: { chart_type: type, date: date },
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
					loadTask(type, date);
					document.getElementById('week-error').innerHTML = ' ';
					drawChart(type, res, date);
					$('#weekly').show();
				}
				if (type == 'daily_chart') {
					loadTask(type, date);
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
	var element = document.getElementById('daily');
	var pixel = [];
	var top = 25;
	var margin_top = 320;
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

	if (window_width > 340) {
		margin_top = 295;
	}
	if (window_width > 679) {
		margin_top = 364;
	}
	var p_left = parseInt(window_width) / 12;
	if (res['data'] != 'No activity in this date.') {
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

			var width = interval / 60 * p_left;
			if (start_time_min < 480) {
				// graph leess than 8 am is not shown
				start_time_min = 480;
			}
			var start_time_pixel = (start_time_min / 60 - 8) * p_left;
			var end_time_pixel = (end_time_min / 60 - 8) * p_left;

			var v = 0;
			for (var k = 0; k < pixel.length; k++) {
				if (start_time_pixel >= pixel[k][0] && start_time_pixel <= pixel[k][1]) {
					v = 25;
					var pixel1 = pixel;
					pixel1[k][0] = null;
					pixel1[k][1] = null;
					for (var e = 0; e < pixel1.length; e++) {
						if (start_time_pixel + width >= window_width) {
							width = window_width - start_time_pixel;
						}
						if (start_time_pixel >= pixel1[e][0] && start_time_pixel <= pixel1[e][1]) {
							v = v + 25;
							printChart(start_time_pixel, width, margin_top - v, task_name, res['data'][0][i], moment(start_time_local).format('hh:mm a'), moment(end_time_local).format('hh:mm a'), color);
							break;
						} else {
							printChart(start_time_pixel, width, margin_top - v, task_name, res['data'][0][i], moment(start_time_local).format('hh:mm a'), moment(end_time_local).format('hh:mm a'), color);
							break;
						}
					}
				}
			}
			if (top1 == top && pixel.length != 0) {
				if (v == 0) {
					if (start_time_pixel + width >= window_width) {
						width = window_width - start_time_pixel;
					}
					printChart(start_time_pixel, width, margin_top, task_name, res['data'][0][i], moment(start_time_local).format('hh:mm a'), moment(end_time_local).format('hh:mm a'), color);
				}
			}
			if (pixel.length == 0) {
				if (start_time_pixel + width >= window_width) {
					width = window_width - start_time_pixel;
				}
				printChart(start_time_pixel, width, margin_top, task_name, res['data'][0][i], moment(start_time_local).format('hh:mm a'), moment(end_time_local).format('hh:mm a'), color);
			}
			pixel.push([ start_time_pixel, end_time_pixel ]);
		}
	}
	width = 0;
	start_time_pixel = 0;
}
var last_index;
var last_task_name = [];
var same_task = 0;
function printChart(start, width, top, task_name, id, start_time, end_time, color) {
	var row = $("<span class='positon-chart animated fadeInLeft print-chart-row " + id + "'  data-toggle='tooltip' data-placement='top' title=" + task_name + " id='new-daily-chart" + graph_id + "'>.<input type = 'hidden' value = " + graph_id + '></span>');
	$(row).css('margin-left', start);
	$(row).css('position', 'absolute');
	$(row).css('top', top);
	$(row).css('width', width);
	$(row).css('backgroundColor', color);
	$(row).css('color', color);
	$('#print-chart').append(row);
	$('#new-daily-chart' + graph_id).mousedown(function() {
		document.getElementById('task-detail').innerHTML = start_time + ' - ' + end_time;
		$('#task-detail').css('display', 'block');
		$('#task-detail').css('margin-left', start + 10);
		var str = this.id;
		var matches = str.match(/(\d+)/);
		var card = document.getElementsByClassName('card-count' + matches[0])[0];
		card.className += ' shake';
	});

	$('#new-daily-chart' + graph_id).mouseup(function() {
		document.getElementById('task-detail').innerHTML = ' ';
		$('#task-detail').css('display', 'none');
		var str = this.id;
		var matches = str.match(/(\d+)/);
		var card = document.getElementsByClassName('card-count' + matches[0])[0];
		card.classList.remove('shake');
	});

	$('#new-daily-chart' + graph_id).click(function() {
		var ele = document.getElementById(this.id);
		var index = parseInt(ele.childNodes[1].value) + 4;
		if (last_index) {
			$('.panel' + last_index).css('backgroundColor', '#ffffff');
		}
		last_index = id;
		var elmnt = document.getElementsByClassName('card-count' + index);
	});

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
		//$('#next-year').css('color', '#666');
		showCalendar(currentMonth, currentYear);
	}
}
function previous() {
	var currentYear = parseInt(document.getElementById('monthly-chart').value.split(' ')[1]);
	var currentMonth = parseInt(document.getElementById('monthly-chart').value.split(' ')[0]);
	currentYear = currentMonth === 0 ? currentYear - 1 : currentYear;
	currentMonth = currentMonth === 0 ? 11 : currentMonth - 1;
	document.getElementById('monthly-chart').value = currentMonth + ' ' + currentYear;
	$('#next-year').css('color', '#666');
	showCalendar(currentMonth, currentYear);
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

function showCalendar(month, year) {
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
		data: { chart_type: 'monthly_chart', date: month + 1 + ' ' + year },
		dataType: 'json',
		success: function(result) {
			$('.card').show();
			if (result['status'] == false) {
				document.getElementById('monthly-chart-error').innerHTML = 'No activities in this month';
			} else {
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
						var rgb = hexToRgb(result['data'][i][2]);
						var rgb_color = 'rgb(' + rgb['r'] + ',' + rgb['g'] + ',' + rgb['b'] + ',' + opacity + ')';
						cal_date[j].innerText = '';
						$(cal_date[j]).css('position', 'relative');
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

						cal_date[j].appendChild(innerCell);
						$(innerSpan).click(function() {
							var date = year + '-' + (month + 1) + '-' + this.innerText;
							$.ajax({
								type: 'POST',
								url: timeTrackerBaseURL + 'index.php/user/load_task_data',
								data: {
									chart_type: 'daily_chart',
									data: date
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
			loadTask('monthly-chart', month + 1 + ' ' + year);
		}
	});
}
function loadCalendarChart() {
	if (document.getElementById('monthly-chart')) {
		showCalendar(parseInt(document.getElementById('monthly-chart').value.split(' ')[0]), parseInt(document.getElementById('monthly-chart').value.split(' ')[1]));
	} else {
		let today = new Date();
		let currentMonth = today.getMonth();
		let currentYear = today.getFullYear();
		showCalendar(currentMonth, currentYear);
	}
}
$(document).ready(function() {
	//Tab Change
	$('#task-detail').css('display', 'none');
	$('#weekly-chart').hide();
	let today = new Date();
	let currentMonth = today.getMonth();
	let currentYear = today.getFullYear();
	if (document.getElementById('monthly-chart')) {
		document.getElementById('monthly-chart').value = currentMonth + ' ' + currentYear;
	}

	var win_width = $('.cust_daily_chart').width();
	var p_l = parseInt(win_width) / 23;
	$('.cust_chart').css('padding-left', p_l);

	$('#daily-chart').change(function() {
		loadDailyChart();
	});
	$('#weekly-chart').change(function() {
		loadWeeklyChart();
	});
	$('#monthly-chart').change(function() {
		loadCalendarChart();
	});
	var tday = moment(new Date());
	if (document.getElementById('current-date')) {
		document.getElementById('current-date').innerHTML = moment(tday).format('dddd MMMM DD');
	}
	if (document.getElementById('daily-chart')) {
		document.getElementById('daily-chart').value = moment(tday).format('YYYY-MM-DD');
	}
	var daily_chart_date = "";
	if(document.getElementById('daily-chart'))
	{
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
			loadDailyChart();
		}
		var check_for_ancher = nextDay;
		check_for_ancher.setDate(day.getDate() + 2);
		if (moment(check_for_ancher).format('YYYY-MM-DD') > moment(new Date()).format('YYYY-MM-DD')) {
			$('#next-date').css('color', '#ccc');
		}
	});

	$('#previous-date').unbind().click(function() {
		var daily_chart_date = document.getElementById('daily-chart').value;
		var day = new Date(daily_chart_date);
		var nextDay = new Date(day);
		$('#next-date').css('color', '#666');
		nextDay.setDate(day.getDate() - 1);
		document.getElementById('daily-chart').value = moment(nextDay).format('YYYY-MM-DD');
		document.getElementById('current-date').innerHTML = moment(nextDay).format('dddd MMMM DD');
		loadDailyChart();
	});

	var t_day = new Date();
	var day = parseInt(moment(t_day).format('E'));
	t_day.setDate(t_day.getDate() - day);
	var s_date = moment(t_day).format('MMM DD');
	t_day.setDate(t_day.getDate() + 6);
	var e_date = moment(t_day).format('MMM DD');
	t_day.setDate(t_day.getDate() - 6);
	$('#next-week').css('color', '#ccc');
	$('#next-year').css('color', '#ccc');

	if(document.getElementById('current-week'))
		document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
	if(document.getElementById('week_y'))
	document.getElementById('week_y').innerHTML = moment(t_day).format('YYYY-MM-DD');

	if(document.getElementById('weekly-chart'))
	document.getElementById('weekly-chart').value = moment(t_day).format('YYYY') + '-W' + (parseInt(moment(t_day).format('W')) + 1); //format 2020-W05

	var daily_chart_date1 = "";
	if(document.getElementById('weekly-chart'))
	{
		daily_chart_date1 = document.getElementById('weekly-chart').value
	}

	$('#next-week').unbind().click(function() {
		var daily_chart_date1 = document.getElementById('weekly-chart').value;
		var week_no = parseInt(daily_chart_date1.slice(6, 8));
		var c_week = moment(new Date()).format('W');
		if (week_no < parseInt(c_week) && parseInt(daily_chart_date1.slice(0, 4)) == parseInt(moment(new Date()).format('YYYY'))) {
			if (c_week == week_no + 1) {
				$('#next-week').css('color', '#ccc');
			}
			week_no++;
			if (week_no.toString().length == 1) {
				week_no = '0' + week_no;
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			} else {
				document.getElementById('weekly-chart').value = daily_chart_date1.slice(0, 6) + week_no;
			}
			document.getElementById('week_y').innerHTML = moment(document.getElementById('week_y').innerHTML).add(7, 'days').format("YYYY-MM-DD");
			t_day.setDate(t_day.getDate() + 7);
			var s_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() + 6);
			var e_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() - 6);
			document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
			loadWeeklyChart();
		} else {
			$('#next-week').css('color', '#ccc');
		}
	});
	$('#previous-week').unbind().click(function() {
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
			document.getElementById('week_y').innerHTML = moment(document.getElementById('week_y').innerHTML).subtract(7, 'days').format("YYYY-MM-DD");
			t_day.setDate(t_day.getDate() - 1);
			var e_date = moment(t_day).format('MMM DD');
			t_day.setDate(t_day.getDate() - 6);
			var s_date = moment(t_day).format('MMM DD');
			document.getElementById('current-week').innerHTML = s_date + ' - ' + e_date;
			loadWeeklyChart();
		}
	});
	/*daily_value.*/
	if (win_width < 400) {
		document.getElementById('chart-labels').remove();
		var new_lebels = $(
			'<p class="cust_daily_chart" ><span class="">8AM</span><span class="cust_chart">10AM</span><span class="cust_chart">12AM</span><span class="cust_chart">2PM</span><span class="cust_chart">4PM</span><span class="cust_chart">6PM</span><span class="cust_chart">8PM</span></p>'
		);
		$('#daily').append(new_lebels);
		var p_l = parseInt(win_width) / 24;
		$('.cust_chart').css('padding-left', p_l);
	}
	if (win_width < 1000 && win_width > 400) {
		document.getElementById('chart-labels').css('display', 'none');
		var new_lebels = $('<p class="cust_daily_chart"><span class="">8AM</span><span class="cust_chart">10AM</span><span class="cust_chart">12AM</span><span class="cust_chart">2PM</span><span class="cust_chart">4PM</span><span class="cust_chart">6PM</span><span class="cust_chart">8PM</span></p>');
		$('#daily').append(new_lebels);
		var p_l = parseInt(win_width) / 10;
		$('.cust_chart').css('padding-left', p_l);
	}

	$('#chart-navigation a').on('shown.bs.tab', function(event) {
		var x = $(event.target).attr('href'); // active tab
		var y = $(event.relatedTarget); // previous tab
		if (x == '#daily-view') {
			var date = document.getElementById('daily-chart').value;
			loadDailyChart();
		}
		if (x == '#weekly-view') {
			loadWeeklyChart();
			var date = document.getElementById('weekly-chart').value;
			//loadTask('weekly_chart', date);
		}
		if (x == '#monthly-view') {
			loadCalendarChart();
		}
	});
});
