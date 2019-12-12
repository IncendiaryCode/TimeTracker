$(document).ready(function()
{
	var form_edit = document.getElementById('editTask');
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
});