

function stop_or_complete(flag) {
	var taskid = document.getElementById('curr-taskid').value;
    $.ajax({
        type: "POST",
        url: timeTrackerBaseURL + 'index.php/user/stop_timer',
        data: { 'action': 'task', 'id': taskid, 'flag': flag },
        //call to stop the task timer.
        dataType: 'json',
        success: function (res) {
            //handle timer
            document.getElementById("user-alerting").innerHTML = res['msg'];
            setTimeout(function(){
                document.getElementById("user-alerting").innerHTML = '';
            }, 5000);
            window.location.reload();
        }
    });
}

//checking for stop or complete the task.
var timerStopModal = function () {
    var timerModal = $('#timestopmodal').modal({
        'show': false,
        'backdrop': 'static',
    });

    timerModal.on('shown.bs.modal', function (e) {
    });

    timerModal.on('hidden.bs.modal', function (e) {
        startTimer(localStorage.getItem('timeStamp'));
    });

    var flag = 0;
    var completeBtn = timerModal.find('button#timestopmodal-complete-task');

    completeBtn.unbind().on('click', function () {
        flag = 1;
        stop_or_complete(flag);
    });

    var stopBtn = timerModal.find('button#timestopmodal-stop-task');
    flag = 0;
    stopBtn.unbind().on('click', function () {
        stop_or_complete(flag);
    });
    return timerModal;
};

$(document).ready(function()
{
var taskid = document.getElementById('curr-taskid').value;
var timerModal = timerStopModal();
$('#stop-or-complete').on('click', function () {
    timerModal.modal('show');
});

	var form_edit = document.getElementById('editTask');
	if(form_edit)
	{
	form_edit.onsubmit = function(e) {

	var x = document.getElementById("total-row").childElementCount/4
	for(var i=0; i<x; i++)
	{
		var start_id = 'start'+i;
		var end_id = 'end'+i;
		var start_date = document.getElementById(start_id).value.slice(0,10);
		var end_date = document.getElementById(end_id).value.slice(0,10);

		if(start_date != end_date)
		{
			document.getElementById('taskError').innerHTML = "Start date and end date of the task should be same..";
			return false;
		}
	} return true;
}
}
});