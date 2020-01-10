var validate = document.getElementById('myProfile');
if (validate) {
    validate.onsubmit = function () {
        var oldPsw = document.getElementById('oldPsw').value;
        if (oldPsw == "" || oldPsw == " ") {
            document.getElementById('alertMsg').innerHTML = "Enter your current passowrd";
            return false;
        }

        /*else if () {}*/
        /*validate old password */

        var psw1 = document.getElementById('psw1').value;
        var psw2 = document.getElementById('psw2').value;
        if (psw1 == "" || psw1 == " ") {
            document.getElementById('alertMsg').innerHTML = "Enter new Password";
            return false;
        } else if (psw1 !== psw2) {
            document.getElementById('alertMsg').innerHTML = "Password is not matching..";

            return false;
        } else {
            $('#alertMsg').attr('class', "text-success");
            document.getElementById('alertMsg').innerHTML = "Password changed successfully!!!";
            return true; /**/
        }
        return false;
    }
}
