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

$(document).ready(function() {

    add_module.init("#append-new-module");

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










