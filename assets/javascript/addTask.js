var addTask = document.getElementById('addTask');

var __editTask;

if (addTask) {
    var m = new Date();
    var start_date = m.getUTCFullYear() + "-" + m.getUTCMonth() + "-" + m.getUTCDate() + " " + m.getHours() + ":" + m.getMinutes() + ":" + m.getSeconds();
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

var id = 0;

function __store_timings()

{
    var date = document.getElementById('date-picker').value;
    var start_time = document.getElementById('timepicker1').value;
    var end_time = document.getElementById('timepicker2').value;


    var __start_seconds = (parseInt(start_time.slice(0, 2)) * 60) + parseInt(start_time.slice(3, 5));

    var __end_seconds = (parseInt(end_time.slice(0, 2)) * 60) + (parseInt(end_time.slice(3, 5)));

    /*fetch timings form database*/
    var validate_interval = __check_for_timeintervals(__start_seconds, date);

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
        id = id + 1;
        display_list(date, start_time, end_time, id);
        //window.location.reload();
    }
}


function display_list(date, start_time, end_time, itirator) {
    var list_element = $('<div class="row p-4"><div class="col-3"><div class="input-group date"><input class="form-control-file border-top-0 border-left-0 border-right-0 border-dark" name="date' + itirator + '" data-date-format="dd/mm/yyyy" value=' + date + '><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div></div>');
    list_element.append('<div class="col-3"><div class="input-group date"><input class="form-control-file border-top-0 border-left-0 border-right-0 rounded-0 border-dark" name="start-Time' + itirator + '"  value=' + start_time + '></div></div>');
    list_element.append('<div class="col-3"><div class="input-group date"><input class="form-control-file border-top-0 border-left-0 border-right-0 rounded-0 border-dark" name="end-Time' + itirator + '" value=' + end_time + '></div></div>');
    list_element.append('<div class="col-3 text-danger text-center"><i class="fas fa-minus" onclick="__delete_from_array(this)" data-toggle="tooltip" data-placement="top" title="delete"></i></div></div>');
    $("#show_list").append(list_element);

    document.getElementById('date-picker').value = "";
    document.getElementById('timepicker1').value = "";
    document.getElementById('timepicker2').value = "";


}


function __delete_from_array(content) {

    var remove_element = content.parentNode.parentNode;
    remove_element.remove();
}

function __check_for_timeintervals(__start_seconds, date) {

    var element = document.getElementById("show_list");
    var value = element.getElementsByTagName("input");
    var input_values = __store_inputvalues();

    if (input_values.length > 1) {
        for (var i = 1; i < input_values.length; i++) {

            var old_date = input_values[i]["old_date"];
            var start_old_time_sec = (parseInt(input_values[i]["old_start_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["old_start_time"].slice(3, 5)));
            var end_old_time_sec = (parseInt(input_values[i]["old_end_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["old_end_time"].slice(3, 5)));

            if (((start_old_time_sec < __start_seconds) && (__start_seconds < end_old_time_sec)) && date == old_date) {
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
    if ((parseInt(cur_date.getFullYear()) > parseInt(date.slice(6, 10)))) {
        return true;
    }
    if ((parseInt(cur_date.getFullYear()) < parseInt(date.slice(6, 10)))) {
        return false;
    }
    if ((parseInt(cur_date.getFullYear()) == parseInt(date.slice(6, 10)))) {
        if ((parseInt(cur_date.getMonth() + 1)) > parseInt(date.slice(3, 5))) {
            return true;
        }
        if ((parseInt(cur_date.getMonth() + 1)) < parseInt(date.slice(3, 5))) {
            return false;
        }
        if ((parseInt(cur_date.getMonth() + 1)) == parseInt(date.slice(3, 5))) {
            if ((parseInt(cur_date.getDate()) >= (parseInt(date.slice(0, 2))))) {
                return true;
            } else { return false; }
        }
    }
    return true;

}

function __store_inputvalues() {
    var input_array = [{}];
    var k = 0;
    var element = document.getElementById("show_list");
    var value = element.getElementsByTagName("input");
    for (var i = 0; i < value.length / 3; i++) {
        var old_date = value[k++].value;
        var old_start_time = value[k++].value;
        var old_end_time = value[k++].value;
        input_array.push({ old_date, old_start_time, old_end_time });
    }
    return input_array;
}