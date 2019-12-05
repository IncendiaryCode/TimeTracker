$(document).ready(function() {
    var i = 0;
    var user_name = [];
    var sameName = 0;
    var icon = $('.icon-plus').click(function() {
        var id = "user-name" + i;
        var user = document.getElementById(id).value;
        if ((user == "Select User") || (user == "") || (user == " ")) {
            document.getElementById('user-name-error').innerHTML = "Please select user.."
        } else {
            for (var k = 0; k < user_name.length; k++) {
                if ((user_name[k] == user) && (k != 1)) {
                    sameName = 1;
                    break;
                } else sameName = 0;
                console.log(user_name[k], user);
            }
            if (sameName == 0) {
                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'index.php/admin/get_username_list',
                    data: { 'type': "get_user" },
                    success: function(res) {
                        document.getElementById('user-name-error').innerHTML = " ";
                        var result = JSON.parse(res);
                        var user_names = result['users'];
                        i++;
                        var element = $('<select class="form-control mt-3"  id="user-name' + i + '"user-name[' + i + '][name]"><option>Select User</option>');
                        for (var j = 0; j < user_names.length; j++) {
                            var option = $('<option>' + user_names[j]["name"] + '</option>');
                            
                            element.append(option);
                        }
                        var end = $('</select>');
                        option.append(end);
                        user_name.push(user);
                        $('#append-new-user').append(element);

                    }
                });
            } else {
                document.getElementById('user-name-error').innerHTML = "user name is repeated.."
            }
        }
    })

    $("#chooseProject").change(function() {
        var project_name = $(this).children("option:selected").val();
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_module',
            data: { 'project name': project_name },
            success: function(res) {
                var result = JSON.parse(res);
                var array = result['result'];
                for (var i = 0; i < array.length; i++) {
                    var module_name = $('<option value=' + array[i]["id"] + '>' + array[i]["name"] + '</option>');
                    $("#module").append(module_name);
                }
            }
        });
    });

    var addTask = document.getElementById('addTask');
    if (addTask) {
        addTask.onsubmit = function(e) {
            var taskName = document.getElementById('Taskname').value;
            var project = document.getElementById('chooseProject').value;
            if ((taskName == "" || taskName == " ")) {
                document.getElementById('taskError').innerHTML = "Please Enter Taskname";
                return false;
            } else
                return true;
        }
    }
})