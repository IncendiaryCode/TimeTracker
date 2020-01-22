var addUser = document.getElementById('addUser');
if (addUser) {
    addUser.onsubmit = function (e) {
        var name = document.getElementById('newUser').value;
        var email = document.getElementById('user_email').value;
        var pswrd = document.getElementById('task_pass').value;
        var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if ((name == "" || name == " ") || (email == "" || email == " ") || (pswrd == "" || pswrd == " ")) {
            document.getElementById('user-error').innerHTML = "Please enter valid details";
            return false;
        }
        if (!emailRegEx.test(email)) {
            document.getElementById("user-error").innerHTML ="Email format is not correct.";
                $('#user_email').focus();
            return false;
        } 
        else {
                document.getElementById('user-error').innerHTML = " ";
                return true;
            }
    }
}
