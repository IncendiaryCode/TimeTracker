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

/*function timeUpdate() {
    localStorage.setItem('lastTime', getTime());
}
*/








/*var images = ["1523978591478-c753949ff840?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=79943bd5886756fc2f8172b3c491aaad", "1506744038136-46273834b3fb?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=4250c7ad21d5fc105432c2368356c084", "1528920304568-7aa06b3dda8b?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=c69d90bfad16229014dfa8c719597c3d", "1523712999610-f77fbcfc3843?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=b2e980356e68649599d7942ec0cb0207", "1506260408121-e353d10b87c7?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=6f7e7a456594490e5791d4001acc8254", "1524260855046-f743b3cdad07?ixlib=rb-0.3.5&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=b7e11411bbd92204a75f7007e8e65a18"];

var svg = document.querySelector("svg");
var circle = svg.querySelector("circle");
var r = circle.getAttribute("r");
var circ = 2 * Math.PI * r;

var sections = document.querySelectorAll("section");
var count = sections.length;
var n = 0;

sections.forEach(function (section, i) {
    var image = new Image();
    var src = "https://images.unsplash.com/photo-" + images[i];

    image.onload = function (e) {
        section.firstElementChild.style.backgroundImage = "url(" + src + ")";

        n++;

        if (n >= count) {
            init();
        }
    };

    image.src = src;
});

var pageable = void 0;
function init() {

    pageable = new Pageable("span", {
        onInit: onInit,
        onScroll: scroll,
        animation: 600,
        freeScroll: false,
        swipeThreshold: 200,
        infinite: true,
        //slideshow: {
          //  interval: 3000,
        //},
        orientation: "horizontal",
        navPrevEl: ".nav-prev",
        navNextEl: ".nav-next"
    });
}

function scroll(data) {
    var pos = round(1 - (data.max - data.scrolled) / data.max, 3);
    circle.style.strokeDashoffset = circ - circ * pos;
}

function onInit(data) {
    scroll.call(this, data);
}

function round(value, decimals) {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}





*/














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

/*
function logout() {
    localStorage.setItem('lastTime', 0);
    localStorage.setItem('timeStamp', 0);
    var counter = localStorage.getItem('count');
    storeTime();
    localStorage.setItem('count', parseInt(counter) + 1);

    localStorage.setItem('firstTime', 'null');
    console.log("loggin out......");

}

*/

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
        //updateTimer('../php/complete.php');
        updateTimer('..index.php/user/stop_timer');
        window.location.reload();
    });

    var stopBtn = timerModal.find('button#timestopmodal-stop-task');
    stopBtn.unbind().on('click', function() {
        updateTimer('..index.php/user/stop_timer');
    });

    return timerModal;
};

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
            for (x in data){
                for(var y=0;y<data[x].length;y++){
               // var start_time = timeTo12HrFormat(data[x][y].start_time);
                //alert(start_time);
                var cardHeader = $('<div class="card-header" />');
                var cardHeaderRow = $('<div class="row pt-2" />');
                cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>'  + ' ' + data[x][y].start_time + '</div>');
                var stopCol = $('<div class="col-6 text-right" />');
                //alert(data[x][y].end_time);
                if (data[x][y].end_time !== '0000-00-00 00:00:00' || data[x][y].end_time !== null) /*check whether task is ended or not*/ {
                    stopCol.append('<i class="far fa-clock"></i>' + data[x][y].end_time);
                    /*change background of current running task entries*/
                    $('.card-style-1').css("background", "#e7d3fe");
                    $('.card-header').css("background", "#e7d3fe");
                    $('.card-footer').css("background", "#e7d3fe");
                } else {
                    var stopButton = $('<a href="#" class="text-danger" id="stop"><i class="fas fa-stop"><input type="hidden" value=' + data[x][y].start_time + '></i> Stop</a>').data('taskid',data[x][y].task_id);
                   
                    stopButton.on('click', function() {
                         alert(data[x][y].start_time);
                       // var start_time = this.getElementsByTagName('input').item(0).value;
                        var start_time=document.getElementsByTagName("input")[0].getAttribute("value");
                        alert(start_time);
                        localStorage.setItem('tid', $(this).data('taskid'));
                        localStorage.setItem('starttime',start_time);
                        alert(localStorage.getItem('starttime'));
                        timerModal.modal('show');
                    });
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
                actionEdit.attr('href', timeTrackerBaseURL + 'index.php/user/load_edit_task?t_id=' + data[x][y].task_id);
                
                /*actionEdit.on('click',function(e){
                    //var id = data[x][y].task_id;
                    var id = this.getElementsByTagName('input').item(0).value;
                    //console.log(t_id);
                    $.ajax({
                        type: 'POST',
                        url: timeTrackerBaseURL + 'index.php/user/load_edit_task',
                        data :{t_id:id},
                       
                    });
                });*/
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
                var actionPlay = $('<a href="#" class="card-action action-delete" id="action-play"><div class="text-center shadow-lg" data-tasktype="login"><i class="fas action-icon position_play_icon animated fadeIn fa-play" data-toggle="tooltip" data-placement="top" title="Resume"><input type="hidden" value =' + data[x][y].task_id + '></i></div></a>');

                actionPlay.on('click', function(e) {
                    var t_id = this.getElementsByTagName('input').item(0).value;
                    //alert(t_id);
                    $.ajax({
                        type: 'POST',
                        url: timeTrackerBaseURL + 'index.php/user/start_timer',
                        data: { 'action': 'task', 'id': t_id },
                        success: function(res) {
                        //alert(res);
                            window.location.reload();
                        }
                    });
                });
                if (data[x][y].end_time !== '0000-00-00 00:00:00' || data[x][y].end_time !== null) {
                    footerRight.append(actionPlay);
                }

                footerRow.append(footerRight);
                cardFooter.append(footerRow);
                cardInner.append(cardFooter);

                var cardCol = $("<div class='col-lg-6 mb-4 cardCol' />");
                cardCol.append(cardInner);

                $("#attach-card").append(cardCol);
            }
        }}
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
        data: { id: localStorage.getItem('tid'),start_time:localStorage.getItem('starttime') },
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
    var firstTime = localStorage.getItem("firstTime");
    if (firstTime == 'null') {
        console.log('timer running');
        localStorage.setItem("firstTime", "stop");
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/user/start_timer',
            data: { 'action': 'login' },
            success: function(res) {
                window.location.reload();
            }
        });
    };
    $('#stop-time').click(function() {
        var t_id = $(this).data('id');
        //alert($(this).data('tasktype'));
        if ($(this).data('tasktype') == 'login') {
            var taskUrl = timeTrackerBaseURL + 'index.php/user/start_timer';
            if (t_id) {
                taskUrl = timeTrackerBaseURL + 'index.php/user/stop_timer';
            }
            console.log('task_id', t_id);
            $.ajax({
                type: 'POST',
                url: taskUrl,
                data: { 'action': 'login', 'id': t_id },
                success: function(res) {
                    //alert(res);
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
           // alert($(this).data('type'));
            loadTaskActivities({ 'type': $(this).data('type') });
        });
    });

});

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
