$(document).ready(function() {
    $('.fa-play').click(function() {
        $('.fa-stop').show();
        $('.fa-play').hide();
    })
    $('.fa-stop ').click(function() {
        $('.fa-stop').hide();
        $('.fa-play').show();
    })
})

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
loginTime();
var a;
var flag = false;
displayTaskTime();
/*localStorage.clear();*/
function setTime() {
    if (flag == true) {
        totalSeconds = localStorage.getItem('totalSeconds');
        flag = false;
        /*pauseCount++;*/
    }
    ++totalSeconds;
    checkTime(totalSeconds % 60, secondsLabel);
    totalMinuts = parseInt(totalSeconds / 60);
    totalHours = parseInt(totalSeconds / 3600);
    checkTime(totalMinuts % 60, minutesLabel);
    checkTime(totalHours % 3600, hoursLabel);
}

function checkTime(value, lable) {
    var n = value.toString().length;
    if (value == 59) {
        lable.innerHTML = '00';
    }
    if (value == "00") {

    } else if (n == 1) {
        lable.innerHTML = '0' + value;
    } else {
        lable.innerHTML = (totalSeconds % 60);
    }
}

function timeUpdate() {
    localStorage.setItem('lastTime', getTime());
    localStorage.setItem('totalSeconds', totalSeconds);
}

function pause() {

    if ((++pauseCount) % 2 !== 0) {
        localStorage.setItem('totalSeconds', totalSeconds);
        clearInterval(a);
    } else {
        flag = true;
        a = setInterval(setTime, 1000);
        count = parseInt(localStorage.getItem('count'));
        var today = new Date();
        var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
        var todayTime = getTime();
        var time = convertTimeToSeconds(todayTime);
        var oldTime = localStorage.getItem('loginTime');
        var oldtime = convertTimeToSeconds(oldTime);
        storing = storing + count;
        storing = {
            'date': date + todayTime,
            'started': oldTime,
            'ended': '00:00:00',
            'timeUsed': '00:00:00'
        }
        localStorage.setItem('entry' + count, JSON.stringify(storing));

    }
}

function displayTaskTime() {

    var totalSeconds = localStorage.getItem('totalSeconds');

    var lastTime = localStorage.getItem('lastTime');
    if (lastTime == 0 || lastTime == null) {
        localStorage.setItem('lastTime', getTime());
        localStorage.setItem('totalSeconds', 0);
    } else {

        var currentTime = getTime();
        currentTime = convertTimeToSeconds(currentTime);
        lastTime = convertTimeToSeconds(lastTime);
        var diff = currentTime - lastTime;
        totalSeconds = parseInt(totalSeconds) + diff;
        localStorage.setItem('totalSeconds', totalSeconds);
        flag = true;
        pauseCount++;
        pause();
    }

}

function logout() {
    localStorage.setItem('lastTime', 0);
    localStorage.setItem('totalSeconds', 0);
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

function loginTime() {

    var id = localStorage.getItem('id');
    if (count == null) {
        localStorage.setItem('count', 0);
    }
    count = parseInt(localStorage.getItem('count'));
    var todayTime = getTime();
    document.getElementById('login-time').innerHTML = "Started at " + todayTime;
    localStorage.setItem('loginTime', todayTime);
    storing = storing + count;
    storing = {
        'id': id,
        'date': 'date',
        'started': todayTime,
        'ended': '00:00:00',
        'timeUsed': '00:00:00'
    }
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

$(document).ready(function() {
    $('.submitProfile').click(function() {
        $('#changeProfile').modal('show')
    });
});