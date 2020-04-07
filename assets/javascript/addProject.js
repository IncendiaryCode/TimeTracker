var add_module = {
    username: [],
    i: 1,
    last_row: 0,
    layout: function (i) {
        var _this = this;
        var last_row = _this.last_row;
        var username = _this.username;
        var i = _this.i;
        var main_row = $('<div class = "row"></div>')
        var element = $('<div class="col-10 assign-module' + i + '"><input type="text" class="form-control mt-3"  id="new-module' + i + '" name="new-module[' + i + '][module]" placeholder="Enter module name"></div>');

        main_row.append(element);
        var row1 = $('<div class="col-2 pt-3 assign-module' + i + '"></div>');
        var addBtn = $('<a href="javascript:void(0);" id="add-module-' + i + '">');
        var icon = $('<i class="fas fa-plus icon-plus text-white"></i></a>');
        addBtn.append(icon);
        row1.append(addBtn);
        row1.appendTo(main_row);

        $('#add-new-module').removeClass('fa-plus');
        if (_this.last_row != 0) {
            _this.last_row.removeClass('fas fa-plus');
            _this.last_row.addClass('fas fa-minus icon-plus text-white');
        }
        $('#adding-module').removeClass('fas fa-plus');
        $('#adding-module').addClass('fas fa-minus icon-plus text-white');
        $(".fa-minus").click(function () {
            this.parentNode.parentNode.parentNode.remove();
        });
        _this.last_row = icon;

        addBtn.on('click', function () {
            _this.validate();
        });
        $('#append-new-module').append(main_row);
        /*$('#append-new-module').append(row1);*/
    },


    validate: function () {
        var __this = this;
        var username = __this.username;
        var i = __this.i;
        var sameName = 0;
        var id = 'new-module' + (i - 1);
        var module_name = document.getElementById(id).value;
        if ((module_name == "Select User") || (module_name == "") || (module_name == " ")) {
            document.getElementById('module-err').innerHTML = "Please enter module name..";
        } else {
            document.getElementById('module-err').innerHTML = " ";
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
                document.getElementById('module-err').innerHTML = "Module name is repeated.";
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

    // Start upload preview image
    var $uploadCrop, tempFilename, rawImg, imageId, cropped_points;
    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.upload-demo').addClass('ready');
                $('#cropImagePop').modal('show');
                rawImg = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }
    $uploadCrop = $('#upload-demo').croppie({
        viewport: {
            width: 200,
            height: 200,
            type: 'circle'
        },
        boundary: {
            width: 300,
            height: 300
        },
        enforceBoundary: false,
        enableExif: true
    });
    $('#cropImagePop').on('shown.bs.modal', function() {
        $uploadCrop
            .croppie('bind', {
                url: rawImg
            })
            .then(function() {});
    });

    $('.item-img').on('change', function() {

        imageId = $(this).data('id'); 
        tempFilename = $(this).val();
        $('#cancelCropBtn').data('id', imageId);
        readFile(this);
    });

    $('#cropImagePop').on('update.croppie', function(ev, cropData) {
        cropped_points = cropData['points'];
    });

    $('#cropImageBtn').on('click', function(ev) {
        $uploadCrop
            .croppie('result', {
                type: 'base64',
                format: 'jpeg',
                size: { width: 200, height: 200 }
            })
            .then(function(resp) {
                $('#item-img-output').attr('src', resp);
                $('#cropImagePop').modal('hide');
            });
    });

    

    if (document.getElementById('old-project-input')) {
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
                    if (usernames[j]["project_name"] != null) {
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
            document.getElementById('cropped-points').value = cropped_points;
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










