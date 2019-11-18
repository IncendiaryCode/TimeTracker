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
        }

        if (document.getElementById('editTask').checked) { //check radio button for multiple timings
            document.getElementById('taskError').innerHTML = "Please enter completed task timings..";
            return false;
        } else {
            // store data in database
            return true;
        }
    }
}

var addTime = {
    id: 0,
    ele: null,
    addBtn: null,
    allTimes: [],
    layout: function(date, start_time, end_time) {

        var section = $('<div class="time-section" />');
        var row = $('<div class="row" />');
        var id = this.id;

        var colDate = $('<div class="col-3">' +
            '<div class="input-group mb-3">' +
            '<input type="text" class="form-control" name="date-' + id + '" data-date-format="dd/mm/yyyy" id="date-picker-' + id + '" value=' + date + ' >' +
            '<div class="input-group-append">' +
            '<span class="input-group-text" id="basic-addon-' + id + '">' +
            '<span class="fa fa-calendar datepicker"></span>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>');
        colDate.appendTo(row);

        var colStartTime = $('<div class="col-3">' +
            '<div class="input-group">' +
            '<input id="start-time-' + id + '" class="form-control timepicker" name="start-time-' + id + '" value=' + start_time + ' />' +
            '</div>' +
            '</div>');
        colStartTime.appendTo(row);

        var colEndTime = $('<div class="col-3">' +
            '<div class="input-group">' +
            '<input id="end-time-' + id + '"  class="form-control timepicker1" name="end-time-' + id + '" value=' + end_time + ' />' +
            '</div>' +
            '</div>');
        colEndTime.appendTo(row);

        var removeBtn = $('<div class="col-3 text-center">' +
            '<a href="javascript:void(0);" title="Remove" id="remove-time-' + id + '">' +
            '<i class="fas fa-minus text-danger"></i>' +
            '</a>' +
            '</div>');
        removeBtn.on('click', function() {
            //remove the row
            $(this).closest(".time-section").remove();
        });
        removeBtn.appendTo(row);

        section.append(row);
        this.ele.prepend(section);

        section.find(".timepicker").timepicker({
            uiLibrary: 'bootstrap4'
        });
        section.find(".timepicker1").timepicker({
            uiLibrary: 'bootstrap4'
        });

        section.find(".datepicker").datepicker({
            //startDate: new Date(),
            weekStart: 1,
            daysOfWeekHighlighted: "6,0",
            autoclose: true,
            todayHighlight: true,
        });
    },
    validate: function() {

        var __this = this;
        var date = document.getElementById('date-picker-start').value;
        var start_time = document.getElementById('start-time-0').value;
        var end_time = document.getElementById('end-time-0').value;


        var __start_seconds = (parseInt(start_time.slice(0, 2)) * 60) + parseInt(start_time.slice(3, 5));

        var __end_seconds = (parseInt(end_time.slice(0, 2)) * 60) + (parseInt(end_time.slice(3, 5)));

        // fetch timings form database
        var validate_interval = __this.check_for_timeintervals(__start_seconds, __end_seconds, date);

        var validate_greater_time = __this.check_for_greatertime(date, start_time, end_time);


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
            return true;
        }

    },
    attachEvents: function() {
        var _this = this;
        this.addBtn.on('click', function(e) {
            e.preventDefault();
            if (_this.validate()) { // validate the timing details
                _this.id++;
                var date = document.getElementById('date-picker-start').value;
                var start_time = document.getElementById('start-time-0').value;
                var end_time = document.getElementById('end-time-0').value;
                _this.layout(date, start_time, end_time); //display multiple timings
            }
        });
    },
    init: function(eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-time');
        this.attachEvents();
    },
    check_for_timeintervals: function(__start_seconds, __end_seconds, date) { // check current task is already entered.
        var _this = this;
        var element = document.getElementById("task-add-time");
        var input_values = _this.get_inputvalues();
        if (input_values.length == 2) {
            return true;
        } else if (input_values.length > 1) {
            for (var i = 1; i < input_values.length - 1; i++) {

                var old_date = input_values[i]["old_date"];
                var start_old_time_sec = (parseInt(input_values[i]["old_start_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["old_start_time"].slice(3, 5)));
                var end_old_time_sec = (parseInt(input_values[i]["old_end_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["old_end_time"].slice(3, 5)));

                if (((start_old_time_sec <= __start_seconds) && (__start_seconds < end_old_time_sec)) && date == old_date) {
                    return false;
                }
                if (((start_old_time_sec >= __start_seconds) && (__end_seconds > start_old_time_sec)) && (date == old_date)) {
                    return false;
                }
            }
        }
        return true;
    },
    get_inputvalues: function() { // to get entered input values.
        var input_array = [{}];
        var k = 0;
        var element = document.getElementById("task-add-time");
        var value = element.getElementsByTagName("input");
        for (var i = 0; i < value.length / 3; i++) {
            var old_date = value[k++].value;
            var old_start_time = value[k++].value;
            var old_end_time = value[k++].value;
            input_array.push({ old_date, old_start_time, old_end_time });
        }
        return input_array;
    },
    check_for_greatertime: function(date, start_time, end_time) { // to check whether entered timings is greater than current time or not.
        var __this = this;
        var cur_date = new Date();
        var cur_date1 = cur_date.getDate() + '/' + (cur_date.getMonth() + 1) + '/' + cur_date.getFullYear();

        var flag = __this.check_date(date);
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
    },
    check_date: function(date) { // check entered date with current date.
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
};

$(document).ready(function() {

    //add multiple time to form
    addTime.init("#task-add-time");

    $('#editTask').click(function() {
        $('#task-times').show();
    });

    $('#newTask').click(function() {
        $('#task-times').hide();
    });

    $('.datepicker').datepicker({
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
    });
    $('#date-picker-start').datepicker("setDate", new Date());


    $('.timepicker').timepicker({
        uiLibrary: 'bootstrap4'
    });
    $('.timepicker1').timepicker({
        uiLibrary: 'bootstrap4'
    });

});