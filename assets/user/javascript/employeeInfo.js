//main timer interval for login
var mainTimerInterval;

function startTimer(startTime) {
	if (startTime === 'stop') {
		//clear the existing interval
		clearInterval(mainTimerInterval);
	} else {
		//set in local storage
		localStorage.setItem('timeStamp', startTime);
		mainTimerInterval = setInterval(function() {
			startTime++;
			setTime(startTime);
		}, 1000);
	}
}

function setTime(startTime) {
	localStorage.setItem('timeStamp', startTime);

	var date = new Date(startTime * 1000);
	// Hours part from the timestamp
	var hours = '0' + date.getHours();
	// Minutes part from the timestamp
	var minutes = '0' + date.getMinutes();
	// Seconds part from the timestamp
	var seconds = '0' + date.getSeconds();

	var formattedTime = hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

	$('#primary-timer').html(formattedTime);
}

function addZeroBefore(n) {
	return (n < 10 ? '0' : '') + n;
}

function minutesToTime(mins) {
	var hr = parseInt(parseInt(mins) / 60);
	var min = parseInt(mins) % 60;
	if (min.toString().length == 1) {
		min = '0' + min;
	}
	if (hr.toString().length == 1) {
		hr = '0' + hr;
	}
	min = min + 'h';
	return hr + ':' + min;
}
function getTime() {
	var timeLogout = new Date();
	var logoutTime = timeLogout.getFullYear() + '-' + (timeLogout.getMonth() + 1) + '-' + timeLogout.getDate();

	var date = timeLogout.getFullYear() + '-' + (timeLogout.getMonth() + 1) + '-' + timeLogout.getDate();
	return date;
}

function formatAMPM(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var ampm = hours >= 12 ? 'PM' : 'AM';
	hours = hours % 12;
	hours = hours ? hours : 12; // the hour '0' should be '12'
	minutes = minutes < 10 ? '0' + minutes : minutes;
	var strTime = hours + ':' + minutes + ' ' + ampm;
	return strTime;
}

function drawCards(data) {
	if (data['data'] == null) {
		document.getElementById('alarmmsg').innerHTML = 'No data available';
	} else {
		for (x in data) {
			for (var y = 0; y < data[x].length; y++) {
				var cardHeader = $('<div class="card-header card-header" />');
				var cardHeaderRow = $('<div class="row pt-2" />');
				var today = getTime();
				if (data[x][y].start_time != null) {
					var task_date = data[x][y].start_time.slice(0, 10);
					if (today != task_date) {
						$('.alert-box').show();
					}
				}
				if (data[x][y].start_time == null) {
					cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>Not yet started.</div>');
				} else {
					var date = data[x][y].start_time.slice(0, 10);
					var start_time = data[x][y].start_time;
					var start_time_utc = moment.utc(start_time).toDate();
					var serverDate1 = moment(start_time_utc).format('YYYY-MM-DD hh:mm a');
					if (serverDate1 != 'Invalid date') {
						cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + serverDate1 + '</div>');
					} else {
						cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + data[x][y].start_time + '</div>');
					}
				}
				var stopCol = $('<div class="col-6 text-right"  id="btn-stop' + data[x][y].id + '" />');
				if (data[x][y].running_task == 0) {
					/*check whether task is ended or not*/
					var timeUsed = minutesToTime(data[x][y].t_minutes);
					stopCol.append('<i class="far fa-clock"></i> ' + timeUsed);
				} else {
					var id = data[x][y].id;
					if (data[x][y].start_time != null) {
						var stopButton = $('<i class="fa fa-hourglass-1">' + '&nbsp' + '</i><span class="running-task" id="running-task-' + data[x][y].id + '"></span>').data('taskid', data[x][y].id);
					}
					stopCol.append(stopButton);
				}

				cardHeaderRow.append(stopCol);
				cardHeader.append(cardHeaderRow);

				var cardInner = $("<div class='card card-style-1 animated fadeIn' />");
				cardInner.append(cardHeader);

				var cardBody = $("<div class='card-body' />");
				cardBody.append(data[x][y].task_name);
				cardInner.append(cardBody);
				var cardFooter = $("<div class='card-footer card-footer'>");
				var footerRow = $('<div class="row" />');

				footerRow.append((data[x][y].image_name !== null ? "<div class='col-12'> <img src=" + data[x][y].image_name + " width='20px;'> " : '') + data[x][y].project + '</div>');
				var date = new moment().format('Y-MM-DD H:mm:ss');
				var serverDate = moment(date).tz('utc').format('Y-MM-DD H:mm:ss');

				var footerRight = $("<div class='card-actions' id='footer-right-" + data[x][y].id + "'>");

				//action Edit
				var actionEdit = $('<a href="#" class=" pl-2  text-white " id="action-edit"><i class="fas fa-pencil-alt action-play" data-toggle="tooltip" data-placement="top" title="edit"></i></a>');
				actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_add_task?t_id=' + data[x][y].id );
				footerRight.append(actionEdit);

				var actionPlay = $('<a href="' + timeTrackerBaseURL + 'user/start_timer?id=' + data[x][y].id + '&time=' + serverDate + '" class="card-action" data-id="' + data[x][y].id + '" id=play-' + data[x][y].id + ' data-toggle="tooltip" data-placement="top" title="Play"></a>');
				actionPlay.append('<i class="fas action-edit  fa-play"></i>');

				var actionStop = $('<a href="' + timeTrackerBaseURL + 'user/stop_timer?id=' + data[x][y].id + '&time=' + serverDate + '" class="card-action" data-id="' + data[x][y].id  + '" id=stop-' + data[x][y].id + ' data-toggle="tooltip" data-placement="top" title="Stop"></a>');

				actionStop.append('<i class="fas action-edit fa-stop"></i>');
				footerRight.append(actionPlay);
				footerRight.append(actionStop);

				if (data[x][y].running_task == 0) {
					//$('#stop-' + data[x][y].id).hide();
					$(actionStop).hide();
				} else {
					//$('#play-' + data[x][y].id).hide();
					$(actionPlay).hide();
				}
				actionPlay.on('click', function(e) {
					e.preventDefault();
					var t_id = $(this).data('id');
					if (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play') {
						$('#play-timer').modal('show');
					} else {
						$.ajax({
							type: 'POST',
							url: timeTrackerBaseURL + 'index.php/user/start_timer',
							data: { action: 'task', id: t_id, time: serverDate },
							dataType: 'json',
							success: function(res) {
								if(res['status'] == true)
								{
								var res = res['data']['details'];
								var startDateTime = moment().format('h:mm A');
								var row = $(
									'<div id="slider-' +
										res['task_id'] +
										'">' +
										'<div class="section-slider task-slider">' +
										'<input type="hidden" id="' +
										res['task_id'] +
										'" value="' +
										res['t_minutes'] +
										'">' +
										'<input type="hidden" id="id-' +
										res.id +
										'" value="' +
										res['task_id'] +
										'">' +
										'<p class="font-weight-light time-font text-center login-time" id="start-time' +
										res['task_id'] +
										'">' +
										'Started at ' +
										startDateTime +
										' </p>' +
										'<div class="font-weight-light text-center primary-timer start-task-timer" id="task-timer' +
										res['task_id'] +
										'" data-type="" data-time="">00:00:00</div>' +
										'<p class="font-weight-light text-center taskName">' +
										res['task_name'] +
										'</p>' +
										'</div>' +
										'</div>'
								);
								var stopButton = $('<i class="fa fa-hourglass-1"></i><span class="running-task" id="running-task-' + t_id + '"></span>').data('taskid', t_id);
								document.getElementById('btn-stop' + t_id).childNodes[0].remove();
								document.getElementById('btn-stop' + t_id).childNodes[0].remove();
								$('#btn-stop' + t_id).append(stopButton);
						
								$('#play-' + t_id).hide();
								$('#stop-' + t_id).show();

								$('#timer-slider').append(row);
								timerSlider.reload();
								document.getElementsByClassName('title').innerText += start_task_timer(-19800, t_id);
								// $('.toast').toast('show');
								}
							}
						});
					}
				});

				actionStop.on('click', function(e) {
					e.preventDefault();
					var task_id = $(this).data('id');
					if (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play') {
						$('#play-timer').modal('show');
					} else {
						$.ajax({
							type: 'POST',
							url: timeTrackerBaseURL + 'index.php/user/stop_timer',
							data: {
								action: 'task',
								id: task_id,
								flag: '0',
								time: serverDate
							},
							dataType: 'json',
							success: function(res) {
								var data = res['flag'];
								if(res['status'] == true)
								{
								$('#running-task-' + task_id).hide();
								$('#play-' + task_id).show();
								$('#stop-' + task_id).hide();

								document.getElementById('btn-stop' + task_id).childNodes[0].remove();
								$('#btn-stop' + task_id).append('<i class="far fa-clock"></i> ' + minutesToTime(data['details']['t_minutes']));
								document.getElementById('slider' + task_id).remove();
								if (timerSlider.slider.getSlideCount() == 1) {
									$('.bx-pager-item').css('display', 'none');
								}
								timerSlider.reload();
								}
								
							}
						});
					}
				});
				
				cardFooter.append(footerRow);
				cardInner.append(cardFooter);
				//add a overlay layer
				var cardOverlay = $("<div class='card-overlay' />");
				cardInner.append(cardOverlay);
				//add action overlay
				var cardActions = $("<div class='card-action-overlay' />");
				cardActions.append(footerRight);
				cardInner.append(cardActions);

				var cardCol = $("<div class='col-lg-6 mb-4 card-col' />");
				cardCol.append(cardInner);

				$('#attach-card #activites-result').append(cardCol);
				if (data[x][y].running_task == 1 && data[x][y].start_time != null) {
					//change background of current running task entries.
					cardInner.css('background-image', 'linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
					cardHeader.css('background-image', 'linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
					cardFooter.css('background-image', 'linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
					document.getElementsByClassName('title').innerText += data[x][y].task_name;
				}
			}
		}
	}
}

var mainTaskInterval;

function start_task_timer(startTime, id) {
	if (startTime === 'stop') {
		//clear the existing interval
		clearInterval(mainTaskInterval);
	} else {
		localStorage.setItem('timeStamp', startTime);
		mainTaskInterval = setInterval(function() {
			startTime++;
			setTaskTime(startTime, id);
		}, 1000);
	}
}

function setTaskTime(startTime, id) {
	//update local storage
	localStorage.setItem('timeStamp', startTime);

	var date = new Date(startTime * 1000);
	// Hours part from the timestamp
	var hours = '0' + date.getHours();
	// Minutes part from the timestamp
	var minutes = '0' + date.getMinutes();
	// Seconds part from the timestamp
	var seconds = '0' + date.getSeconds();

	var formattedTime = hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
	$('#task-timer' + id).html(formattedTime);
	document.getElementById('running-task-' + id).innerHTML = formattedTime;
	$('.title').html(formattedTime);
}

function loadTaskActivities(formData) {
	$('#attach-card #section-loader').show();
	$.ajax({
		type: 'GET',
		url: timeTrackerBaseURL + 'index.php/user/load_task_data',
		data: formData,
		success: function(values) {
			$('#attach-card #section-loader').hide();
			var data = JSON.parse(values);
			$('#attach-card #activites-result').empty();
			drawCards(data);
		}
	});
}

function timeTo12HrFormat(time) {
	// Take a time in 24 hour format and format it in 12 hour format
	var time_part_array = time.split(':');
	var ampm = 'AM';

	if (time_part_array[0] >= 12) {
		ampm = 'PM';
	}
	if (time_part_array[0] > 12) {
		time_part_array[0] = time_part_array[0] - 12;
	}

	formatted_time = time_part_array[0] + ':' + time_part_array[1] + ' ' + ampm;

	return formatted_time;
}

var timerSlider = window.timerSlider || {};
timerSlider = {
	slider: null,
	reload: function() {
		this.slider.reloadSlider();
		this.slider.goToSlide(this.slider.getSlideCount() - 1);
	},
	init: function() {
		this.slider = $('#timer-slider').bxSlider({
			auto: false,
			infiniteLoop: false,
			controls: false
		});
	}
};

function clear_filter() {
	for (var i = 0; i < document.getElementsByClassName('switches').length; i++) {
		if (document.getElementsByClassName('switches')[i].checked == true) {
			document.getElementsByClassName('switches')[i].checked = false;
			$('.alert-filter').hide();
			$('.clear-filter').hide();
		}
	}
	loadTaskActivities({ type: 'date' });
}

$(document).ready(function() {
	if(document.getElementById('start-login-time'))
	{
		document.getElementById('start-login-time').value = moment().format('HH:mm a');
		$('#stop-now').modal({
			keyboard: false
		});
	}
	if (document.getElementById('previous-punch-in')) {
		$('#previous-punch-in').modal({
			keyboard: false
		});
	}
	if (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play') {
		document.getElementById('login-time').innerHTML = 'Punch in at 00:00:00';
	}

	
	if(document.getElementById('dashboard-filter'))
	{
		var filter_form = document.getElementById('dashboard-filter');
		filter_form.onsubmit = function()
		{
			var user_sorting = document.getElementById('sorting').getElementsByTagName('input');
			var user_filtering = document.getElementById('filtering').getElementsByTagName('input');
			var  sortBy = 'date';
			var filterBy = [];
			for(var i=0; i<user_sorting.length; i++)
			{
				if(user_sorting[i].checked == true)
				{
					sortBy = user_sorting[i].value;
					
				}
			}
			for(var i=0; i<user_filtering.length; i++)
			{
				if(user_filtering[i].checked == true)
				{
					filterBy.push(user_filtering[i+1].value);
				}
			}
			$('#clear-filter').show();
			loadTaskActivities({ "type": sortBy, "project_filter": JSON.stringify(filterBy) });
			$('#navbarToggleExternalContent').collapse('toggle');
			return false;
		}
	}
	$('#clear-filter').click(function(e)
	{
		e.preventDefault();
		for (var i = 0; i < document.getElementById('navbarToggleExternalContent').getElementsByTagName('input').length; i++) {
			if (document.getElementById('navbarToggleExternalContent').getElementsByTagName('input')[i].checked == true) {
				document.getElementById('navbarToggleExternalContent').getElementsByTagName('input')[i].checked = false;
				$('#clear-filter').hide();
			}
		}
		document.getElementById('sorting').getElementsByTagName('input')[0].checked = true
		loadTaskActivities({ type: "date" });
	});

	for (var i = 0; i < document.getElementById('filtering').getElementsByTagName('input').length; i++) {
		if (document.getElementById('filtering').getElementsByTagName('input')[i].checked == true) {
			$('#clear-filter').show();
		}
	}

	$('#stop-time').click(function() {
		if (stopped == 1) {
			$('#play-timer').modal('show');
			return false;
		}
		if (not_logged == 1) {
			$('#alert-punchin').modal('show');
			document.getElementById('start-login-time').value = moment().format('HH:mm');
			return false;
		}
		var _dateObj = new Date();
		var _hr = _dateObj.getHours();
		if (_hr.toString().length == 1) {
			_hr = '0' + _hr.toString();
		}

		var _mins = _dateObj.getMinutes();
		if (_mins.toString().length == 1) {
			_mins = '0' + _mins.toString();
		}

		if (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop') {
			timerSlider.slider.getCurrentSlide();
			var t_id = 0;
			var t_id = timerSlider.slider.getCurrentSlideElement()[0].id;
			var matches = t_id.match(/(\d+)/);
			if (timerSlider.slider.getCurrentSlide() != 0) {
				task_id = matches[0];
				timerSlider.slider.getCurrentSlideElement()[0].remove();
				document.getElementsByClassName('bx-pager-item')[1].remove();
				timerSlider.reload();
				if (task_id) {
					taskUrl = timeTrackerBaseURL + 'user/stop_timer';
				}
				var date = new moment().format('Y-MM-DD H:mm:ss');
				var server_stop_time = moment(date).tz('utc').format('Y-MM-DD H:mm:ss');
				$.ajax({
					type: 'POST',
					url: taskUrl,
					data: { action: 'task', id: task_id, time: server_stop_time },
					success: function(res) {
						var data = JSON.parse(res);
						var task_id_no = t_id.match(/(\d+)/);
						var action_play = $('<a href="' + timeTrackerBaseURL + 'user/start_timer?id=' + task_id_no[0] + '" class="card-action" data-id="' + task_id_no[0] + '" data-toggle="tooltip" data-placement="top" title="Play"></a>');
						action_play.append('<i class="fas action-edit  fa-play"></i>');

						document.getElementById('footer-right-' + task_id_no[0]).childNodes[0].remove();
						$('#footer-right-' + task_id_no[0]).append(action_play);

						var ele = $('#footer-right-' + task_id_no[0]).find('.fa-stop');
						ele.hide();
						$('#running-task-' + task_id).hide();
						document.getElementById('btn-stop' + task_id_no[0]).childNodes[0].remove();
						$('#btn-stop' + task_id_no[0]).append('<i class="far fa-clock"></i> ' + minutesToTime(data['flag']['details']['t_minutes']));
						$('#action-play' + task_id_no[0]).css('display', 'block');
						if (timerSlider.slider.getSlideCount() == 1) {
							$('.bx-pager-item').css('display', 'none');
						}
						timerSlider.reload();
					}
				});
			} else {
				if (t_id == '' || t_id == ' ') {
					$('#pause-action').modal('show');
				} else {
					localStorage.setItem('task_id', t_id);
				}
			}
		} else {
			var curr_timeStamp = Math.floor(Date.now() / 1000);
			login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
			if (typeof login_timer != 'undefined') {
				if (login_timer == parseInt(login_timer)) {
					$('#play-timer').modal('show');
				}
			}
		}
	});
	var curr_timeStamp = Math.floor(Date.now() / 1000);
	if (typeof __timeTrackerLoginTime !== 'undefined' && document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop') {
		login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
		if (typeof login_timer != 'undefined') {
			if (login_timer == parseInt(login_timer)) {
				startTimer(login_timer);
			}
		}
	}
	var x = document.getElementsByClassName('task-slider');
	for (var i = 0; i < x.length; i++) {
		var __timeTrackerTaskTime = x[i].childNodes[1].value;
		__timeTrackerTaskTimeNew = parseInt(curr_timeStamp) - parseInt(__timeTrackerTaskTime);
		if (typeof __timeTrackerTaskTimeNew != 'undefined' && __timeTrackerTaskTimeNew !== 0) {
			if (__timeTrackerTaskTimeNew == parseInt(__timeTrackerTaskTimeNew) && document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop') {
				start_task_timer(__timeTrackerTaskTimeNew, x[i].childNodes[1].id);
			}
		}
	}

	if ($('#attach-card').length > 0) {
		loadTaskActivities({ type: 'date' });
	}

	timerSlider.init();

	if (typeof timerSlider.slider.getSlideCount !== 'undefined' && timerSlider.slider.getSlideCount() == 1) {
		$('.bx-pager-item').css('display', 'none');
	}
	$('.timerpicker-c').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		uiLibrary: 'bootstrap4'
	});
	$('.timerpicker-stop-now').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		uiLibrary: 'bootstrap4'
	});
	$(function() {
		$('.stopnow-time').timepicker({
			mode: '24hr',
			format: 'HH:MM',
			useCurrent: false
		});
	});
	if (document.getElementById('update-stop-now')) {
		var stop_now = document.getElementById('update-stop-now');
		var __element = document.getElementById('update-stop-now');
		stop_now.onsubmit = function() {
			var stop_now = document.getElementById('stop-end-time').value;
			if (stop_now == ' ' || stop_now == '') {
				document.getElementById('punchout-error').innerHTML = 'enter valid end time. ';
				return false;
			} else {
				var input_element = __element.getElementsByClassName('check-for-utc');
				for (var i = 0; i < input_element.length; i++) {
					if (input_element[i].value != '' && input_element[i].value != ' ') {
						var serverDate = moment(document.getElementById('previous-date').value.slice(0, 10) + ' ' + input_element[i].value).tz('utc').format('Y-MM-DD H:mm:ss');
						input_element[i].value = serverDate;

						if (typeof parseInt(serverDate.slice(0, 2)) == 'string') {
							document.getElementById('punchout-error').innerHTML = 'enter valid end time. ';
							return false;
						}
					}
				}
				return true;
			}
		};
	}
	if (document.getElementById('update-punch-in')) {
		var punchout = document.getElementById('update-punch-in');
		var __element = document.getElementById('update-punch-in');
		punchout.onsubmit = function() {
			var stop_now = document.getElementById('punchout-time').value;
			if (stop_now == ' ' || stop_now == '') {
				document.getElementById('punchout-error').innerHTML = 'enter valid end time. ';
				return false;
			} else {
				var input_element = __element.getElementsByClassName('check-for-utc');
				for (var i = 0; i < input_element.length; i++) {
					if (input_element[i].value != '' && input_element[i].value != ' ') {
						var serverDate = moment(document.getElementById('previous-punchout').value.slice(0, 10) + ' ' + input_element[i].value).tz('utc').format('Y-MM-DD H:mm:ss');
						input_element[i].value = serverDate;

						if (typeof parseInt(serverDate.slice(0, 2)) == 'string') {
							document.getElementById('punchout-error').innerHTML = 'enter valid end time. ';
							return false;
						}
					}
				}
				var login_id = document.getElementById('login-id').value;
				$.ajax({
					type: 'POST',
					url: timeTrackerBaseURL + 'user/update_end_time',
					data: { action: 'previous', id: login_id, time: serverDate },
					success: function(res) {
						window.location.reload();
					}
				});
			}
			return false;
		};
	}

	if (document.getElementById('starting-timer')) {
		var startingTimer = document.getElementById('starting-timer');
		startingTimer.onsubmit = function() {
			var startTime = document.getElementById('start-login-time').value;
			var t_day = moment().format('YYYY-MM-DD');
			startTime = moment(t_day + ' ' + startTime).format('HH:mm');
			document.getElementById('start-login-time').value = startTime;
			if (startTime == 'Invalid date') {
				document.getElementById('stop-timer-error').innerHTML = 'Please enter valid time';
				return false;
			}
			if (startTime == '' || startTime == ' ') {
				document.getElementById('stop-timer-error').innerHTML = 'Please enter login time';
				return false;
			} else {
				document.getElementById('stop-timer-error').innerHTML = ' ';
				var currentTime = new Date().getHours() * 60 + new Date().getMinutes();
				var enteredTime = parseInt(startTime.toString().slice(0, 2) * 60) + parseInt(startTime.toString().slice(3, 5));
				if (currentTime < enteredTime) {
					document.getElementById('stop-timer-error').innerHTML = 'Login time cannot be greater than current time';
					return false;
				} else if (stopped == 1) {
					$('#play-timer').modal('show');
					return false;
				} else {
					$('#icon-for-task').removeClass('fa-play');
					$('#icon-for-task').addClass('fa-stop');
					var input_element = startingTimer.getElementsByClassName('check-for-utc');
					for (var i = 0; i < input_element.length; i++) {
						if (input_element[i].value != '' && input_element[i].value != ' ') {
							var server_start_time = moment(new Date().getFullYear() + '-' + (parseInt(new Date().getMonth()) + 1) + '-' + new Date().getDate() + ' ' + input_element[i].value).tz('utc').format('YYYY-MM-DD HH:mm:ss');
							input_element[i].value = server_start_time;
						}
					}
					startTimer(login_timer);
					return true;
				}
			}
			return false;
		};
	}

	var changeImage = document.getElementById('upload-image');
	if (changeImage) {
		changeImage.onsubmit = function(e) {
			var image = document.getElementById('image').value;
			if (image == '' || image == ' ') {
				document.getElementById('imageerror').innerHTML = 'Choose an image';
				return false;
			} else return true;
		};
	}
	// if (document.getElementById('punch-out')) {
	// 	$('#punch-out').on('click', function(e) {
	// 		var pounchOutTime = moment().tz('utc').format('Y-MM-DD HH:mm:ss');
	// 		$.ajax({
	// 			type: 'POST',
	// 			url: timeTrackerBaseURL + 'user/update_end_time',
	// 			data: { punch_out_time: pounchOutTime },
	// 			success: function(res) {
	// 				document.getElementById('stop-time').childNodes[1].childNodes[0].classList.remove('fa-stop');
	// 				document.getElementById('stop-time').childNodes[1].childNodes[0].classList.add('fa-play');
	// 				document.getElementById('stop-time').removeEventListener('click');
	// 			}
	// 		});
	// 	});
	// }

	//check for existing running tasks
	$.ajax({
		type: 'POST',
		url: timeTrackerBaseURL + 'index.php/user/get_running_task',
		dataType: 'json',
		success: function(res) {
			$('#stop-now').modal('show');
		}
	});
});
