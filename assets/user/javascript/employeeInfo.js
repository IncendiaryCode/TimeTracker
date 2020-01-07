//main timer interval for login
var mainTimerInterval;

function startTimer(startTime) {
	if (startTime === "stop") {
		//clear the existing interval
		clearInterval(mainTimerInterval);
	} else {
		//set in local storage
		localStorage.setItem("timeStamp", startTime);
		mainTimerInterval = setInterval(function() {
			startTime++;
			setTime(startTime);
		}, 1000);
	}
}

function setTime(startTime) {
	//update local storage
	localStorage.setItem("timeStamp", startTime);

	var date = new Date(startTime * 1000);
	// Hours part from the timestamp
	var hours = "0" + date.getHours();
	// Minutes part from the timestamp
	var minutes = "0" + date.getMinutes();
	// Seconds part from the timestamp
	var seconds = "0" + date.getSeconds();

	var formattedTime =
		hours.substr(-2) + ":" + minutes.substr(-2) + ":" + seconds.substr(-2);

	$("#primary-timer").html(formattedTime);
}

function addZeroBefore(n) {
	return (n < 10 ? "0" : "") + n;
}
var changeImage = document.getElementById("uploadImage");
if (changeImage) {
	changeImage.onsubmit = function(e) {
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

function updateTimer(flag, id) {
	$.ajax({
		type: "POST",
		url: timeTrackerBaseURL + "index.php/user/stop_timer",
		data: { action: "task", id: id, flag: flag },
		//call to stop the task timer.
		dataType: "json",
		success: function(res) {
			//handle timer
			var time_details = res["flag"]["details"];
			var total_time = minutesToTime(time_details["total_minutes"]);

			//window.location.reload();
			document.getElementById("slider" + id).remove();
			document.getElementsByClassName("bx-pager-item")[1].remove();
			//update total time of the card...

			document.getElementById("btn-stop" + id).childNodes[0].remove();
			$("#btn-stop" + id).append(total_time);

			//var actionPlay = $('<a href="#" class="card-action action-delete text-white" id="action-play'+id+'"><div class="text-center shadow-lg" data-tasktype="login"><i class="fas action-icon position_play_icon fa-play" data-toggle="tooltip" data-placement="top" title="Resume"></i></div></a>');

			var actionPlay = $(
				'<a href="' +
					timeTrackerBaseURL +
					"user/start_timer?id=" +
					id +
					'" class="card-action action-delete text-white" id="action-play-' +
					id +
					'"/>'
			);
			actionPlay.append(
				'<i class="fas action-icon position_play_icon fa-play" data-toggle="tooltip" data-placement="top" title="Resume"></i>'
			);

			$("#footerRight" + id).append(actionPlay);
            var stop_btn;
            
            //TODO:: Remove this..attach click event only once while loading task initially
			
		}
	});
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
		success: function(values) {
			console.log(values);
			var data = JSON.parse(values);
			$("#attach-card").empty();
			/*var timerModal = timerStopModal();*/
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
								'<span class="fa fa-hourglass-1"><i class=""></i> Running</span>'
							).data("taskid", data[x][y].id);
							
						}
						stopCol.append(stopButton);
					}

					cardHeaderRow.append(stopCol);
					cardHeader.append(cardHeaderRow);

					var cardInner = $(
						"<div class='card card-style-1 animated fadeInUp content-overlay'  />"
					);
					cardInner.append(cardHeader);

					var cardBody = $("<div class='card-body' />");
					cardBody.append(data[x][y].task_name);
					cardInner.append(cardBody);
					var cardFooter = $("<div class='card-footer card-footer'>");
					var footerRow = $('<div class="row" />');
					footerRow.append(
						"<div class='col-6'> <img src=" +
							data[x][y].image_name +
							" width='20px;' alt=''> " +
							data[x][y].project +
							"</div>"
					);

					var footerRight = $(
						"<div class='col-6 text-right card-actions' id='footer-right-" +
							data[x][y].id +
							"'>"
					);

					var actionPlay = $(
						'<a href="' +
							timeTrackerBaseURL +
							"user/start_timer?id=" +
							data[x][y].id +
							'" class="card-action action-delete content-details" data-id="' +
							data[x][y].id +
							'" data-toggle="tooltip" data-placement="top" title="Play"></a>'
					);
					actionPlay.append(
						'<i class="fas action-icon position_play_icon fa-play"></i>'
					);
					var actionStop = $(
						'<a href="' +
							timeTrackerBaseURL +
							"user/stop_timer?id=" +
							data[x][y].id +
							'" class="card-action action-delete content-details" data-id="' +
							data[x][y].id +
							'" data-toggle="tooltip" data-placement="top" title="Stop"></a>'
					);

					actionStop.append(
						'<i class="fas action-icon position_play_icon fa-stop"></i>'
					);
					if (data[x][y].running_task == 0) {
						footerRight.append(actionPlay);
					}
					else{
						footerRight.append(actionStop);
					}

					actionPlay.on("click", function(e) {
						e.preventDefault();
						var t_id = $(this).data("id");
						$.ajax({
							type: "POST",
							url: timeTrackerBaseURL + "index.php/user/start_timer",
							data: { action: "task", id: t_id },
							dataType: "json",
							success: function(res) {
								//TODO:: show stop icon
								//add task to slider
								//TODO:: fetch task information from server
								var row = $(
									'<div id="slider-' +
										res.id +
										'">' +
										'<div class="section-slider task-slider">' +
										'<input type="hidden" id="' +
										res.id +
										'" value="' +
										res.time +
										'">' +
										'<input type="hidden" id="id-' +
										res.id +
										'" value="' +
										res.id +
										'">' +
										'<p class="font-weight-light time-font text-center login-time">' +
										"Started at " +
										res.time +
										" </p>" +
										'<div class="font-weight-light text-center primary-timer start-task-timer" data-type="" data-time="">00:00:00</div>' +
										'<p class="font-weight-light text-center taskName">' +
										res.name +
										"</p>" +
										"</div>" +
										"</div>"
								);

								var stopButton = $(
								'<span class="fa fa-hourglass-1"><i class=""></i> Running</span>'
							).data("taskid", t_id);
							
								document.getElementById("btn-stop"+t_id).childNodes[0].remove();
								document.getElementById("btn-stop"+t_id).childNodes[0].remove();
								$('#btn-stop'+t_id).append(stopButton);
								document.getElementById("footer-right-"+t_id).childNodes[0].remove();
								$("#footer-right-"+t_id).append(actionStop);

								$("#timer-slider").append(row);
								timerSlider.reload();

							}
						});
					});



					actionStop.on("click", function(e) {
						e.preventDefault();
						var task_id = $(this).data("id");
						$.ajax({
							type: "POST",
							url: timeTrackerBaseURL + "index.php/user/stop_timer",
							data: { action: "task", id: id, flag: '0' },
							dataType: "json",
							success: function(res) {
								//TODO:: show stop icon

								document.getElementById("footer-right-"+task_id).childNodes[0].remove();
								$("#footer-right-"+task_id).append(actionPlay);
								
								console.log(res)
								/*var timeUsed = minutesToTime(data[x][y].t_minutes);*/

								document.getElementById("btn-stop"+task_id).childNodes[0].remove();
								$('#btn-stop'+task_id).append('<i class="far fa-clock"></i> ' +"12h:50m");
								$("#action-play" + task_id).css("display", "block");
								document.getElementById("slider" + task_id).remove();
								document.getElementsByClassName("bx-pager-item")[1].remove();
							}
						});
					});

					//action Edit
					var actionEdit = $(
						'<a href="#" class="card-action pl-2 action-edit text-white content-details " id="action-edit"><i class="far fa-edit position_edit_icon" data-toggle="tooltip" data-placement="top" title="edit"></i></a>'
					);
					actionEdit.attr(
						"href",
						timeTrackerBaseURL +
							"index.php/user/load_edit_task?t_id=" +
							data[x][y].id
					);

					
					footerRight.append(actionEdit);

					footerRow.append(footerRight);
					cardFooter.append(footerRow);
					cardInner.append(cardFooter);
					var cardCol = $("<div class='col-lg-6 mb-4 cardCol content' />");
					cardCol.append(cardInner);
					$("#attach-card").append(cardCol);
					if (data[x][y].running_task == 1 && data[x][y].start_time != null) {
						//change background of current running task entries.
						document.getElementsByClassName("title").innerText +=
							data[x][y].task_name;
					}
				}
			}
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
		//set in local storage
		localStorage.setItem("timeStamp", startTime);
		mainTaskInterval = setInterval(function() {
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

	var formattedTime =
		hours.substr(-2) + ":" + minutes.substr(-2) + ":" + seconds.substr(-2);

	$("#task-timer" + id).html(formattedTime);
	$(".title").html(formattedTime);
}

var oldEndTime = document.getElementById("update-endtime");
if (oldEndTime) {
	oldEndTime.onsubmit = function() {
		var oldTime = document.getElementById("old-datepicker").value;
		var start_date = document.getElementById("old-start-date").textContent;
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
	reload: function() {
		this.slider.reloadSlider();
		this.slider.goToSlide(this.slider.getSlideCount() - 1);
	},
	init: function() {
		this.slider = $("#timer-slider").bxSlider({
			auto: false,
			infiniteLoop: false,
			controls: false
		});
	}
};

$(document).ready(function() {
	$("#stop-time").click(function() {
		var t_id = 0;

		var curr_element = document.getElementsByClassName("bx-pager");
		var x = curr_element[0].childNodes;
		for (var i = 0; i < x.length; i++) {
			var className = x[i]["lastChild"];
			var len = className.classList.length;
			if (len == 2) {
				var scroll_num = i + 1;
				var scroll_element = document.getElementById("timer-slider");
				if (scroll_num == 1) {
					$("#pause-action").modal("show");
				} else {
					var ele = scroll_element.children[scroll_num];
					var taskid = document.getElementById("id" + i).value;
					t_id = taskid;
				}
			}
		}

		if ($(this).data("tasktype") == "task") {
			var taskUrl = timeTrackerBaseURL + "index.php/user/start_timer";
			if (t_id) {
				taskUrl = timeTrackerBaseURL + "index.php/user/stop_timer";
			}
			$.ajax({
				type: "POST",
				url: taskUrl,
				data: { action: "task", id: t_id },
				success: function(res) {
					document.getElementById("alarmmsg").innerHTML = res["msg"];
					setTimeout(function() {
						document.getElementById("alarmmsg").innerHTML = "";
					}, 5000);
				}
			});
		} else {
			if (t_id == "" || t_id == " ") {
				$("#pause-action").modal("show");
			} else {
				localStorage.setItem("task_id", t_id);
				/*                var timerModal = timerStopModal();*/
				//timerModal.modal('show');
				updateTimer(0);
			}
		}
	});

	var curr_timeStamp = Math.floor(Date.now() / 1000);
	login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
	if (typeof login_timer != "undefined") {
		if (login_timer == parseInt(login_timer)) {
			startTimer(login_timer);
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
			if (__timeTrackerTaskTimeNew == parseInt(__timeTrackerTaskTimeNew)) {
				start_task_timer(__timeTrackerTaskTimeNew, x[i].childNodes[1].id);
			}
		}
	}

	if ($("#attach-card").length > 0) {
		loadTaskActivities({ type: "task" });
	}

	$("#dropdown-recent-acts").on("show.bs.dropdown", function(e) {
		var anchors = $(e.currentTarget).find("a.dropdown-item");
		anchors.unbind("click").on("click", function(e) {
			e.preventDefault();
			loadTaskActivities({ type: $(this).data("type") });
		});
	});

	timerSlider.init();
});
