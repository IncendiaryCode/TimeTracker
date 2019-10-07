$(document).ready(function()
{
	$('#change').click(function()
	{
		$('#changeImage').fadeToggle();
	});
});



var changePsw = document.getElementById('changePsw');
changePsw.onsubmit = function (e) {
    
    var oldPsw= document.getElementById('oldPsw').value;
    if (oldPsw == "" || oldPsw == " ") {
    	document.getElementById('alertMsg').innerHTML = "Enter your current passowrd";
    	return false;
    }

    /*else if () {}*//*validate old password */

    var psw1 = document.getElementById('psw1').value;
    var psw2 = document.getElementById('psw2').value;
    if (psw1 == "" || psw1 == " ") {
        document.getElementById('alertMsg').innerHTML = "Empty Password";
        return false;
    }
    else if (psw1 !== psw2) {
        document.getElementById('alertMsg').innerHTML = "Passowrd is not matching..";
         
        return false;
    }
    else
    {
     document.getElementById('alertMsg').innerHTML = "Password changed successfully!!!";
            return true;/**/
        }
        return false;
    }

var changeImage = document.getElementById('uploadImage');
changeImage.onsubmit = function (e) {
	var image = document.getElementById('image').value;
	if (image =="" || image == " ") {
		document.getElementById('imageErr').innerHTML = "Choose an image";
		return false;
	}
	else
		return true;
}


