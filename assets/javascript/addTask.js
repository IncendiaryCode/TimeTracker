var addTask = document.getElementById('addTask');
var taskTimer;
var pauseCount = 0;
var num = localStorage.getItem('num');
var idItiration = 0;
/*localStorage.clear();*/
var variable;
var store = {
    'id': 'id',
    'started': '10:00AM',
    'ended': '0',
    'taskName': 'taskName',
    'taskDescription': 'taskDescription',
    'projectName': 'projectName',
    'totalTime': 'totalTime'
}

if (addTask) {
    var m = new Date();
    var start_date = m.getUTCFullYear() + "-" + m.getUTCMonth() + "-" + m.getUTCDate() + " " + m.getHours() + ":" + m.getMinutes() + ":" + m.getSeconds();
    document.getElementById('setCurrentDate').value = start_date;
    var loginTime = document.getElementById('setCurrentDate').value;
    var ended = document.getElementById('ended').value;
    if (ended == null || ended == "" || ended == " ") {
        ended = 0;
    }
    addTask.onsubmit = function(e) {
        var taskName = document.getElementById('Taskname').value;
        var project = document.getElementById('chooseProject').value;
        var taskDescription = document.getElementById('description').value;

        if (taskName == "" || taskName == " ") {
            document.getElementById('taskError').innerHTML = "Please Enter Task Name ";
            return false;
        }
        if (project == "" || project == "Select Project") {
            document.getElementById('taskError').innerHTML = "Please Choose Project Name ";
            console.log("here");
            return false;
        } else {
            /*store data in database*/
            return true;
        }
    }
}


var secLabel = document.getElementById("sec");
var minLabel = document.getElementById("min");
var hrLabel = document.getElementById("hr");
var totalSec = 0;
var totalMin = 0;
var totalHr = 0;

function displaytime() {

    ++totalSec;

    timeFormate(totalSec % 60, secLabel);
    totalMin = parseInt(totalSec / 60);
    totalHr = parseInt(totalSec / 3600);
    timeFormate(totalMin % 60, minLabel);
    timeFormate(totalHr % 3600, hrLabel);
}

function timeFormate(value, lable) {
    var n = value.toString().length;
    if (value == 59) {
        lable.innerHTML = '00';
    }
    if (value == "00") {

    } else if (n == 1) {
        lable.innerHTML = '0' + value;
    } else {
        lable.innerHTML = (totalSec % 60);
    }
}


function taskSchedule(card) {

    console.log(card);
    var id = card.id;
    var loginTime = card.started;
    var ended = card.ended;
    if (ended == null) {
        ended = 0;
    }
    var taskName = card.taskName;
    var projectName = card.projectName;
    var taskDescription = card.taskDescription;


    if ((++pauseCount) % 2 == 0) {
        clearInterval(taskTimer);
        console.log('totalSec', totalSec);
        store.ended = parseInt(ended) + taskSecondsToTime(totalSec);
        console.log(store.ended);
        store.id = id;
        store.started = loginTime;
        store.taskName = taskName;
        store.projectName = projectName;
        localStorage.setItem('row' + store.id, JSON.stringify(store));

        document.getElementById('stop').innerHTML = "Resume";

    } else {
        taskTimer = setInterval(displaytime, 1000);
        document.getElementById('stop').innerHTML = "Stop";
    }
}

function clearTime() {
    $('.shadow').click(function() {
        var cardText = $(this).text();
        var cardNum = cardText.slice(0, 1);
        console.log(cardNum);
        var cardData = localStorage.getItem('row' + parseInt(cardNum));

        var card = JSON.parse(cardData);
        taskSchedule(card);
    });
}

function pauseTime() {
    pauseCount++;
    console.log(totalSec);
    document.getElementById('stop').innerHTML = "Resume";
    clearInterval(taskTimer);
}
/**/


function taskSecondsToTime(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
    return h + m + s;
}

function taskTimeToSeconds(time) {
    var totalHrs = parseInt(time.slice(0, 2));
    var totalMins = parseInt(time.slice(3, 5));
    var totalSec = parseInt(time.slice(6, 8));
    var totalSeconds = (totalHrs * 3600) + (totalMins * 60) + (totalSec);
    return totalSeconds;

}


function sortByTaskName() {
    /*retreive name from database*/

}

$(document).ready(function(){
    var btn;
    $('#editTask').click(function()
    {
    btn = document.getElementById('editTask').checked;
    if (btn == true) {
        $('#Checked').show();
    }
    })
    $('#newTask').click(function()
    {
    $('#Checked').hide();
});
});