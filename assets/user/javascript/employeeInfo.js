var minutesLabel = document.getElementById("minutes");
var secondsLabel = document.getElementById("seconds");
var hoursLabel = document.getElementById("hours");
var totalSeconds = 0;
var totalMinuts = 0;
var totalHours = 0;
var totalWorkTime = 0;
var pauseCount = 0;
var storing;

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
    //update local storage
    localStorage.setItem('timeStamp', startTime);

    var date = new Date(startTime * 1000);
    // Hours part from the timestamp
    var hours = "0" + date.getHours();
    // Minutes part from the timestamp
    var minutes = "0" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "0" + date.getSeconds();

    var formattedTime = hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

    $('#primary-timer').html(formattedTime);
}

function addZeroBefore(n) {
    return (n < 10 ? '0' : '') + n;
}
var changeImage = document.getElementById('uploadImage');
if (changeImage) {
    changeImage.onsubmit = function(e) {
        var image = document.getElementById('image').value;
        if (image == "" || image == " ") {
            document.getElementById('imageerror').innerHTML = "Choose an image";
            return false;
        } else
            return true;
    }
}

function updateTimer() {
    console.log(localStorage.getItem('task_id'));
    $.ajax({
        type: "POST",
        url: timeTrackerBaseURL + 'index.php/user/stop_timer',
        data: { 'action': 'task', 'id': localStorage.getItem('task_id') },
        /*call to stop the task timer.*/
        dataType: 'json',
        success: function(res) {
            //handle timer
            window.location.reload();
        }
    });
}
//checking for stop or complete the task.
var timerStopModal = function() {
    var timerModal = $('#timestopmodal').modal({
        'show': false,
        'backdrop': 'static',
    });

    timerModal.on('shown.bs.modal', function(e) {
        console.log('shown modal', localStorage.getItem('timeStamp'));
    });

    timerModal.on('hidden.bs.modal', function(e) {
        console.log('hidden modal', localStorage.getItem('timeStamp'));
        startTimer(localStorage.getItem('timeStamp'));
    });

    var completeBtn = timerModal.find('button#timestopmodal-complete-task');
    completeBtn.unbind().on('click', function() {
        updateTimer();
    });

    var stopBtn = timerModal.find('button#timestopmodal-stop-task');
    stopBtn.unbind().on('click', function() {
        updateTimer();
    });
    return timerModal;
};

function minutesToTime(mins) {
    var total_mins = Number(mins);
    var h = Math.floor(total_mins / 3600);
    var m = Math.floor(total_mins % 3600 / 60);


    var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes. ") : "";
    return hDisplay + mDisplay;
}


function getTime() {
    var timeLogout = new Date();
    var logoutTime = timeLogout.getFullYear() + '-' + (timeLogout.getMonth() + 1) + '-' + timeLogout.getDate();

    var date = timeLogout.getFullYear() + '-' + (timeLogout.getMonth() + 1) + '-' + timeLogout.getDate();
    var currentHr = timeLogout.getHours();
    currentHr = addZeroBefore(currentHr);
    var currentMin = timeLogout.getMinutes();
    currentMin = addZeroBefore(currentMin);
    var currentSec = timeLogout.getSeconds();
    currentSec = addZeroBefore(currentSec);
    var time = currentHr + ":" + currentMin + ":" + currentSec;
    return date;
}

function loadTaskActivities(formData) {
    $("#attach-card").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
    $.ajax({
        type: 'GET',
        url: timeTrackerBaseURL + 'index.php/user/load_task_data',
        data: formData,
        success: function(values) {
            var data = JSON.parse(values);
            $("#attach-card").empty();
            var timerModal = timerStopModal();

            for (x in data) {
                    console.log(data)
                for (var y = 0; y < data[x].length; y++) {
                    var cardHeader = $('<div class="card-header" />');
                    var cardHeaderRow = $('<div class="row pt-2" />');
                    var today = getTime();
                    if (data[x][y].start_time != null) {
                        if (today != data[x][y].start_time.slice(0,10)) {
                            $('.alert-box').show();
                        }
                    }
                    cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + ' ' + data[x][y].start_time + '</div>');
                    var stopCol = $('<div class="col-6 text-right" />');
                    if (data[x][y].running_task == 0)  /*check whether task is ended or not*/ {
                        var timeUsed = minutesToTime(data[x][y].t_minutes)
                        stopCol.append('<i class="far fa-clock"></i>Total timeused=' + timeUsed);
                    } else {
                        if (data[x][y].start_time != null) {
                            var stopButton = $('<a href="#" class="text-danger" id="stop"><i class="fas fa-stop"></i> Stop</a>').data('taskid', data[x][y].id);
                            stopButton.on('click', function() {
                                localStorage.setItem('task_id', $(this).data('taskid'));
                                timerModal.modal('show');
                            });
                        }
                        stopCol.append(stopButton);
                    }
                    cardHeaderRow.append(stopCol);
                    cardHeader.append(cardHeaderRow);

                    var cardInner = $("<div class='card card-style-1'  />");
                    cardInner.append(cardHeader);

                    var cardBody = $("<div class='card-body' />");  
                    cardBody.append(data[x][y].task_name);
                    cardInner.append(cardBody);

                    var cardFooter = $("<div class='card-footer'>");
                    var footerRow = $('<div class="row" />');
                    footerRow.append("<div class='col-6'> <i class='fab fa-twitter'></i> " + data[x][y].name + "</div>");

                    var footerRight = $("<div class='col-6 text-right card-actions'>");
                    //action Edit
                    var actionEdit = $('<a href="#" class="card-action action-edit text-success" id="action-edit"><i class="far fa-edit position_edit_icon animated fadeIn" data-toggle="tooltip" data-placement="top" title="edit"></i></a>');
                    actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_edit_task?t_id=' + data[x][y].id);


                    footerRight.append(actionEdit);
                    var actionPlay = $('<a href="#" class="card-action action-delete" id="action-play"><div class="text-center shadow-lg" data-tasktype="login"><i class="fas action-icon position_play_icon animated fadeIn fa-play" data-toggle="tooltip" data-placement="top" title="Resume"><input type="hidden" value =' + data[x][y].id + '></i></div></a>');

                    if (data[x][y].running_task == 0 || data[x][y].start_time == null) {
                        footerRight.append(actionPlay);
                    }

                    actionPlay.on('click', function(e) {
                        var t_id = this.getElementsByTagName('input').item(0).value;
                        $.ajax({
                            type: 'POST',
                            url: timeTrackerBaseURL + 'index.php/user/start_timer',
                            data: { 'action': 'task', 'id': t_id },
                            success: function(res) {
                                window.location.reload();
                            }
                        });
                    });

                    footerRow.append(footerRight);
                    cardFooter.append(footerRow);
                    cardInner.append(cardFooter);
                    var cardCol = $("<div class='col-lg-6 mb-4 cardCol' />");
                    cardCol.append(cardInner);
                    $("#attach-card").append(cardCol);
                    if ((data[x][y].running_task == 1 && data[x][y].start_time != null)) { //change background of current running task entries.
                        cardInner.css("background", "#e7d3fe");
                        cardHeader.css("background", "#e7d3fe");
                        cardFooter.css("background", "#e7d3fe");
                    }
                }
            }
        }
    });
}
function timeTo12HrFormat(time) { // Take a time in 24 hour format and format it in 12 hour format
    var time_part_array = time.split(":");
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

var mainTaskInterval;

function start_task_timer(startTime, id) {
    if (startTime === 'stop') {
        //clear the existing interval
        clearInterval(mainTaskInterval);
    } else {
        //set in local storage
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
    var hours = "0" + date.getHours();
    // Minutes part from the timestamp
    var minutes = "0" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "0" + date.getSeconds();

    var formattedTime = hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

    $('#task-timer' + id).html(formattedTime);
}

var oldEndTime = document.getElementById('update-endtime');
if (oldEndTime) {
    $('#save-changes').click(function() {
        var oldTime = document.getElementById('old-datepicker').value;
        console.log(oldTime)
        if (oldTime == "" || oldTime == " ") {
            document.getElementById('old-date-error').innerHTML = "Please enter correct end time.";
            oldEndTime.onsubmit = function() {
                return false;
            }
        } else
            oldEndTime.onsubmit = function() {
                return true;
            }

    })
}


$(document).ready(function() {

    $('#stop-time').click(function() {
        var t_id = $(this).data('id');


        if ($(this).data('tasktype') == 'task') {
            var taskUrl = timeTrackerBaseURL + 'index.php/user/start_timer';
            if (t_id) {
                taskUrl = timeTrackerBaseURL + 'index.php/user/stop_timer';
            }
            $.ajax({
                type: 'POST',
                url: taskUrl,
                data: { 'action': 'task', 'id': t_id },
                success: function(res) {
                    window.location.reload();
                }
            });
        } else {
            if (t_id == "" || t_id == " ") {
                $('#logout-modal').modal('show')
            }
            else
            {
           localStorage.setItem('task_id', t_id);
            var timerModal = timerStopModal();
            timerModal.modal('show');
            }
        }
    });
    var curr_timeStamp = Math.floor(Date.now() / 1000);
    login_timer = parseInt(curr_timeStamp) - parseInt(__timeTrackerLoginTime);
    if ((typeof login_timer != 'undefined')) {
        if (login_timer == parseInt(login_timer)) {
            startTimer(login_timer);
        }
    }
    var x = document.getElementsByClassName("task-slider");
    for (var i = 0; i < x.length; i++) {
        var __timeTrackerTaskTime = x[i].childNodes[1].value;
        __timeTrackerTaskTimeNew = parseInt(curr_timeStamp) - parseInt(__timeTrackerTaskTime);
        if ((typeof __timeTrackerTaskTimeNew != 'undefined') && (__timeTrackerTaskTimeNew !== 0)) {
            if (__timeTrackerTaskTimeNew == parseInt(__timeTrackerTaskTimeNew)) {
                start_task_timer(__timeTrackerTaskTimeNew, x[i].childNodes[1].id);
            }
        }
    }

    if ($("#attach-card").length > 0) {
        loadTaskActivities({ 'type': 'task' });
    }

    $('#dropdown-recent-acts').on('show.bs.dropdown', function(e) {
        var anchors = $(e.currentTarget).find('a.dropdown-item');
        anchors.unbind('click').on('click', function(e) {
            e.preventDefault();
            loadTaskActivities({ 'type': $(this).data('type') });
        });
    });

    $("#timer-slider").bxSlider({
        auto: false,
        infiniteLoop: false,
        controls: false,
    });

});