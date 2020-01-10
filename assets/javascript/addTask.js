
$(document).ready(function () {

    /*   addUser.init("#append-new-user");*/

    $("#chooseProject").change(function () {
        var project_id = $(this).children("option:selected").val();
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_module',
            data: { 'project_id': project_id },
            success: function (res) {
                var result = JSON.parse(res);
                var array = result['result'];
                $("#module").empty();
                for (var i = 0; i < array.length; i++) {
                    var module_name = $('<option value=' + array[i]["id"] + '>' + array[i]["name"] + '</option>');
                    $("#module").append(module_name);
                }
            }
        });
    });

    var addTask = document.getElementById('addTask');
    if (addTask) {
        addTask.onsubmit = function (e) {
            var taskName = document.getElementById('task_name').value;
            var project = document.getElementById('chooseProject').value;
            if ((taskName == "" || taskName == " ")) {
                document.getElementById('taskError').innerHTML = "Please Enter taskname";
                return false;
            }
            else if (project == "Select Project") {
                document.getElementById('taskError').innerHTML = "Please select project name";
                return false;
            }
            else if ((document.getElementById('select-users').value == " ") || ((document.getElementById('select-users').value == ""))) {
                document.getElementById('taskError').innerHTML = "Please select user";
                return false;
            } else
                return true;
        }
    }
})