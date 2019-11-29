var addTask = document.getElementById('addTask');
if (addTask) {
addTask.onsubmit = function (e) {
	var taskName = document.getElementById('Taskname').value;
	var	 project = document.getElementById('chooseProject').value;
	if ((taskName =="" || taskName == " " )) {
		document.getElementById('taskError').innerHTML = "Please Enter Taskname";
		return false;
	}
	else

		return true;
}
}