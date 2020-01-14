//main timer interval for login
var mainTimerInterval;

function startTimer(startTime) {
	if (startTime === "stop") {
		//clear the existing interval
		clearInterval(mainTimerInterval);
	} else {
		//set in local storage
		localStorage.setItem("timeStamp", startTime);
		mainTimerInterval = setInterval(function () {
			startTime++;
			setTime(startTime);
		}, 1000);
	}
}

function setTime(startTime) {

	localStorage.setItem("timeStamp", startTime);

	var date = new Date(startTime * 1000);
	// Hours part from the timestamp
	var hours = "0" + date.getHours();
	// Minutes part from the timestamp
	var minutes = "0" + date.getMinutes();
	// Seconds part from the timestamp
	var seconds = "0" + date.getSeconds();

	var formattedTime = hours.substr(-2) + ":" + minutes.substr(-2) + ":" + seconds.substr(-2);

	$("#primary-timer").html(formattedTime);
}

function addZeroBefore(n) {
	return (n < 10 ? "0" : "") + n;
}
var changeImage = document.getElementById("uploadImage");
if (changeImage) {
	changeImage.onsubmit = function (e) {
		var image = document.getElementById("image").value;
		if (image == "" || image == " ") {
			document.getElementById("imageerror").innerHTML = "Choose an image";
			return false;
		} else return true;
	};
}
function minutesToTime(mins) {
	var total_mins = Number(mins * 60);
	var h = Math.floor(total_mins / 3600);
	var m = Math.floor((total_mins % 3600) / 60);

	var hDisplay = h > 0 ? h + (h == 1 ? " h. " : "h:") : "";
	var mDisplay = m > 0 ? m + (m == 1 ? " m. " : "m.") : "";
	return hDisplay + mDisplay;
}
function getTime() {
	var timeLogout = new Date();
	var logoutTime =
		timeLogout.getFullYear() +
		"-" +
		(timeLogout.getMonth() + 1) +
		"-" +
		timeLogout.getDate();

	var date =
		timeLogout.getFullYear() +
		"-" +
		(timeLogout.getMonth() + 1) +
		"-" +
		timeLogout.getDate();
	return date;
}

function drawCards(data) {
	for (x in data) {
		for (var y = 0; y < data[x].length; y++) {
			var cardHeader = $('<div class="card-header card-header" />');
			var cardHeaderRow = $('<div class="row pt-2" />');
			var today = getTime();
			if (data[x][y].start_time != null) {
				var task_date = data[x][y].start_time.slice(0, 10);
				if (today != task_date) {
					$(".alert-box").show();
				}
			}
			if (data[x][y].start_time == null) {
				cardHeaderRow.append(
					'<div class="col-6 text-left"><span class="vertical-line"></span>Not yet started.</div>'
				);
			} else {
				cardHeaderRow.append(
					'<div class="col-6 text-left"><span class="vertical-line"></span>' +
					" " +
					data[x][y].start_time +
					"</div>"
				);
			}
			var stopCol = $(
				'<div class="col-6 text-right"  id="btn-stop' +
				data[x][y].id +
				'" />'
			);
			if (data[x][y].running_task == 0) {
				/*check whether task is ended or not*/
				var timeUsed = minutesToTime(data[x][y].t_minutes);
				stopCol.append('<i class="far fa-clock"></i> ' + timeUsed);
			} else {
				var id = data[x][y].id;
				if (data[x][y].start_time != null) {
					var stopButton = $(
						'<span class=""><i class="fa fa-hourglass-1"></i> Running</span>'
					).data("taskid", data[x][y].id);
				}
				stopCol.append(stopButton);
			}

			cardHeaderRow.append(stopCol);
			cardHeader.append(cardHeaderRow);

			var cardInner = $(
				"<div class='card card-style-1 animated fadeIn' />"
			);
			cardInner.append(cardHeader);

			var cardBody = $("<div class='card-body' />");
			cardBody.append(data[x][y].task_name);
			cardInner.append(cardBody);
			var cardFooter = $("<div class='card-footer card-footer'>");
			var footerRow = $('<div class="row" />');

			footerRow.append(((data[x][y].image_name !== null) ?
				"<div class='col-12'> <img src=" +
				data[x][y].image_name +
				" width='20px;'> " : '') +
				data[x][y].project +
				"</div>"
			);

			var footerRight = $(
				"<div class='card-actions' id='footer-right-" +
				data[x][y].id +
				"'>"
			);
			var actionPlay = $(
				'<a href="' +
				timeTrackerBaseURL +
				"user/start_timer?id=" +
				data[x][y].id +
				'" class="card-action action-delete" data-id="' +
				data[x][y].id +
				'" data-toggle="tooltip" data-placement="top" title="Play"></a>'
			);
			actionPlay.append(
				'<i class="fas action-edit  fa-play"></i>'
			);
			var actionStop = $(
				'<a href="' +
				timeTrackerBaseURL +
				"user/stop_timer?id=" +
				data[x][y].id +
				'" class="card-action action-delete" data-id="' +
				data[x][y].id +
				'" data-toggle="tooltip" data-placement="top" title="Stop"></a>'
			);

			actionStop.append(
				'<i class="fas action-edit fa-stop"></i>'
			);
			if (data[x][y].running_task == 0) {
				footerRight.append(actionPlay);
			}
			else {
				footerRight.append(actionStop);
			}

			actionPlay.on("click", function (e) {
				e.preventDefault();
				var t_id = $(this).data("id");
				if(document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play')
				{
					$('#play-timer').modal("show");
				}
				else
				{
				$.ajax({
					type: "POST",
					url: timeTrackerBaseURL + "index.php/user/start_timer",
					data: { action: "task", id: t_id },
					dataType: "json",
					success: function (res) {
						var res = res['data']['details'];
						var startDateTime = new Date().getHours()+':'+ new Date().getMinutes()+':'+new Date().getSeconds();
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
							'<p class="font-weight-light time-font text-center login-time" id="start-time' + res['task_id'] + '">' +
							"Started at " +
							startDateTime +
							" </p>" +
							'<div class="font-weight-light text-center primary-timer start-task-timer" id="task-timer' + res['task_id'] + '" data-type="" data-time="">00:00:00</div>' +
							'<p class="font-weight-light text-center taskName">' +
							res['task_name'] +
							"</p>" +
							"</div>" +
							"</div>"
						);
						var stopButton = $(
							'<span class=""><i class="fa fa-hourglass-1"></i> Running</span>'
						).data("taskid", t_id);


						var icon_tag = document.getElementById("footer-right-" + t_id).childNodes[0];

						document.getElementById("btn-stop" + t_id).childNodes[0].remove();
						document.getElementById("btn-stop" + t_id).childNodes[0].remove();
						$('#btn-stop' + t_id).append(stopButton);
						
						document.getElementById('footer-right-'+t_id).childNodes[0].remove();

						var action_stop = $(
								'<a href="' +
								timeTrackerBaseURL +
								"user/stop_timer?id=" +
								t_id +
								'" class="card-action action-delete" data-id="' +
								t_id +
								'" data-toggle="tooltip" data-placement="top" title="Stop"></a>'
							);

							action_stop.append(
								'<i class="fas action-edit fa-stop"></i>'
							);
						$('#footer-right-'+t_id).append(action_stop)
						
						$("#timer-slider").append(row);
						timerSlider.reload();
						document.getElementsByClassName("title").innerText +=
						start_task_timer(-19800, t_id);
					}
				});
				}
			});

			actionStop.on("click", function (e) {
				e.preventDefault();
				var task_id = $(this).data("id");
				if(document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play')
				{
					$('#play-timer').modal("show");
				}
				else
				{
				$.ajax({
					type: "POST",
					url: timeTrackerBaseURL + "index.php/user/stop_timer",
					data: { action: "task", id: id, flag: '0' },
					dataType: "json",
					success: function (res) {
						var data = res['flag'];
						document.getElementById("footer-right-" + task_id).childNodes[0].remove();
						
						var action_play = $(
							'<a href="' +
							timeTrackerBaseURL +
							"user/start_timer?id=" +
							task_id +
							'" class="card-action action-delete" data-id="' +
							task_id +
							'" data-toggle="tooltip" data-placement="top" title="Play"></a>'
						);
						action_play.append(
							'<i class="fas action-edit  fa-play"></i>'
						);
						$('#footer-right-'+task_id).append(action_play)
						document.getElementById("btn-stop" + task_id).childNodes[0].remove();
						$('#btn-stop' + task_id).append('<i class="far fa-clock"></i> ' + minutesToTime(data['details']['t_minutes']));
						$("#action-play" + task_id).css("display", "block");

						document.getElementById("slider" + task_id).remove();
						if (timerSlider.slider.getSlideCount() == 1) {
							$('.bx-pager-item').css('display', "none");
						}
						timerSlider.reload();
					}
				});
				}
			});
			//action Edit
			var actionEdit = $(
				'<a href="#" class=" pl-2  text-white " id="action-edit"><i class="far fa-edit action-play" data-toggle="tooltip" data-placement="top" title="edit"></i></a>'
			);
			actionEdit.attr(
				"href",
				timeTrackerBaseURL +
				"index.php/user/load_add_task?t_id=" +
				data[x][y].id
			);
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

			var cardCol = $("<div class='col-lg-6 mb-4 card-col' />");
			cardCol.append(cardInner);

			$("#attach-card").append(cardCol);
			if (data[x][y].running_task == 1 && data[x][y].start_time != null) {
				//change background of current running task entries.
				cardInner.css('background-image','linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
				cardHeader.css('background-image','linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
				cardFooter.css('background-image','linear-gradient(to right, #fff, #f4f4f8, #f0f1f6)');
				document.getElementsByClassName("title").innerText +=
					data[x][y].task_name;
			}
		}
	}
}
function loadTaskActivities(formData) {
	$("#attach-card")
		.empty()
		.html(
			'<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>'
		);
	$.ajax({
		type: "GET",
		url: timeTrackerBaseURL + "index.php/user/load_task_data",
		data: formData,
		success: function (values) {
			var data = JSON.parse(values);
			$("#attach-card").empty();
			drawCards(data);
		}
	});
}
function timeTo12HrFormat(time) {
	// Take a time in 24 hour format and format it in 12 hour format
	var time_part_array = time.split(":");
	var ampm = "AM";

	if (time_part_array[0] >= 12) {
		ampm = "PM";
	}
	if (time_part_array[0] > 12) {
		time_part_array[0] = time_part_array[0] - 12;
	}

	formatted_time = time_part_array[0] + ":" + time_part_array[1] + " " + ampm;

	return formatted_time;
}

var mainTaskInterval;

function start_task_timer(startTime, id) {

	if (startTime === "stop") {
		//clear the existing interval
		clearInterval(mainTaskInterval);
	} else {
		localStorage.setItem("timeStamp", startTime);
			mainTaskInterval = setInterval(function () {
			startTime++;
			setTaskTime(startTime, id);
		}, 1000);
	}
}

function setTaskTime(startTime, id) {
	//update local storage
	localStorage.setItem("timeStamp", startTime);

	var date = new Date(startTime * 1000);
	// Hours part from the timestamp
	var hours = "0" + date.getHours();
	// Minutes part from the timestamp
	var minutes = "0" + date.getMinutes();
	// Seconds part from the timestamp
	var seconds = "0" + date.getSeconds();

	var formattedTime = hours.substr(-2) + ":" + minutes.substr(-2) + ":" + seconds.substr(-2);

	$("#task-timer" + id).html(formattedTime);
	$(".title").html(formattedTime);
}

var oldEndTime = document.getElementById("update-endtime");
if (oldEndTime) {
	oldEndTime.onsubmit = function () {
		var oldTime = document.getElementById("old-datepicker").value;
		var start_date = document.getElementById("old-start-date").tex;
		start_date = start_date.trim().slice(0, 10);
		var old_date = oldTime.slice(0, 10);

		if (oldTime == "" || oldTime == " ") {
			document.getElementById("old-date-error").innerHTML =
				"Please enter correct end time.";
			return false;
		} else if (start_date != old_date) {
			document.getElementById("old-date-error").innerHTML =
				"Entered date is not matching...";
			return false;
		}
		return true;
	};
}

var timerSlider = window.timerSlider || {};
timerSlider = {
	slider: null,
	reload: function () {
		this.slider.reloadSlider();
		this.slider.goToSlide(this.slider.getSlideCount() - 1);
	},
	init: function () {
		this.slider = $("#timer-slider").bxSlider({
			auto: false,
			infiniteLoop: false,
			controls: false
		});
	}
};

$(document).ready(function () {
	$("#stop-time").click(function () {
		if(document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop')
		{
			timerSlider.slider.getCurrentSlide();
			var t_id = 0;
			var t_id = timerSlider.slider.getCurrentSlideElement()[0].id;
			var matches = t_id.match(/(\d+)/);
			if (timerSlider.slider.getCurrentSlide() != 0) {
				task_id = matches[0];
				timerSlider.slider.getCurrentSlideElement()[0].remove();
				document.getElementsByClassName("bx-pager-item")[1].remove();
				timerSlider.reload();
				if (task_id) {
					taskUrl = timeTrackerBaseURL + "index.php/user/stop_timer";
				}
				$.ajax({
					type: "POST",
					url: taskUrl,
					data: { action: "task", id: task_id },
					success: function (res) {
						var data = JSON.parse(res);
						var task_id_no = t_id.match(/(\d+)/);
						var action_play = $(
								'<a href="' +
								timeTrackerBaseURL +
								"user/start_timer?id=" +
								task_id_no[0] +
								'" class="card-action action-delete" data-id="' +
								task_id_no[0] +
								'" data-toggle="tooltip" data-placement="top" title="Play"></a>'
							);
							action_play.append(
								'<i class="fas action-edit  fa-play"></i>'
							);
							document.getElementById('footer-right-'+task_id_no[0]).childNodes[0].remove();
							$('#footer-right-'+task_id_no[0]).append(action_play)
							document.getElementById("btn-stop" + task_id_no[0]).childNodes[0].remove();
							$('#btn-stop' + task_id_no[0]).append('<i class="far fa-clock"></i> ' + minutesToTime(data['flag']['details']['t_minutes']));
							$("#action-play" + task_id_no[0]).css("display", "block");
							if (timerSlider.slider.getSlideCount() == 1) {
								$('.bx-pager-item').css('display', "none");
							}
							timerSlider.reload();
						}
					});
				} else {
					if (t_id == "" || t_id == " ") {
						$("#pause-action").modal("show");
					} else {
						localStorage.setItem("task_id", t_id);
					}
				}
			}else
			{
			var curr_timeStamp = Math.floor(Date.now() / 1000);
				login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
				if (typeof login_timer != "undefined") {
					if (login_timer == parseInt(login_timer)) {
						$("#play-timer").modal("show");
					}
				}
			$('#icon-for-task').removeClass("fa-play");
			$('#icon-for-task').addClass("fa-stop");
			}
		});
	var curr_timeStamp = Math.floor(Date.now() / 1000);
	if (__timeTrackerLoginTime && (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop')) {
		login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
		if (typeof login_timer != "undefined") {
			if (login_timer == parseInt(login_timer)) {
				startTimer(login_timer);
			}
		}
	}
	var x = document.getElementsByClassName("task-slider");
	for (var i = 0; i < x.length; i++) {
		var __timeTrackerTaskTime = x[i].childNodes[1].value;

		__timeTrackerTaskTimeNew =
			parseInt(curr_timeStamp) - parseInt(__timeTrackerTaskTime);
		if (
			typeof __timeTrackerTaskTimeNew != "undefined" &&
			__timeTrackerTaskTimeNew !== 0
		) {
			if (__timeTrackerTaskTimeNew == parseInt(__timeTrackerTaskTimeNew) &&  (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-stop')) {
				start_task_timer(__timeTrackerTaskTimeNew, x[i].childNodes[1].id);
			}
		}
	}

	if ($("#attach-card").length > 0) {
		loadTaskActivities({ type: "task" });
	}

	$("#dropdown-recent-acts").on("show.bs.dropdown", function (e) {
		var anchors = $(e.currentTarget).find("a.dropdown-item");
		anchors.unbind("click").on("click", function (e) {
			e.preventDefault();
			loadTaskActivities({ type: $(this).data("type") });
		});
	});

	timerSlider.init();

	if (timerSlider.slider.getSlideCount() == 1) {
		$('.bx-pager-item').css('display', "none");
	}
	$('.timerpicker-c').timepicker({
        uiLibrary: 'bootstrap4'
    });
	if (document.getElementById('update-stop-now')) {
		var stop_now = document.getElementById('update-stop-now');
		stop_now.onsubmit = function () {
			var stop_now = document.getElementById('stop-end-time').value;
			if (stop_now == ' ' || stop_now == "") {
				document.getElementById('stop-now-error').innerHTML = "enter valid end time. "
				return false;
			}
			else {
				return true;
			}
		}
	}

	if(document.getElementById('starting-timer'))
	{
		var startingTimer = document.getElementById('starting-timer');
		startingTimer.onsubmit = function()
		{
			var startTime = document.getElementById('start-login-time').value;
			if(startTime == "" || startTime == " ")
			{
				document.getElementById('stop-timer-error').innerHTML = "Please enter login time";
				return false;
			}
			else
			{
				document.getElementById('stop-timer-error').innerHTML = " ";
				var currentTime = (new Date().getHours())*60 + (new Date().getMinutes());
				var enteredTime = parseInt(startTime.toString().slice(0,2)*60) + parseInt(startTime.toString().slice(3,5));
				if(currentTime < enteredTime)
				{
					document.getElementById('stop-timer-error').innerHTML = "Login time cannot be greater than current time";
					return false;
				}
				else
				{
					startTimer(login_timer);
					return true;
				}
			}return false;
		}
	}

});
