var minutesLabel = document.getElementById("minutes");
var secondsLabel = document.getElementById("seconds");
var hoursLabel = document.getElementById("hours");
var totalSeconds = 0;
var totalMinuts = 0;
var totalHours = 0;
var totalWorkTime = 0;
var pauseCount = 0;
var storing;
var count = localStorage.getItem('count');

// loginTime();

//main timer interval
var mainTimerInterval;

//localStorage.clear();

function timeUpdate() {
    localStorage.setItem('lastTime', getTime());
}

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


function logout() {
    localStorage.setItem('lastTime', 0);
    localStorage.setItem('timeStamp', 0);
    var counter = localStorage.getItem('count');
    storeTime();
    localStorage.setItem('count', parseInt(counter) + 1);
}



function storeTime() {

    count = parseInt(localStorage.getItem('count'));
    var today = new Date();
    var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();

    var logoutTime = getTime();

    var timeUsed = secondsToTime(totalSeconds);
    storing.ended = logoutTime;
    storing.timeUsed = timeUsed;
    localStorage.setItem('entry' + count, JSON.stringify(storing));


}

function secondsToTime(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
    return hDisplay + mDisplay + sDisplay;
}

function convertTimeToSeconds(time) {
    var totalHrs = parseInt(time.slice(0, 2));
    var totalMins = parseInt(time.slice(3, 5));
    var totalSec = parseInt(time.slice(6, 8));
    var totalSeconds = (totalHrs * 3600) + (totalMins * 60) + (totalSec);
    return totalSeconds;

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
    return time;
}

function addZeroBefore(n) {
    return (n < 10 ? '0' : '') + n;
}

var changeImage = document.getElementById('uploadImage');
changeImage.onsubmit = function(e) {
    var image = document.getElementById('image').value;
    if (image == "" || image == " ") {
        document.getElementById('imageerror').innerHTML = "Choose an image";
        return false;
    } else
        return true;
}

var timerStopModal = function() {
    var timerModal = $('#timestopmodal').modal({
        'show': false,
        'backdrop': 'static',
        // 'keyboard': false
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
        updateTimer('../php/complete.php');
    });

    var stopBtn = timerModal.find('button#timestopmodal-stop-task');
    stopBtn.unbind().on('click', function() {
        updateTimer('../php/stop.php');
    });

    return timerModal;
};

function loadTaskActivities(formData) {
    $("#attach-card").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
    $.ajax({
        type: 'GET',
        url: timeTrackerBaseURL + 'php/activity.php',
        data: formData,
        success: function(values) {
            var data = JSON.parse(values);
            $("#attach-card").empty();

            var timerModal = timerStopModal();
            for (x in data) {

                var start_time = timeTo12HrFormat(data[x].start_time);
                var cardHeader = $('<div class="card-header" />');
                var cardHeaderRow = $('<div class="row pt-2" />');
                cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + data[x].t_date + ' ' +start_time + '</div>');
                var stopCol = $('<div class="col-6 text-right" />');
                if (data[x].end_time !== '00:00:00') /*check whether task is ended or not*/
                 {
                    stopCol.append('<i class="far fa-clock"></i> ' + data[x].end_time);
                } else {

                    var stopButton = $('<a href="#" class="text-danger" id="stop"><i class="fas fa-stop"></i> Stop</a>').data('taskid', data[x].t_id);
                    stopButton.on('click', function() {
                        localStorage.setItem('tid', $(this).data('taskid'));
                        timerModal.modal('show');
                    });
                    stopCol.append(stopButton);
                }
                cardHeaderRow.append(stopCol);
                cardHeader.append(cardHeaderRow);


                var cardInner = $("<div class='card card-style-1' />");
                cardInner.append(cardHeader);

                var cardBody = $("<div class='card-body' />");
                cardBody.append(data[x].task_name);
                cardInner.append(cardBody);

                var cardFooter = $("<div class='card-footer'>");
                var footerRow = $('<div class="row" />');
                footerRow.append("<div class='col-6'> <i class='fab fa-twitter'></i> " + data[x].name + "</div>");

                var footerRight = $("<div class='col-6 text-right card-actions'>");
                //action Edit
                var actionEdit = $('<a href="#" class="card-action action-edit text-success" id="action-edit"><i class="far fa-edit"></i></a>');
                actionEdit.attr('href', timeTrackerBaseURL + 'user/edit_task.php?t_id=' + data[x].t_id);
                /* actionEdit.on('click', function() {
                     e.preventDefault();
                     window.location.href = "../user/edit_task.php";
                 });*/
                footerRight.append(actionEdit);

                /*//action delete
                var actionDelete = $('<a href="#" class="card-action action-delete text-danger" id="action-delete"><i class="far fa-trash-alt"></i></a>');
                actionDelete.on('click', function(e) {
                    e.preventDefault();
                    console.log(this.id);
                });
                footerRight.append(actionDelete);*/
                var actionPlay = $('<a href="#" class="card-action action-delete" id="action-play"><div class="text-center shadow-lg" data-tasktype="login"><i class="fas action-icon fa-play"><input type="hidden" value =' + data[x].t_id + '></i></div></a>');

                actionPlay.on('click', function(e) {
                    var t_id = this.getElementsByTagName('input').item(0).value;
                    $.ajax({
                        type: 'GET',
                        url: timeTrackerBaseURL + 'php/activity.php',
                        data: { 'id': t_id },
                        success: function(res) {
                            /*pause current running task and start selected task.*/
                            $.ajax({
                                type: 'POST',
                                url: timeTrackerBaseURL + 'php/play.php',
                                data: { 'action': 'task', 'id': t_id },
                                success: function(res) {
                                    window.location.reload();
                                }
                            });
                        }
                    });
                });
                if (data[x].end_time !== '00:00:00') {
                    footerRight.append(actionPlay);
                }

                footerRow.append(footerRight);
                cardFooter.append(footerRow);
                cardInner.append(cardFooter);

                var cardCol = $("<div class='col-lg-6 mb-4 cardCol' />");
                cardCol.append(cardInner);

                $("#attach-card").append(cardCol);
            }
        }
    });
}

function updateTimer(timerUrl) {
    /*var isRunning = $('#stop-time').data('isrunning');
    console.log(isRunning);
    var action = 'play';
    var url = '../php/play.php';
    if (parseInt(isRunning)) {
        action = 'stop';
        url = '../php/stop.php';
    }*/
    $.ajax({
        type: "POST",
        url: timerUrl,
        data: { id: localStorage.getItem('tid') },
        dataType: 'json',
        success: function(res) {
            //handle timer
            if (res.status) {
                window.location.reload();
            }
            /*if (res.status) {
                if (res.action == 'stop') {
                    $('#stop-time .action-icon').removeClass('fa-stop').addClass('fa-play');                    
                    startTimer('stop');
                } else {
                    $('#stop-time .action-icon').removeClass('fa-play').addClass('fa-stop');
                    startTimer(res.start_time);
                }
            }*/
        }
    });
}

$(document).ready(function() {

    $('#stop-time').click(function() {
        var t_id = $(this).data('id');
        if ($(this).data('tasktype') == 'login') {
            var taskUrl = timeTrackerBaseURL + 'php/play.php';
            if (t_id) {
                taskUrl = timeTrackerBaseURL + 'php/stop.php';
            }
            $.ajax({
                type: 'POST',
                url: taskUrl,
                data: { 'action': 'login', 'id': t_id },
                success: function(res) {
                    window.location.reload();
                }
            });
        } else {
            localStorage.setItem('tid', t_id);
            var timerModal = timerStopModal();
            timerModal.modal('show');
        }
    });

    if ((typeof __timeTrackerStartTime != 'undefined') && (__timeTrackerStartTime !== 0)) {
        //TODO: check for integer only 
        if (__timeTrackerStartTime == parseInt(__timeTrackerStartTime)) {
            startTimer(__timeTrackerStartTime);
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

    $('#submit-profile').click(function() {
        // $('#change-profile').modal('show');
        var image = document.getElementById('image').value;
        $.ajax({
            type: 'POST',
            url: '<?=BASE_URL?>php/upload_profile.php',
            data: { change_img: image },
            success: function(data) {
                //document.getElementById('new_img').src=response;
                $('#new_img').empty().append(data);
                console.log(data);
            }
        });
    });
});

function timeTo12HrFormat(time)
{   // Take a time in 24 hour format and format it in 12 hour format
    var time_part_array = time.split(":");
    var ampm = 'AM';

    if (time_part_array[0] >= 12) {
        ampm = 'PM';
    }

    if (time_part_array[0] > 12) {
        time_part_array[0] = time_part_array[0] - 12;
    }

    formatted_time = time_part_array[0] + ':' + time_part_array[1] +' '+ ampm;

    return formatted_time;
}