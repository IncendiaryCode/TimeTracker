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

$(document).ready(function() {
	var i=1;
	var icon = $('.icon-plus').click(function()
	{
		$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_module',
            data: { 'type': "get_user" },
            success: function(res) {
               var element = $('<select class="form-control mt-3"  id="user-name'+i+'" name="user_name'+i+'">'+
				+'<option>Select User</option>'+
				+'<?php foreach($names as $name){ ?> '+
				+'<option> <?php echo $name["name"]; ?></option> '+
				+' <?php } ?></select>');
				$('#append-new-user').append(element);

            }
        });
		
	})
	i++;
})