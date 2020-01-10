var addUser = document.getElementById('addUser');
if (addUser) {
    addUser.onsubmit = function (e) {
        var name = document.getElementById('newUser').value;
        var email = document.getElementById('user_email').value;
        var pswrd = document.getElementById('task_pass').value;
        if ((name == "" || name == " ") || (email == "" || email == " ") || (pswrd == "" || pswrd == " ")) {
            document.getElementById('user-error').innerHTML = "Please enter valid details";
            return false;
        } else {
            document.getElementById('user-error').innerHTML = " ";
            return true;
        }
    }
}