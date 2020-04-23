localStorage.setItem('first_entry', 1);
var addTime = {
    id: 0,
    ele: null,
    addBtn: null,
    array_of_timings: [],
    layout: function(date, start_time, end_time, descri) {
        if (start_time == undefined) {
            start_time = moment().format('HH:mm');
        }
        if (end_time == undefined) {
            end_time = '';
        }
        if (descri == undefined) {
            descri = '';
        }
        if (date == undefined) {
            date = moment().format('YYYY-MM-DD');
        }
        var section = $('<div class="time-section pt-3 pb-3" />');
        var row = $('<div class="row animated fadeIn" />');
        var id = this.id;
        if (edit == 1 && localStorage.getItem('first_entry') == 1) {
            id = 0;
            localStorage.setItem('first_entry', 0);
        } else {
            id = this.id;
        }
        if (edit == 0 && document.getElementById('date-picker-0') == null) {
            id = 0;
        }
        var array_of_timings = this.array_of_timings;
        var colDate = $(
            '<div class="col-4 col-md-5">' +
            '<div class="input-group date mb-3">' +
            '<input type="text" class="form-control date-utc datepicker pl-3" name="time[' +
            id +
            '][date]" data-date-format="yyyy-mm-dd" id="date-picker-' +
            id +
            '" value="' +
            date +
            '" >' +
            '<div class="input-group-append">' +
            '<span class="input-group-text" id="basic-addon-' +
            id +
            '">' +
            '<span class="fa fa-calendar"></span>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>'
        );
        colDate.appendTo(row);

        var colStartTime = $(
            '<div class="col-4 col-md-3">' + '<div class="input-group">' + '<input id="start-time-' + id + '" class="form-control date-utc timepicker" data-date-format="hh:mm:ss" name="time[' + id + '][start]" value="' + start_time + '" placeholder="hh:mm" />' + '</div>' + '</div>'
        );
        colStartTime.appendTo(row);

        var colEndTime = $('<div class="col-4 col-md-3">' + '<div class="input-group">' + '<input id="end-time-' + id + '"  class="form-control date-utc timepicker1" data-date-format="hh:mm:ss" name="time[' + id + '][end]" value="' + end_time + '" placeholder="hh:mm" />' + '</div>' + '</div>');
        colEndTime.appendTo(row);

        var colDescri = $('<div class="col-11">' + '<div class="input-group">' + '<input id="description-' + id + '"  class="form-control"  name="time[' + id + '][task_description]" value="' + descri + '" placeholder="description" />' + '</div>' + '</div>');
        colDescri.appendTo(row);

        var removeBtn = $('<div class="col-1 text-center">' + '<a href="javascript:void(0);" class="ml-0 remove-timeline" id="remove-time-' + id + '">' + '<i class="fas fa-minus icon-plus text-white"></i>' + '</a>' + '</div><hr>');
        removeBtn.on('click', function() {
            //remove the row
            __this_new = this;
            $('#alert_for_delete').modal('show');
            $('#alert_for_delete_true').click(function() {
                $(__this_new).closest('.time-section').remove();
                $('#alert_for_delete').modal('hide');
                array_of_timings.pop();
            });
        });
        array_of_timings.push({ date, start_time, end_time });
        removeBtn.appendTo(row);

        section.append(row);
        if (edit == 0) {
            this.ele.find('.primary-wrap').append(section);
            document.getElementById('date-picker-0').value = moment().format('YYYY-MM-DD');
            document.getElementById('start-time-0').value = moment().format('HH:mm');
            document.getElementById('end-time-0').value = '';
        } else {
            if (document.getElementById('date-picker-0') == null) {
                this.ele.find('.primary-wrap').prepend(section);
            } else {
                // var prnt_node = document.getElementById('date-picker-0');
                // $(prnt_node.parentNode.parentNode.parentNode.parentNode).prepend(section);
                this.ele.find('.primary-wrap').append(section);
            }
            document.getElementById('date-picker-0').value = moment().format('YYYY-MM-DD');
            document.getElementById('start-time-0').value = moment().format('HH:mm');
            document.getElementById('end-time-0').value = '';
        }
        section.find('.timepicker').timepicker({
            mode: '24hr',
            format: 'HH:MM',
            uiLibrary: 'bootstrap4'
        });
        section.find('.timepicker1').timepicker({
            mode: '24hr',
            format: 'HH:MM',
            uiLibrary: 'bootstrap4'
        });
        section.find('.datepicker').datepicker({
            weekStart: 1,
            autoclose: true,
            todayHighlight: true
        });
    },
    validate: function(endtimeValidation) {
        var __this = this;
        if (document.getElementById('date-picker-0')) {
            var date = document.getElementById('date-picker-0').value;
            var start_time = document.getElementById('start-time-0').value;
            var end_time = document.getElementById('end-time-0').value;

            var __start_seconds = parseInt(start_time.slice(0, 2)) * 60 + parseInt(start_time.slice(3, 5));
            var __end_seconds = parseInt(end_time.slice(0, 2)) * 60 + parseInt(end_time.slice(3, 5));

            var total_start_sec;
            var total_end_sec;
            if (end_time != "") {

                if ((start_time.split(':')[1]) == undefined) {
                    total_start_sec = parseInt(start_time) * 60;
                } else {

                    total_start_sec = parseInt(start_time.split(':')[0] * 60) + parseInt(start_time.split(':')[1]);
                }
                if ((end_time.split(':')[1]) == undefined) {
                    total_end_sec = parseInt(end_time) * 60;
                } else {
                    total_end_sec = parseInt(end_time.split(':')[0] * 60) + parseInt(end_time.split(':')[1]);
                }
                if (total_start_sec >= total_end_sec) {
                    document.getElementById('taskError').innerHTML = 'Start time cannot greater than or equal to end time';
                    return false;
                }
            }

            if (endtimeValidation) {
                if (start_time == '' || start_time == ' ' || end_time == '' || end_time == ' ') {
                    document.getElementById('taskError').innerHTML = 'Please enter valid time';
                    return false;
                }
                if (startTime == 'Invalid date' || endTime == 'Invalid date') {
                    document.getElementById('taskError').innerHTML = 'Please enter valid time';
                    return false;
                }
                var cur_sec = parseInt(moment().format('HH')) * 60 + parseInt(moment().format('mm'));
                if (__end_seconds > cur_sec && moment('YYYY-MM-DD') == date) {
                    document.getElementById('taskError').innerHTML = 'Start time cannot greater than current time';
                    return false;
                }
            } else {
                if (start_time == '' || start_time == ' ') {
                    document.getElementById('taskError').innerHTML = 'Please enter start time';
                    return false;
                }

            }

            var cur_sec = parseInt(moment().format('HH')) * 60 + parseInt(moment().format('mm'));

            if (__start_seconds > cur_sec && (moment().format('YYYY-MM-DD') === date) && end_time == '') {
                document.getElementById('taskError').innerHTML = 'Start time cannot greater than current time';
                return false;
            }


            // fetch timings form database
            var t_day = moment(date).format('YYYY-MM-DD');
            var startTime = moment(t_day + ' ' + start_time).format('HH:mm');
            var endTime = moment(t_day + ' ' + end_time).format('HH:mm');

            var validate_interval = __this.check_for_timeintervals(__start_seconds, __end_seconds, date);
            var check_for_date = __this.check_date(date);
            if (check_for_date) {
                document.getElementById('taskError').innerHTML = 'You cannot add future task';
                return false;
            }
            var validate_greater_time = __this.check_for_greatertime(date, start_time, end_time);
            if (endtimeValidation) {
                if (date == '' || date == ' ') {
                    document.getElementById('taskError').innerHTML = 'Enter date';
                    return false;
                }
                if (start_time == '' || start_time == ' ') {
                    document.getElementById('taskError').innerHTML = 'Enter start time to add task';
                    return false;
                }
                if (end_time == '' || end_time == ' ') {
                    document.getElementById('taskError').innerHTML = 'Enter end time to add task.';
                    return false;
                }
            }

            if (__start_seconds >= __end_seconds) {
                document.getElementById('taskError').innerHTML = 'Start time cannot be greater or equal to end time.';
                return false;
            } else if (!validate_interval && (!edit)) {
                document.getElementById('taskError').innerHTML = 'Already same task is done in this interval.';
                return false;
            } else {
                document.getElementById('taskError').innerHTML = ' ';
                return true;
            }
        } else {
            return true;
        }
    },
    check_for_timeintervals: function(__start_seconds, __end_seconds, date) {
        // check current task is already entered.
        var _this = this;
        // var element = document.getElementById("task-add-time");
        var input_values = this.array_of_timings;
        if (input_values.length > 0) {
            for (var i = 0; i < input_values.length; i++) {
                var old_date = input_values[i]['date'];
                var start_old_time_sec = parseInt(input_values[i]['start_time'].slice(0, 2)) * 60 + parseInt(input_values[i]['start_time'].slice(3, 5));
                var end_old_time_sec = parseInt(input_values[i]['end_time'].slice(0, 2)) * 60 + parseInt(input_values[i]['end_time'].slice(3, 5));

                if (start_old_time_sec <= __start_seconds && __start_seconds < end_old_time_sec && date == old_date) {
                    return false;
                }
                if (start_old_time_sec >= __start_seconds && __end_seconds > start_old_time_sec && date == old_date) {
                    return false;
                }
            }
        } else {
            return true;
        }
        return true;
    },
    check_for_greatertime: function(date, start_time, end_time) {
        // to check whether entered timings is greater than current time or not.
        var __this = this;

        var cur_date = new Date();

        var cur_time = cur_date.getHours() + ':' + cur_date.getMinutes();
        if (parseInt(end_time.slice(0, 2)) > parseInt(cur_time.slice(0, 2))) {
            if (parseInt(date.slice(0, 4)) == cur_date.getFullYear() && parseInt(date.slice(5, 7)) == cur_date.getMonth() + 1 && parseInt(date.slice(9, 11)) == cur_date.getDate()) {
                return true;
            }
        } else {
            if (parseInt(end_time.slice(3, 5)) > parseInt(cur_time.slice(3, 5))) {
                if (parseInt(date.slice(0, 4)) == cur_date.getFullYear() && parseInt(date.slice(5, 7)) == cur_date.getMonth() + 1 && parseInt(date.slice(9, 11)) == cur_date.getDate()) {
                    return true;
                }
            }
        }
        return false;
    },
    attachEvents: function() {
        var _this = this;
        this.addBtn.on('click', function(e) {
            e.preventDefault();
            if (document.getElementById('date-picker-0') == null && edit == 0) {
                _this.layout();
                document.getElementById('taskError').innerHTML = '';
            }
            if (_this.validate(true)) {
                // validate the timing details
                _this.id++;
                //prepare prefill data
                var dateObj = new Date();
                var _month = dateObj.getMonth() + 1;
                if (_month.toString().length == 1) {
                    _month = '0' + _month.toString();
                }
                var _day = dateObj.getDate();
                if (_day.toString().length == 1) {
                    _day = '0' + _day.toString();
                }

                var _hour = dateObj.getHours();
                if (_hour.toString().length == 1) {
                    _hour = '0' + _hour.toString();
                }

                var _min = dateObj.getMinutes();
                if (_min.toString().length == 1) {
                    _min = '0' + _min.toString();
                }
                if (document.getElementById('date-picker-0')) {
                    var date = document.getElementById('date-picker-0').value;
                    var start_time = document.getElementById('start-time-0').value;
                    var end_time = document.getElementById('end-time-0').value;
                    var descri = document.getElementById('description-0').value;
                    _this.layout(date, start_time, end_time, descri);
                } else {
                    //_this.layout(moment().format("YYYY-MM-DD"),moment().format("HH:mm"),"","");
                }
                var date = dateObj.getFullYear() + '-' + _month + '-' + _day;
                var start_time = _hour + ':' + _min;
                var end_time = ' ';
                var descri = ' ';
            }
        });
        _this.ele.find('a.delete-task').click(function() {
            var table_id = $(this).find('input:hidden').val();
            var _that = $(this);
            $('#alert_for_delete').modal('show');
            $('#alert_for_delete_true').click(function() {
                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'user/delete_task_data',
                    data: { type: 'delete', id: table_id },
                    dataType: 'json',
                    success: function(res) {
                        //remove the row
                        if (res.status) {
                            $('#alert_for_delete').modal('hide');
                            _that.closest('.time-section').remove();
                        }
                    }
                });
            });
        });
    },
    check_date: function(date) {
        // check entered date with current date.
        var cur_date = new Date();
        if (parseInt(cur_date.getFullYear()) < parseInt(date.slice(6, 10))) {
            return true;
        }
        if (parseInt(cur_date.getFullYear()) == parseInt(date.slice(0, 4))) {
            if (parseInt(cur_date.getMonth() + 1) > parseInt(date.slice(5, 7))) {
                return false;
            }
            if (parseInt(cur_date.getMonth() + 1) < parseInt(date.slice(5, 7))) {
                return true;
            }
            if (parseInt(cur_date.getMonth() + 1) == parseInt(date.slice(5, 7))) {
                if (parseInt(cur_date.getDate()) < parseInt(date.slice(8, 11))) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    },
    init: function(eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-time');
        this.id = this.ele.find('.time-section').length;
        this.attachEvents();
    }
};

$(document).ready(function() {
    if (document.getElementById('task-times')) {
        addTime.init('#task-times');
    }

    $('#remove-time-0').click(function() {
        $('#alert_for_delete').modal('show');
        $('#alert_for_delete_true').click(function() {
            $('#alert_for_delete').modal('hide');
            document.getElementById('taskError').innerHTML = '';
            $('.remove-first-timeline').remove();
        });
    });
    var addTask = document.getElementById('addTask');
    if (addTask) {
        var m = new Date();
        addTask.onsubmit = function(e) {
            var taskName = document.getElementById('Taskname').value;
            var project = document.getElementById('choose-project').value;
            var sel = document.getElementById("choose-module")
            document.getElementById('selected-module').value = sel.options[sel.selectedIndex].text;
            if (taskName == '' || taskName == ' ') {
                document.getElementById('taskError').innerHTML = 'Please Enter Task Name ';
                return false;
            }
            if (project == '' || project == 'Select Project') {
                document.getElementById('taskError').innerHTML = 'Please Choose Project Name ';
                return false;
            }
            document.getElementById('taskError').innerHTML = ' ';
            var flag = false;
            flag = addTime.validate(false);

            if (flag == false) {
                return false;
            } else {
                var elements = document.getElementById('task-times');
                if (elements != null) {
                    var input_elements = elements.getElementsByClassName('date-utc');
                    var k = 0;
                    for (var i = 0; i < input_elements.length / 3; i++) {
                        if (input_elements[k].value != '' && input_elements[k + 1].value != ' ') {
                            var serverStartDate = moment(input_elements[k].value + ' ' + input_elements[k + 1].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            if (serverStartDate == 'Invalid date') {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            }
                        }
                        if (input_elements[k].value != '' && input_elements[k + 2].value != '') {
                            var serverEndDate = moment(input_elements[k].value + ' ' + input_elements[k + 2].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            if (serverEndDate == 'Invalid date') {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            }
                        }
                        k = k + 3;
                    }
                    var j = 0;
                    for (var i = 0; i < input_elements.length / 3; i++) {
                        if (input_elements[j].value != '' && input_elements[j + 1].value != ' ') {
                            var timeZone = moment.tz.guess();
                            var serverStartDate = moment(input_elements[j].value + ' ' + input_elements[j + 1].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            input_elements[j].value = serverStartDate.slice(0, 10);
                            if (serverStartDate != 'Invalid date') input_elements[j + 1].value = serverStartDate;
                        }
                        if (input_elements[j].value != '' && input_elements[j + 2].value != '') {
                            var serverEndDate = moment(input_elements[j].value + ' ' + input_elements[j + 2].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            input_elements[j + 2].value = serverEndDate;
                        }
                        j = j + 3;
                    }
                }
                return true;
            }
        };
    }
    if (document.getElementById('editTask') != undefined) {
        var editTask = document.getElementById('editTask');
        if (editTask) {
            editTask.onsubmit = function(e) {
                var taskName = document.getElementById('Taskname').value;
                var project = document.getElementById('choose-project').value;
                var start_time;

                var sel = document.getElementById("choose-module")
                document.getElementById('selected-module').value = sel.options[sel.selectedIndex].text;
                var flag = false;
                flag = addTime.validate(false);
                if (flag == false) {
                    return false;
                }

                if (document.getElementById('start-time-0')) {
                    start_time = document.getElementById('start-time-0').value;
                }
                if (taskName == '' || taskName == ' ') {
                    document.getElementById('taskError').innerHTML = 'Please Enter Task Name ';
                    return false;
                }
                if (project == '' || project == 'Select Project') {
                    document.getElementById('taskError').innerHTML = 'Please Choose Project Name ';
                    return false;
                }
                if ((start_time == '' || start_time == ' ') && document.getElementById('start-time-0') == true) {
                    document.getElementById('taskError').innerHTML = 'Please enter start time';
                    return false;
                } else {
                    var elements = document.getElementById('task-times');
                    var input_elements = elements.getElementsByClassName('date-utc');
                    var k = 0;
                    for (var i = 0; i < input_elements.length / 3; i++) {
                        if (input_elements[k].value != '' && input_elements[k + 1].value != ' ') {
                            var serverStartDate = moment(input_elements[k].value + ' ' + input_elements[k + 1].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            if (serverStartDate == 'Invalid date') {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            }
                        }
                        if (input_elements[k].value != '' && input_elements[k + 2].value != '') {
                            var serverEndDate = moment(input_elements[k].value + ' ' + input_elements[k + 2].value).tz('utc').format('Y-MM-DD HH:mm:ss');
                            if (serverEndDate == 'Invalid date') {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            }
                        }
                        //validate timings
                        if ((input_elements[k + 1].value == '' || input_elements[k + 2].value == '') && input_elements[k].value != moment().format('Y-MM-DD')) {
                            document.getElementById('taskError').innerHTML = 'Start/end time can not be empty ';
                            return false;
                        }
                        var total_start_sec;
                        var total_end_sec;
                        if ((input_elements[k + 1].value.split(':')[1]) == undefined) {
                            total_start_sec = parseInt(input_elements[k + 1].value) * 60;
                        } else {

                            total_start_sec = parseInt(input_elements[k + 1].value.split(':')[0] * 60) + parseInt(input_elements[k + 1].value.split(':')[1]);
                        }
                        if ((input_elements[k + 2].value.split(':')[1]) == undefined) {
                            total_end_sec = parseInt(input_elements[k + 2].value) * 60;
                        } else {
                            total_end_sec = parseInt(input_elements[k + 2].value.split(':')[0] * 60) + parseInt(input_elements[k + 2].value.split(':')[1]);
                        }
                        if (total_start_sec >= total_end_sec) {
                            document.getElementById('taskError').innerHTML = 'Start time cannot greater than or equal to end time';
                            return false;
                        }

                        if (addTime.check_date(input_elements[k].value)) {
                            document.getElementById('taskError').innerHTML = 'You cannot add future task';
                            return false;
                        } else {
                            var date = input_elements[k].value;
                            var start_time = input_elements[k + 1].value;
                            var end_time = input_elements[k + 2].value;
                            addTime.array_of_timings.push({ date, start_time, end_time });
                        }
                        k = k + 3;
                    }

                    var initial_start_min = parseInt(addTime.array_of_timings[0]['start_time'].split(':')[0] * 60) + parseInt(addTime.array_of_timings[0]['start_time'].split(':')[1]);
                    var initial_end_min = parseInt(addTime.array_of_timings[0]['end_time'].split(':')[0] * 60) + parseInt(addTime.array_of_timings[0]['end_time'].split(':')[1]);
                    var initial_date = addTime.array_of_timings[0]['date'];
                    for (var i = 1; i < addTime.array_of_timings.length; i++) {
                        var cur_start_min = parseInt(addTime.array_of_timings[i]['start_time'].split(':')[0] * 60) + parseInt(addTime.array_of_timings[i]['start_time'].split(':')[1]);
                        var cur_end_time = parseInt(addTime.array_of_timings[i]['end_time'].split(':')[0] * 60) + parseInt(addTime.array_of_timings[i]['end_time'].split(':')[1]);
                        var cur_date = addTime.array_of_timings[i]['date'];
                        if (initial_start_min <= cur_start_min && cur_start_min < initial_end_min && cur_date == initial_date) {
                            document.getElementById('taskError').innerHTML = 'Already same task is done in this interval.';
                            addTime.array_of_timings = [];
                            return false;
                        } else if (cur_end_time == 'NaN') {
                            if ((initial_end_min > cur_start_min) && (cur_date == initial_date)) {
                                document.getElementById('taskError').innerHTML = 'Already same task is done in this interval.';
                                addTime.array_of_timings = [];
                                return false;
                            }
                        } else {
                            initial_start_min = cur_start_min;
                            initial_end_min = cur_end_time;
                            initial_date = cur_date;
                        }
                    }

                    var j = 0;
                    for (var i = 0; i < input_elements.length / 3; i++) {
                        if (input_elements[j].value != '' && input_elements[j + 1].value != '') {
                            var serverStartDate = moment(input_elements[j].value + ' ' + input_elements[j + 1].value).tz('utc').format('Y-MM-DD H:mm:ss');
                            input_elements[j].value = serverStartDate.slice(0, 10);
                            if (serverStartDate != 'Invalid date') {
                                input_elements[j + 1].value = serverStartDate;
                            } else {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            }
                        } else {
                            document.getElementById('taskError').innerHTML = 'Please enter valid time';
                            return false;
                        }
                        if (input_elements[j].value != '' && input_elements[j + 2].value != '') {
                            var serverEndDate = moment(input_elements[j].value + ' ' + input_elements[j + 2].value).tz('utc').format('Y-MM-DD H:mm:ss');
                            if (serverEndDate == 'Invalid date') {
                                document.getElementById('taskError').innerHTML = 'Please enter valid time';
                                return false;
                            } else {
                                input_elements[j + 2].value = serverEndDate;
                            }
                        }
                        j = j + 3;
                    }
                    return true;
                }
            };
        }
    }
    var dateObj = new Date();
    var _month = dateObj.getMonth() + 1;
    if (_month.toString().length == 1) {
        _month = '0' + _month.toString();
    }
    var _day = dateObj.getDate();
    if (_day.toString().length == 1) {
        _day = '0' + _day.toString();
    }
    if (typeof edit != 'undefined') {
        if (edit == 0) {
            var date = dateObj.getFullYear() + '-' + _month + '-' + _day;
            if (document.getElementById('date-picker-0')) {
                document.getElementById('date-picker-0').value = date;
            }
        }
    }

    $('.timepicker-a').timepicker({
        mode: '24hr',
        format: 'HH:MM',
        uiLibrary: 'bootstrap4'
    });
    $('.timepicker-b').timepicker({
        mode: '24hr',
        format: 'HH:MM',
        uiLibrary: 'bootstrap4'
    });
    $('#choose-project').change(function() {
        $('#choose-module').empty().html('<option>Select module</option>');
    });
    if (document.getElementById('start-time-0')) {
        var current_time = new Date();
        var hrs = current_time.getHours().toString();
        if (hrs.length == 1) {
            hrs = '0' + hrs;
        }
        var min = current_time.getMinutes().toString();
        if (min.length == 1) {
            min = '0' + min;
        }
        var start_time = hrs + ':' + min;
        document.getElementById('start-time-0').value = start_time;
    }

    $('select.project_name').change(function() {
        var project_id = $(this).children('option:selected').val();
        document.getElementById('choose-module').disabled = true;
        document.getElementById('save-tasks').disabled = true;
        if (project_id != 'Select Project') {
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/user/get_project_module',
                data: { id: project_id },
                success: function(res) {
                    var result = JSON.parse(res);
                    var array = result['result'];
                    for (var i = 0; i < array.length; i++) {
                        var module_name = $('<option value=' + array[i]['id'] + '>' + array[i]['name'] + '</option>');
                        $('#choose-module').append(module_name);
                    }
                    document.getElementById('choose-module').disabled = false;
                    document.getElementById('save-tasks').disabled = false;
                }
            });
        }
    });

    if (document.getElementById('task-len')) {
        var len = document.getElementById('task-len').value;
        for (var i = 0; i < len * 2; i++) {
            $('.timepicker-' + i).timepicker({
                mode: '24hr',
                format: 'HH:MM',
                uiLibrary: 'bootstrap4'
            });
        }
    }
    $('#add-new-time').click(function() {
        var flag = 0;
        var elements = document.getElementById('task-times');
        var input_elements = elements.getElementsByClassName('date-utc');
        for (var i = 0; i < input_elements.length; i++) {
            if (input_elements[i].value == '') {
                flag = 1;
            }
        }
        if (edit == 1 && localStorage.getItem('first_entry') == 1) {
            if (flag == 0) {
                document.getElementById('taskError').innerHTML = ' ';
                addTime.layout(moment().format('YYYY-MM-DD'), moment().format('HH:mm'), '', '');
            } else {
                document.getElementById('taskError').innerHTML = 'Please enter end time';
            }
        }
        if (edit == 1 && document.getElementById('date-picker-0') == null) {
            if (flag == 0) {
                document.getElementById('taskError').innerHTML = ' ';
                addTime.layout(moment().format('YYYY-MM-DD'), moment().format('HH:mm'), '', '');
            }
        }
    });

    $('#task-add-time .input-group.date').datepicker({
        weekStart: 1,
        autoclose: true,
        format: 'yyyy-mm-dd'
    });
});