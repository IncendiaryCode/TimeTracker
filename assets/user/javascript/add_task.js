localStorage.setItem('first_entry', 1)
var addTime = {
	id: 0,
	ele: null,
	addBtn: null,
	array_of_timings: [],
	layout: function(date, start_time, end_time, descri) {
		var section = $('<div class="time-section pt-3 pb-5" />');
		var row = $('<div class="row" />');
		var id = this.id;
		var array_of_timings = this.array_of_timings;

		var colDate = $(
			'<div class="col-4 col-md-5">' +
				'<div class="input-group mb-3">' +
				'<input type="text" class="form-control date-utc datepicker" name="time[' +
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

		var removeBtn = $('<div class="col-1 text-center">' + '<a href="javascript:void(0);" class="ml-0" title="Remove" id="remove-time-' + id + '">' + '<i class="fas fa-minus icon-plus text-white"></i>' + '</a>' + '</div><hr>');
		removeBtn.on('click', function() {
			//remove the row
			$(this).closest('.time-section').remove();
			array_of_timings.pop();
		});
		array_of_timings.push({ date, start_time, end_time });
		removeBtn.appendTo(row);

		section.append(row);
		if (edit == 0) {
			if (end_time == array_of_timings[0]['end_time']) {
				document.getElementById('date-picker-0').value = moment().format('YYYY-MM-DD');
				document.getElementById('start-time-0').value = moment().format('HH:mm');
				document.getElementById('end-time-0').value = "";
			}
			this.ele.find('.primary-wrap').prepend(section);
		} else {
			if(localStorage.getItem('first_entry') == 1)
			{
				var first_entry = document.getElementsByClassName('primary-wrap')[0].childNodes[0].nextElementSibling;
				if(document.getElementsByClassName('primary-wrap')[0].childNodes[0].nextElementSibling)
				{
					document.getElementsByClassName('primary-wrap')[0].childNodes[0].nextElementSibling.remove();
				}
				this.ele.find('.primary-wrap').append(section);
				this.ele.find('.primary-wrap').append(first_entry);
				localStorage.setItem('first_entry', 0)
			}else
			{
				this.ele.find('.primary-wrap').prepend(section);
			}
			if (end_time == array_of_timings[0]['end_time'] && (document.getElementById('date-picker-0') !=undefined)) {
				document.getElementById('date-picker-0').value = moment().format('YYYY-MM-DD');
				document.getElementById('start-time-0').value = moment().format('HH:mm');
				document.getElementById('end-time-0').value = '';
			}
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
		// if(document.getElementById('date-picker-0') == undefined)
		// {
		// 	__this.layout(moment().format('YYYY-MM-DD'), moment().format('HH:mm'), '', '');
		// }
		if(document.getElementById('date-picker-0'))
		{
		var date = document.getElementById('date-picker-0').value;
		var start_time = document.getElementById('start-time-0').value;
		var end_time = document.getElementById('end-time-0').value;
		
		var __start_seconds = parseInt(start_time.slice(0, 2)) * 60 + parseInt(start_time.slice(3, 5));
		var __end_seconds = parseInt(end_time.slice(0, 2)) * 60 + parseInt(end_time.slice(3, 5));
		if(endtimeValidation)
		{
			if (date == '' || date == ' ' || start_time == '' || start_time == ' ' ||  end_time == '' || end_time == ' ') {
				document.getElementById('taskError').innerHTML = 'Please enter end time';
				return false;
			}
			if (startTime == 'Invalid date' || endTime == 'Invalid date') {
				document.getElementById('taskError').innerHTML = 'Please enter valid time';
				return false;
			}
			var cur_sec = parseInt(moment().format("HH"))*60+parseInt(moment().format("mm"));
			if(__end_seconds > cur_sec && (moment("YYYY-MM-DD") == date))
			{
				document.getElementById('taskError').innerHTML = 'Start time cannot greater than current time';
				return false;
			}
		}
		else if(date == '' || date == ' ' || start_time == '' || start_time == ' ') {
			document.getElementById('taskError').innerHTML = 'Please enter valid details...';
			return false;
		}
		
		var cur_sec = parseInt(moment().format("HH"))*60+parseInt(moment().format("mm"));
		if((__start_seconds > cur_sec || __end_seconds > cur_sec) && (moment().format("YYYY-MM-DD") === date))
		{
			document.getElementById('taskError').innerHTML = 'Start/end time cannot greater than current time';
			return false;
		}
		// fetch timings form database
		var t_day = moment(date).format('YYYY-MM-DD');
		var startTime = moment(t_day + ' ' + start_time).format('HH:mm');
		var endTime = moment(t_day + ' ' + end_time).format('HH:mm');
		
		var validate_interval = __this.check_for_timeintervals(__start_seconds, __end_seconds, date);

		
		var check_for_date = __this.check_date(date);
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
		/*else if (__this.check_date(date) || validate_greater_time) {
			document.getElementById("datetime-error").innerHTML =
				"date/time of start/end connot be greater than currnet date/time";
			return false;
		}*/

		if (__start_seconds >= __end_seconds) {
			document.getElementById('taskError').innerHTML = 'Start time cannot be greater or equal to end time.';
			return false;
		} else if (!validate_interval) {
			document.getElementById('taskError').innerHTML = 'Already same task is done in this interval.';
			return false;
		} else {
			document.getElementById('taskError').innerHTML = ' ';
			return true;
			}
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
				var date = document.getElementById('date-picker-0').value;
				var start_time = document.getElementById('start-time-0').value;
				var end_time = document.getElementById('end-time-0').value;
				var descri = document.getElementById('description-0').value;

				_this.layout(date, start_time, end_time, descri);

				var date = dateObj.getFullYear() + '-' + _month + '-' + _day;
				var start_time = _hour + ':' + _min;
				var end_time = ' ';
				var descri = ' ';
			}
		});
		_this.ele.find('a.delete-task').click(function() {
			var table_id = $(this).find('input:hidden').val();
			var _that = $(this);
			$.ajax({
				type: 'POST',
				url: timeTrackerBaseURL + 'user/delete_task_data',
				data: { type: 'delete', id: table_id },
				dataType: 'json',
				success: function(res) {
					//remove the row
					if (res.status) {
						_that.closest('.time-section').remove();
					}
				}
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
	if (document.getElementById('task-add-time')) {
		addTime.init('#task-add-time');
	}
	var addTask = document.getElementById('addTask');
	if (addTask) {
		var m = new Date();
		addTask.onsubmit = function(e) {
			var taskName = document.getElementById('Taskname').value;
			var project = document.getElementById('choose-project').value;
			if (taskName == '' || taskName == ' ') {
				document.getElementById('taskError').innerHTML = 'Please Enter Task Name ';
				return false;
			}
			if (project == '' || project == 'Select Project') {
				document.getElementById('taskError').innerHTML = 'Please Choose Project Name ';
				return false;
			}
			document.getElementById('taskError').innerHTML = ' ';
			var date = document.getElementById('date-picker-0').value;
			var start_time = document.getElementById('start-time-0').value;
			var end_time = document.getElementById('end-time-0').value;


			var flag = false;
			flag = addTime.validate(false);

			if (flag == false) {
				return false;
			} else {
				var elements = document.getElementById('task-times');
				if (elements != null) {
					var input_elements = elements.getElementsByClassName('date-utc');
					var j = 0;
					for (var i = 0; i < input_elements.length / 3; i++) {
						if (input_elements[j].value != '' && input_elements[j + 1].value != ' ') {
							var timeZone = moment.tz.guess();
							var serverStartDate = moment(input_elements[j].value + ' ' + input_elements[j + 1].value).tz('utc').format('Y-MM-DD HH:mm:ss');
							input_elements[j].value = serverStartDate.slice(0, 10);
							if (serverStartDate != 'Invalid date') input_elements[j + 1].value = serverStartDate;
							else{
								document.getElementById('taskError').innerHTML = 'Please enter valid time';
								return false;	
							}
						}
						if (input_elements[j].value != '' && input_elements[j + 2].value != '') {
							var serverEndDate = moment(input_elements[j].value + ' ' + input_elements[j + 2].value).tz('utc').format('Y-MM-DD HH:mm:ss');
							if (input_elements[j + 2].value.length > 2) {
								input_elements[j + 2].value = serverEndDate;
							}
							if (serverEndDate == 'Invalid date') 
							{
								document.getElementById('taskError').innerHTML = 'Please enter valid time';
								return false;	
							}
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

				var flag = false;
				flag = addTime.validate(false);
				if (flag == false) {
					return false;
				}

				if(document.getElementById('start-time-0'))
				{
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
				if ((start_time == '' || start_time == ' ') && (document.getElementById('start-time-0')  == true)) {
					document.getElementById('taskError').innerHTML = 'Please enter start time';
					return false;
				} else {
					var elements = document.getElementById('task-times');
					var input_elements = elements.getElementsByClassName('date-utc');
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
						}else{
							
							document.getElementById('taskError').innerHTML = 'Please enter valid time';
								return false;
						}
						if (input_elements[j].value != '' && input_elements[j + 2].value != '') {
							var serverEndDate = moment(input_elements[j].value + ' ' + input_elements[j + 2].value).tz('utc').format('Y-MM-DD H:mm:ss');
							if (serverEndDate == 'Invalid date') {
								
								document.getElementById('taskError').innerHTML = 'Please enter valid time';
								return false;
							}
							if (input_elements[j + 2].value.length > 2) {
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
	if (edit != undefined || edit != null) {
		if (edit == 0) {
			var date = dateObj.getFullYear() + '-' + _month + '-' + _day;
			if (document.getElementById('date-picker-0')) {
				document.getElementById('date-picker-0').value = date;
			}
		}
	}

	$('.datepicker').datepicker({
		weekStart: 1,
		autoclose: true,
	});

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
		// document.getElementById("choose-module").disabled = true;
		// document.getElementById("save-tasks").disabled = true;
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
					// document.getElementById("choose-module").disabled = false;
					// document.getElementById("save-tasks").disabled = false;
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
});
