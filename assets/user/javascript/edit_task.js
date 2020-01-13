function validate_timings()
{
for (var i = 0; i < parseInt(document.getElementById('task-len').value); i++) {
	var start_id = document.getElementById('start' + i).value;
	var end_id = document.getElementById('end' + i).value;
	var start_old_time_sec = (parseInt(start_id.slice(0, 2)) * 60 + parseInt(start_id.slice(3, 5)));
    var end_old_time_sec = (parseInt(end_id.slice(0, 2)) * 60 + parseInt(end_id.slice(3, 5)));            
    if (start_old_time_sec >= end_old_time_sec) {
        return false;
    }return true;
	}
	return true;
}

$(document).ready(function () {

var len = document.getElementById('task-len').value;
for (var i = 0; i < (len*2); i++) {
	$('.timepicker'+i).timepicker({
		uiLibrary: 'bootstrap4'
	});

}

var form_edit = document.getElementById('editTask');
if (form_edit) {
	form_edit.onsubmit = function (e) {
	/*var validateTime = validate_timings();
		 for (var i = 0; i < parseInt(document.getElementById('task-len').value); i++) {
			var start_id = document.getElementById('start' + i).value;
			var end_id = document.getElementById('end' + i).value;
			var start_old_time_sec = (parseInt(start_id.slice(0, 2)) * 60 + parseInt(start_id.slice(3, 5)));
		    var end_old_time_sec = (parseInt(end_id.slice(0, 2)) * 60 + parseInt(end_id.slice(3, 5)));            
		    if (start_old_time_sec >= end_old_time_sec) {
		        return false;
		    }
		}*/
		return true;
	}
}
	$('#timepicker').timepicker({
		uiLibrary: 'bootstrap4'
	});
});
