var addTime = {
    id: 0,
    ele: null,
    addBtn: null,
    array_of_timings: [],
    layout: function (date, start_time, end_time, descri) {

        var section = $('<div class="time-section pt-3 pb-5" />');
        var row = $('<div class="row" />');
        var id = this.id;
        var array_of_timings = this.array_of_timings;

        var colDate = $('<div class="col-4">' +
            '<div class="input-group mb-3">' +
            '<input type="text" class="form-control" name="daterange[' + id + '][date]" data-date-format="yyyy-mm-dd" id="date-picker-start-' + id + '" value=' + date + ' >' +
            '<div class="input-group-append">' +
            '<span class="input-group-text" id="basic-addon-' + id + '">' +
            '<span class="fa fa-calendar datepicker"></span>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>');
        colDate.appendTo(row);

        var colStartTime = $('<div class="col-4">' +
            '<div class="input-group">' +
            '<input id="start-time-' + id + '" class="form-control timepicker" data-date-format="hh:mm:ss" name="daterange[' + id + '][start]" value=' + start_time + ' placeholder="hh:mm" />' +
            '</div>' +
            '</div>');
        colStartTime.appendTo(row);

        var colEndTime = $('<div class="col-4">' +
            '<div class="input-group">' +
            '<input id="end-time-' + id + '"  class="form-control timepicker1" data-date-format="hh:mm:ss" name="daterange[' + id + '][end]" value=' + end_time + ' placeholder="hh:mm" />' +
            '</div>' +
            '</div>');
        colEndTime.appendTo(row);

        var colDescri = $('<div class="col-11">' +
            '<div class="input-group">' +
            '<input id="description-' + id + '"  class="form-control"  name="daterange[' + id + '][description]" value=' + descri + ' placeholder="description" />' +
            '</div>' +
            '</div>');
        colDescri.appendTo(row);

        var removeBtn = $('<div class="col-1 text-center">' +
            '<a href="javascript:void(0);" title="Remove" id="remove-time-' + id + '">' +
            '<i class="fas fa-minus icon-plus text-danger"></i>' +
            '</a>' +
            '</div><hr>');

        removeBtn.on('click', function () {
            //remove the row
            $(this).closest(".time-section").remove();
        });
        array_of_timings.push({ date, start_time, end_time });
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
    validate_info: function () {

        var __this = this;
        var date = document.getElementById('date-picker-start-0').value;
        var start_time = document.getElementById('start-time-0').value;
        var end_time = document.getElementById('end-time-0').value;

        var __start_seconds = (parseInt(start_time.slice(0, 2)) * 60) + parseInt(start_time.slice(3, 5));

        var __end_seconds = (parseInt(end_time.slice(0, 2)) * 60) + (parseInt(end_time.slice(3, 5)));


        // fetch timings form database
        var validate_interval = __this.check_for_timeintervals(__start_seconds, __end_seconds, date);

        var check_for_date = __this.check_date(date);
        validate_greater_time = __this.check_for_greatertime(date, start_time, end_time);
        if (date == "" || date == " " || start_time == "" || start_time == " " || end_time == "" || end_time == " ") {

            document.getElementById('datetime-error').innerHTML = "Please enter valid details...";
            return false;

        } 
        else if (__this.check_date(date)) {
            document.getElementById('datetime-error').innerHTML = "date/time of start/end connot be greater than currnet date/time";
            return false;
        }
        else if (validate_greater_time) {
            document.getElementById('datetime-error').innerHTML = "date/time of start/end connot be greater than currnet date/time";
            return false;
        }
         else if (__start_seconds >= __end_seconds) {

            document.getElementById('datetime-error').innerHTML = "Start time cannot be greater or equal to end time.";
            return false;
        } else if (!validate_interval) {
            document.getElementById('datetime-error').innerHTML = "Already same task is done in this interval.";
            return false;
        } else {
            document.getElementById('datetime-error').innerHTML = " ";
            return true;
        }
    },
    validate: function () {

        var __this = this;
        var date = document.getElementById('date-picker-start-0').value;
        var start_time = document.getElementById('start-time-0').value;
        var end_time = document.getElementById('end-time-0').value;

       
        var __start_seconds = (parseInt(start_time.slice(0, 2)) * 60) + parseInt(start_time.slice(3, 5));

        var __end_seconds = (parseInt(end_time.slice(0, 2)) * 60) + (parseInt(end_time.slice(3, 5)));

        // fetch timings form database
        var validate_interval = __this.check_for_timeintervals(__start_seconds, __end_seconds, date);

        var validate_greater_time = __this.check_for_greatertime(date, start_time, end_time);


        if (date == "" || date == " " || start_time == "" || start_time == " ") {

            document.getElementById('datetime-error').innerHTML = "Please enter valid details...";
            return false;

        } else if (validate_greater_time) {
            document.getElementById('datetime-error').innerHTML = "date/time of start/end connot be greater than currnet date/time";
            return false;
        }
        else if (__this.check_date(date)) {
            document.getElementById('datetime-error').innerHTML = "date/time of start/end connot be greater than currnet date/time";
            return false;
        }
         else if (__start_seconds >= __end_seconds) {

            document.getElementById('datetime-error').innerHTML = "Start time cannot be greater or equal to end time.";
            return false;
        } else if (!validate_interval) {
            document.getElementById('datetime-error').innerHTML = "Already same task is done in this interval.";
            return false;
        } else {
            document.getElementById('datetime-error').innerHTML = " ";
            return true;
        }
    },
    attachEvents: function () {
        var _this = this;
        this.addBtn.on('click', function (e) {
            e.preventDefault();
            if (_this.validate_info()) { // validate the timing details
                _this.id++;
                var date = document.getElementById('date-picker-start-0').value;
                var start_time = document.getElementById('start-time-0').value;
                var end_time = document.getElementById('end-time-0').value;
                var descri;
                if(document.getElementById('description-0').value == "" || document.getElementById('description-0').value == " ")
                {
                descri = "description";
                }else{
                descri = document.getElementById('description-0').value;
                }
                _this.layout(date, start_time, end_time, descri); //display multiple timings


                var current_time = new Date().getHours().toString()+':'+ new Date().getMinutes();
                document.getElementById('start-time-0').value = current_time;
                $('.datepicker-0').datepicker("setDate", new Date());
                document.getElementById('end-time-0').value = "";
                document.getElementById('description-0').value = "";

            }
        });
    },
    init: function (eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-time');
        this.attachEvents();

    },
    check_for_timeintervals: function (__start_seconds, __end_seconds, date) { // check current task is already entered.
        var _this = this;
       // var element = document.getElementById("task-add-time");
        var input_values = this.array_of_timings;
        if (input_values.length > 0) {
            for (var i = 0; i < input_values.length; i++) {

                var old_date = input_values[i]["date"];
                var start_old_time_sec = (parseInt(input_values[i]["start_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["start_time"].slice(3, 5)));
                var end_old_time_sec = (parseInt(input_values[i]["end_time"].slice(0, 2)) * 60 + parseInt(input_values[i]["end_time"].slice(3, 5)));

                if (((start_old_time_sec <= __start_seconds) && (__start_seconds < end_old_time_sec)) && date == old_date) {
                    return false;
                }
                if (((start_old_time_sec >= __start_seconds) && (__end_seconds > start_old_time_sec)) && (date == old_date)) {
                    return false;
                }
            }
        } else {
            return true;
        }
        return true;
    },
    
    check_for_greatertime: function (date, start_time, end_time) { // to check whether entered timings is greater than current time or not.
        var __this = this;

        var cur_date = new Date();
        var day;
        var month;
        var cur_date1 = cur_date.getFullYear() + '-' + (cur_date.getMonth() + 1)  + '-' +  cur_date.getDate();

        var cur_time = cur_date.getHours() + ':' + cur_date.getMinutes();
            if ((parseInt(end_time.slice(0,2)) > parseInt(cur_time.slice(0,2)))) {

                if(((parseInt(date.slice(0,4))) == cur_date.getFullYear()) && ((parseInt(date.slice(5,7))) == cur_date.getMonth() + 1) && ((parseInt(date.slice(9,11))) == cur_date.getDate()))
                {
                    return true;
                }
            }
        else
            {
            if ((parseInt(end_time.slice(3,5)) > parseInt(cur_time.slice(3,5)))) {
                if(((parseInt(date.slice(0,4))) == cur_date.getFullYear()) && ((parseInt(date.slice(5,7))) == cur_date.getMonth() + 1) && ((parseInt(date.slice(9,11))) == cur_date.getDate()))
                {
                    return true;
                }
            }
        }
        return false;
    },
    check_date: function (date) { // check entered date with current date.
        var cur_date = new Date();
        if ((parseInt(cur_date.getFullYear()) < parseInt(date.slice(6, 10)))) {
            return true;
        }
        if ((parseInt(cur_date.getFullYear()) == parseInt(date.slice(0, 4)))) {
            if ((parseInt(cur_date.getMonth() + 1)) > parseInt(date.slice(5, 7))) {
                return false;
            }
            if ((parseInt(cur_date.getMonth()+1)) < parseInt(date.slice(5, 7))) {
                return true;
            }
            if ((parseInt(cur_date.getMonth() + 1)) == parseInt(date.slice(5, 7))) {
                if ((parseInt(cur_date.getDate()) < (parseInt(date.slice(8, 11))))) {
                    return true;
                } else {
                 return false; 
             }
            }
        }
        return false;

    }
};

$(document).ready(function () {

var addTask = document.getElementById('addTask');
if (addTask) {
    var m = new Date();
    addTask.onsubmit = function (e) {

        var taskName = document.getElementById('Taskname').value;
        var project = document.getElementById('choose-project').value;
        if (taskName == "" || taskName == " ") {
            document.getElementById('taskError').innerHTML = "Please Enter Task Name ";
            return false;
        }

        if (project == "" || project == "Select Project") {
            document.getElementById('taskError').innerHTML = "Please Choose Project Name ";
            return false;
        }
            document.getElementById('taskError').innerHTML = " ";
            var date = document.getElementById('date-picker-start').value;
            var start_time = document.getElementById('start-time-0').value;
            var end_time = document.getElementById('end-time-0').value;
            var flag = false;

                flag = addTime.validate();
                if (flag == false) {
                    return false;
                } else {
                    array_of_timings.push({ date, start_time, end_time });
                }
    }
}


    addTime.init("#task-add-time");

    $('.datepicker').datepicker({
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
    });
    $('.datepicker-0').datepicker("setDate", new Date());

    $('.timepicker').timepicker({
        uiLibrary: 'bootstrap4'
    });
    $('.timepicker1').timepicker({
        uiLibrary: 'bootstrap4'
    });
    $('#choose-project').click(function () {
        $('#choose-module').empty().html('<option>Select module</option>');
    })
    var current_time = new Date().getHours().toString()+':'+ new Date().getMinutes();
    document.getElementById('start-time-0').value = current_time;

    $("select.project_name").change(function () {
        var project_id = $(this).children("option:selected").val();
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/user/get_project_module',
            data: { 'id': project_id },
            success: function (res) {
                var result = JSON.parse(res);
                var array = result['result'];
                for (var i = 0; i < array.length; i++) {
                    var module_name = $('<option value=' + array[i]["id"] + '>' + array[i]["name"] + '</option>');
                    $("#choose-module").append(module_name);
                }

            }
        });
    });
});