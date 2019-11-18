var addTask = document.getElementById('addTask');
/*var taskTimer;
 */
var pauseCount = 0;
var idItiration = 0;

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
    });
    $('#newTask').click(function() {
        __editTask = false;
        $('#Checked').hide();
    });
    $(function() {
        $('.datetimepicker').datetimepicker();
    });
});

$('#date-picker').datepicker({
    weekStart: 1,
    daysOfWeekHighlighted: "6,0",
    autoclose: true,
    todayHighlight: true,
});
$('#date-picker').datepicker("setDate", new Date());


$('#timepicker1').timepicker({
    uiLibrary: 'bootstrap4'
});
$('#timepicker2').timepicker({
    uiLibrary: 'bootstrap4'
});



function __store_timings()

{
    var date = document.getElementById('date-picker').value;
    var start_time = document.getElementById('timepicker1').value;
    var end_time = document.getElementById('timepicker2').value;


    var __start_seconds = parseInt(start_time.slice(0, 2)) * 60 + parseInt(start_time.slice(4, 6));

    var __end_seconds = parseInt(end_time.slice(0, 2)) * 60 + parseInt(end_time.slice(4, 6));

    /*fetch timings form database*/
    arr = localStorage.getItem('timings');
    if (arr == null) {
        var arr = [{}];
        localStorage.setItem('timings', JSON.stringify(arr));
    }

    arr = localStorage.getItem('timings');
    arr = JSON.parse(arr);

    var validate_interval = __check_for_timeintervals(arr, __start_seconds, date);

    var validate_greater_time = __check_for_greatertime(date, start_time, end_time);


    if (date == "" || date == " " || start_time == "" || start_time == " " || end_time == "" || end_time == " ") {

        document.getElementById('datetime-error').innerHTML = "Please enter valid details...";

    } else if (!validate_greater_time) {
        document.getElementById('datetime-error').innerHTML = "date/time of start/end connot be greater than currnet date/time";
    } else if (__start_seconds >= __end_seconds) {
        document.getElementById('datetime-error').innerHTML = "Start time cannot be greater or equal to end time.";
    } else if (!validate_interval) {
        document.getElementById('datetime-error').innerHTML = "Already same task is done in this interval.";
    } else {
        document.getElementById('datetime-error').innerHTML = " ";
        arr = localStorage.getItem('timings');
        arr = JSON.parse(arr);

        arr.push({ 'date': date, start_time: start_time, 'end_time': end_time });
        localStorage.setItem('timings', JSON.stringify(arr));
        window.location.reload();
    }
}

$(document).ready(function() {
    $('#editTask').click(function() {
        $("#show_list").empty();
        arr = localStorage.getItem('timings');
        if (arr == null) {
            $("#show_list").html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
        }
        arr = JSON.parse(arr);
        for (var i = 1; i < arr.length; i++) {
            if (arr[i] != null) {
                var list_element = $('<tr><td><div class="table-data__info">' + arr[i]['date'] + '</div>');
                list_element.append('</td><td><span>' + arr[i]['start_time'] + '</span>');
                list_element.append('</td><td>' + arr[i]['end_time'] + '</td><td><span class="more p-1" onclick="__delete_from_array(this, arr)"><input type="hidden" value=' + i + '><i class="fas fa-minus"  style="color:red;" data-toggle="tooltip" data-placement="top" title="delete"></i></span></td></tr>');
                $("#show_list").append(list_element);
            }
        }
    });
});

function __delete_from_array(index_value, array) {

    var index = index_value.childNodes[0].value;
    var value = array;
    delete value[index];
    localStorage.setItem('timings', JSON.stringify(value));
    window.location.reload();
}

function __check_for_timeintervals(arr, __start_seconds, date) {

    for (var i = 1; i < arr.length; i++) {
        if (arr[i] != null && arr[i]['date'] == date) {

            var old_start_time = arr[i]['start_time'];
            var old_end_time = arr[i]['end_time'];

            var start_old_time_sec = (parseInt(old_start_time.slice(0, 2)) * 60 + parseInt(old_start_time.slice(4, 6)));
            var end_old_time_sec = (parseInt(old_end_time.slice(0, 2)) * 60 + parseInt(old_end_time.slice(4, 6)))


            if (start_old_time_sec <= __start_seconds <= end_old_time_sec) {
                return false;
            }
        }

    }
    return true;
}

function __check_for_greatertime(date, start_time, end_time) {

    var cur_date = new Date();
    var cur_date1 = cur_date.getDate() + '/' + (cur_date.getMonth() + 1) + '/' + cur_date.getFullYear();

    var flag = check_date(date);
    if (flag == false) {
        return false;
    }
    var cur_time = cur_date.getHours() + ':' + cur_date.getMinutes();

    if (start_time > cur_time && (date == cur_date1)) {
        return false;
    }
    if (end_time > cur_time && (date == cur_date1)) {
        return false;
    }
    return true;
}

function check_date(date) {
    var cur_date = new Date();


    if ((parseInt(cur_date.getFullYear()) < parseInt(date.slice(6, 10)))) {
        return false;
    }

    if (((parseInt(cur_date.getMonth() + 1)) < parseInt(date.slice(3, 5)))) {
        return false;
    }

    if (parseInt(cur_date.getDate()) < (parseInt(date.slice(0, 2)))) {
        return false;
    }
    return true;

}
