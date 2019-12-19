var add_module = {
    username: [],
    i: 1,
    last_row : 0,
    layout: function (i) {
        var _this = this;
        var last_row = _this.last_row;
        var username = _this.username;
        var i = _this.i;
        var row = $('<div class="col-10 assign-module' + i + '"></div>');
        var element = $('<input type="text" class="form-control mt-3"  id="new-module' + i + '" name="new-module[' + i + '][module]" placeholder="Enter module name">');

        row.append(element);
        var row1 = $('<div class="col-2 mt-3 assign-module' + i + '"></div>');
        var addBtn = $('<a href="javascript:void(0);" title="Remove" id="add-module-' + i + '">');
        var icon = $('<i class="fas fa-plus icon-plus text-success"></i></a>');
        addBtn.append(icon);
        addBtn.appendTo(row1);
        if(_this.last_row != 0)
        {
            _this.last_row.addClass('fas fa-minus icon-plus text-danger');
            _this.last_row.removeClass('fas fa-plus');
        }
        $(_this.last_row).click(function()
        {
            $('.assign-module'+i-1).remove();
        })
        _this.last_row = icon;
        
        addBtn.on('click', function () {
        _this.validate();
        });
        $('#append-new-module').append(row);
        $('#append-new-module').append(row1);
    },


    validate: function () {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;
        var id = 'new-module' + (i - 1);
        var module_name = document.getElementById(id).value;
        if ((module_name == "Select User") || (module_name == "") || (module_name == " ")) {
            document.getElementById('module-error').innerHTML = "Please enter module name..";
        } else {
            document.getElementById('module-error').innerHTML = " ";
            for (var k = 0; k < username.length; k++) {
                if ((username[k] == module_name) && (k != 1)) {
                    sameName = 1;
                    break;
                } else sameName = 0;
            }
            if (sameName == 0) {
                __this.layout(__this.i);
                __this.i++;
                username.push(module_name);
            }
            else {
                document.getElementById('module-error').innerHTML = "User name is repeated.";
            }
        }
    },
    attachEvents: function () {
        var _this = this;
        this.addBtn.on('click', function (e) {
            e.preventDefault();
            _this.validate() // validate the timing details


        });
    },
    init: function (eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-module');
        this.attachEvents();
    }
}



var assign = {
    username: [],
    i: 1,
    layout: function (usernames, i) {
        var _this = this;
        var i = _this.i;
        var username = _this.username;
        var row = $('<div class="col-10 assign-new-user' + i + '"></div>');
        var element = $('<select class="form-control mt-3"  id="assign-name' + i + '" name="assign-name[' + i + '][name]">');
        for (var j = 0; j < usernames.length; j++) {
            var option = $('<option>' + usernames[j]["name"] + '</option>');
            option.appendTo(element);
        }
        var end = $('</select>');
        element.append(end);
        row.append(element);
        var row1 = $('<div class="col-2 mt-3 assign-new-user' + i + '"></div>');
        var removeBtn = $('<a href="javascript:void(0);" title="Remove" id="remove-name-' + i + '">' +
            '<i class="fas fa-minus icon-plus text-danger"></i></a>');
        removeBtn.appendTo(row1);
        removeBtn.on('click', function () {
            $('.assign-new-user' + i).remove();
            _this.i--;
            username.pop();
        });
        $('#assign-new-user').append(row);
        $('#assign-new-user').append(row1);
    },


    validate: function () {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;


        var id = 'assign-name' + (i - 1);

        var user = document.getElementById(id).value;
        if ((user == "Select User") || (user == "") || (user == " ")) {
            document.getElementById('module-error').innerHTML = "Please select user..";
        } else {
            for (var k = 0; k < username.length; k++) {
                if ((username[k] == user) && (k != 1)) {
                    sameName = 1;
                    break;
                } else sameName = 0;
            }
            if (sameName == 0) {
                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'index.php/admin/get_username_list',
                    data: { 'type': "get_user" },
                    success: function (res) {
                        document.getElementById('module-error').innerHTML = " ";

                        var result = JSON.parse(res);
                        var usernames = result['users'];
                        __this.layout(usernames, __this.i);
                        __this.i++;
                        username.push(user);
                    }
                }
                );
            }
            else {
                document.getElementById('module-error').innerHTML = "User name is repeated.";
            }
        }
    },
    attachEvents: function () {
        var _this = this;
        this.addBtn.on('click', function (e) {
            e.preventDefault();
            _this.validate() // validate the timing details

        });
    },
    init: function (eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#assign-new-user');
        this.attachEvents();
    }
}

$(document).ready(function () {
    if(document.getElementById("new-project"))
    {
        /*var new_project = document.getElementById("new-project").checked;
        var old_project = document.getElementById("old-project").checked;*/
        if (old_project == true) {
            document.getElementById("new-project").checked = false;
            $('#new-project-input').hide();
            
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
                data: { 'type': "get_user" },
                success: function (res) {
                    var result = JSON.parse(res);
                    usernames = result['result'];
                    for (var j = 0; j < usernames.length; j++) {
                        var option = $('<option>' + usernames[j]["name"] + '</option>');
                        $('.project-list').append(option);
                    }
                }
            });
        }
    }
    /*$('#new-project').click(function()
    {
        $('#old-project-input').hide();
        $('#new-project-input').show();
        document.getElementById("old-project").checked = false;
    });
    $('#old-project').click(function()
    {
        $('#old-project-input').show();
        $('#new-project-input').hide();
        document.getElementById("new-project").checked = false;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
            data: { 'type': "get_user" },
            success: function (res) {
                var result = JSON.parse(res);
                usernames = result['result'];
                console.log(usernames);
                $('.project-list').empty();
                for (var j = 0; j < usernames.length; j++) {
                    if(usernames[j]["project_name"] != null)
                    {
                    var option = $('<option>' + usernames[j]["project_name"] + '</option>');
                    $('.project-list').append(option);
                    }
                }
            }
        });
    });*/
    if(document.getElementById('old-project-input'))
    {
    $('#old-project-input').show();
        $('#new-project-input').hide();
        document.getElementById("new-project").checked = false;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
            data: { 'type': "get_user" },
            success: function (res) {
                var result = JSON.parse(res);
                usernames = result['result'];
                $('.project-list').empty();
                for (var j = 0; j < usernames.length; j++) {
                    if(usernames[j]["project_name"] != null)
                    {
                    var option = $('<option>' + usernames[j]["project_name"] + '</option>');
                    $('.project-list').append(option);
                    }
                }
            }
        });
    }
        
    add_module.init("#append-new-module");
    assign.init("#assign-new-user");
    var addProject = document.getElementById('add-project');
    if (addProject != null) {
        addProject.onsubmit = function (e) {
            var project_title = document.getElementById('project-name').value;
            if ((project_title == "" || project_title == " ")) {
                document.getElementById('module-error').innerHTML = "Enter Project name";
                return false;
            }
                    var project_name = document.getElementById('old-project-input').value;
                    var user_name = document.getElementById('assign-name0').value;
                    if (project_name == "" || project_name == " ") {
                        document.getElementById('module-error').innerHTML = "Enter Project name";
                        return false;
                    } else if (user_name == "Select User") {
                        document.getElementById('module-error').innerHTML = "Enter user name";
                        return false;
                    }
        }
    }


})










