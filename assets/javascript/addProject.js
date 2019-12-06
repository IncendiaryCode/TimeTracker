var add_module = {
    username: [],
    i: 1,
    layout: function(i) {
        var _this = this;
        var username = _this.username;
        var i = _this.i;
        var row = $('<div class="col-10 assign-module' + i + '"></div>');
        var element = $('<input type="text" class="form-control mt-3"  id="new-module' + i + '" name="new-module[' + i + '][module]" placeholder="Enter module name">');
        
            row.append(element);
        var row1 = $('<div class="col-2 mt-3 assign-module' + i + '"></div>');
        var removeBtn = $('<a href="javascript:void(0);" title="Remove" id="remove-module-' + i + '">' +
            '<i class="fas fa-minus icon-plus text-danger"></i></a>');
        removeBtn.appendTo(row1);
        removeBtn.on('click', function() {
            $('.assign-module' + i).remove();

            _this.i--;
            username.pop();
        });
            $('#append-new-module').append(row);
            $('#append-new-module').append(row1);
    },


    validate: function() {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;

        var id = 'new-module' + (i-1);
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
            else{
                document.getElementById('module-error').innerHTML = "User name is repeated.";
            }
        }
    },
    attachEvents: function() {
        var _this = this;
        this.addBtn.on('click', function(e) {
            e.preventDefault();
            _this.validate() // validate the timing details
               

        });
    },
    init: function(eleID) {
        //initial settings
        this.ele = $(eleID);
        this.addBtn = this.ele.find('#add-new-module');
        this.attachEvents();
    }
}



var assign = {
    username: [],
    i: 1,
    layout: function(usernames, i) {
        var _this = this;
        var i = _this.i;
        var username = _this.username;
        var row = $('<div class="col-10 assign-new-user' + i + '"></div>');
        var element = $('<select class="form-control mt-3"  id="assign-name' + i + '" name="assign-name[' + i + '][assign]"><option>Select User</option>');
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
        removeBtn.on('click', function() {
            $('.assign-new-user' + i).remove();
            _this.i--;
            username.pop();
        });
            $('#assign-new-user').append(row);
            $('#assign-new-user').append(row1);
    },


    validate: function() {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;


        var id = 'assign-name' + (i-1);

        console.log('i', i);
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
                    success: function(res) {
                        document.getElementById('module-error').innerHTML = " ";
                        var result = JSON.parse(res);
                        console.log(result)
                        var usernames = result['users'];
                        __this.layout(usernames, __this.i);
                        __this.i++;
                        username.push(user);
                    }
                }
                );
            }
            else{
                document.getElementById('module-error').innerHTML = "User name is repeated.";
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
        this.addBtn = this.ele.find('#assign-new-user');
        this.attachEvents();
    }
}




$(document).ready(function() {

    add_module.init("#append-new-module");
    assign.init("#assign-new-user");
    var addProject = document.getElementById('add-project');
	if (addProject != null) {
	addProject.onsubmit = function (e) {
		var task = document.getElementById('project-name').value;
		console.log(task);
		if (task =="" || task == " ") {
			document.getElementById('module-error').innerHTML = "Enter Project name";
			return false;
		}
		else
			return true;
	}
	}

})










