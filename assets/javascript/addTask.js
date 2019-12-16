var addUser = {
    username: [],
    i: 1,
    layout: function(usernames, i) {
        var _this = this;
        var i = _this.i;
        var username = _this.username;
        var row = $('<div class="col-10 assign-user' + i + '"></div>');
        var element = $('<select class="form-control mt-3"  id="user-name' + i + '" name="user-name[' + i + '][name]"><option>Select User</option>');
        for (var j = 0; j < usernames.length; j++) {
            var option = $('<option>' + usernames[j]["name"] + '</option>');
            option.appendTo(element);
        }
            var end = $('</select>');
            element.append(end);
            row.append(element);
        var row1 = $('<div class="col-2 mt-3 assign-user' + i + '"></div>');
        var removeBtn = $('<a href="javascript:void(0);" title="Remove" id="remove-time-' + i + '">' +
            '<i class="fas fa-minus icon-plus text-danger"></i></a>');
        removeBtn.appendTo(row1);
        removeBtn.on('click', function() {
            $('.assign-user' + i).remove();
            _this.i--;
            username.pop();
        });
            $('.assign-user').append(row);
            $('.assign-user').append(row1);
    },

    validate: function() {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;

        
        var id = 'user-name' + (i-1);

        console.log('i', i);
        var user = document.getElementById(id).value;
        if ((user == "Select User") || (user == "") || (user == " ")) {
            document.getElementById('user-name-error').innerHTML = "Please select user..";
        } else {
            for (var k = 0; k < username.length; k++) {
                if ((username[k] == user) && (k != 1)) {
                    sameName = 1;
                    console.log(username[k], user);
                    break;
                } else sameName = 0;
            }
            if (sameName == 0) {
                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'index.php/admin/get_username_list',
                    data: { 'type': "get_user" },
                    success: function(res) {
                        document.getElementById('user-name-error').innerHTML = " ";
                        var result = JSON.parse(res);
                        var usernames = result['users'];
                        __this.layout(usernames, __this.i);
                        __this.i++;
                        username.push(user);
                    }
                });
            }
            else{
                document.getElementById('user-name-error').innerHTML = "User name is repeated.";
            }
        }
    },
    attachEvents: function() {
        var _this = this;
        this.addBtn.on('click', function(e) {
            e.preventDefault();
            console.log("clicked");
            _this.validate() // validate the timing details
               

        });
    },
    init: function(eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-user');
        this.attachEvents();
    }
}

$(document).ready(function() {

    addUser.init("#append-new-user");

    $("#chooseProject").change(function() {
        var project_id = $(this).children("option:selected").val();
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_module',
            data: { 'project_id': project_id },
            success: function(res) {
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