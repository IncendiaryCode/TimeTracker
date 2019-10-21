var addTask = document.getElementById('addTask');
/*var taskTimer;
*/var pauseCount = 0;
var idItiration = 0;
/*localStorage.clear();*/
var __editTask;

if (addTask) {
    var m = new Date();
    var start_date = m.getUTCFullYear() + "-" + m.getUTCMonth() + "-" + m.getUTCDate() + " " + m.getHours() + ":" + m.getMinutes() + ":" + m.getSeconds();
    // console.log(start_date);
    /*    document.getElementById('setCurrentDate').innerHTML = start_date;*/
    // var loginTime = document.getElementById('setCurrentDate').value;
    // var ended = document.getElementById('ended').value;
    /*if (ended == null || ended == "" || ended == " ") {
        ended = 0;
    }*/
    addTask.onsubmit = function(e) {
        var taskName = document.getElementById('Taskname').value;
        var project = document.getElementById('choose-project').value;

        if (taskName == "" || taskName == " ") {
            document.getElementById('taskError').innerHTML = "Please Enter Task Name ";
            return false;
        }
        if (__editTask == true) {
            var start_date = document.getElementById('started-date').value;
            var end_date = document.getElementById('end-date').value;
            if (start_date == "" || start_date == " " || end_date == "" || end_date == " ") {
            document.getElementById('taskError').innerHTML = "Please Enter start and end date.";
            return false;
            }
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



$(document).ready(function() {

    $('#editTask').click(function() {
        __editTask = document.getElementById('editTask').checked;
        if (__editTask == true) {
            $('#Checked').show();
        }
    })
    $('#newTask').click(function() {
        $('#Checked').hide();
    });
$(function () {
    $('.datetimepicker').datetimepicker();
    });
});