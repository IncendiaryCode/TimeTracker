var addUser = document.getElementById('addUser');
addUser.onsubmit = function(e) {
    var name = document.getElementById('newUser').value;
    if (name == "" || name == " ") {
        document.getElementById('userError').innerHTML = "Empty username";
        return false;
    } else {
        document.getElementById('userError').innerHTML = " ";
        return true;

        return false;
    }
}