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
    'taskDescription':'taskDescription',
    'projectName': 'projectName',
    'totalTime': 'totalTime'
}
if (addTask) {
    var m=new Date();
    var start_date=m.getUTCFullYear() + "-" + m.getUTCMonth() + "-" + m.getUTCDate() + " "+ m.getHours()+":"+m.getMinutes()+":"+m.getSeconds();
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
<<<<<<< HEAD
        if (project == "" || project == "Select Project") {
=======
        if (project == "Select Project") {
>>>>>>> 8f08aeaa5781360dc38d4f4cb6e97c2997478051
            document.getElementById('taskError').innerHTML = "Please Choose Project Name ";
            console.log("here");
            return false;
        } else {
            /*store data in database*/
            return true;
        }
    }

}

function showTask() {
    num = localStorage.getItem('num');
    // console.log(num);
    /*retrieve from db and pass it..*/
    /*display in loop*/
    if (num > 0) {
        for (var i = num; i > 0; i--) {
            variable = localStorage.getItem('row' + parseInt(i));
            var data = JSON.parse(variable);
            var id = data.id;

            var loginTime = data.started;
            var ended = data.ended;
            var taskName = data.taskName;
            var projectName = data.projectName;
<<<<<<< HEAD
            
         
=======
            if ((ended == "" || ended == " " || ended == null)) {
                $(document).ready(function() {
                    $('.timer').show();
                    $('.Btn').show();

                });

            } else {
                $(document).ready(function() {
                    $('.timer').hide();
                }); 
            }
            $(".attach-card").append("<div class='col-lg-5 ml-lg-5 mt-3 shadow card-style' onclick = 'clearTime()' data-toggle='modal' data-target='#newModal' data-toggle='tooltip' data-placement='top' '><div class='card-header bg-white text-left text-black-50'><div class='row pt-2'><span class='vertical-line'></span><div class='col-6 text-left'>" + id + ")" + loginTime + "</div><div class='col-5 text-right'><span class='text-right timer'><i class=' far fa-clock'></i> <label id='hr'>00</label>:<label id='min'>00</label>:<label id='sec'>00</label></span><span>" + ended + "</span></div></div></div><div class='card-body text-body ml-4'><p>" + taskName + "</p></div><div class='card-footer text-black-50 bg-white pl-4 pb-3'><i class='fas fa-user-circle'></i>" + projectName + "</div></div>");
>>>>>>> 8f08aeaa5781360dc38d4f4cb6e97c2997478051
        }
    }
}

/*showTask();*/


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
    var id= card.id;
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
            store.id=id;
            store.started=loginTime;
            store.taskName=taskName;
            store.projectName=projectName;
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
