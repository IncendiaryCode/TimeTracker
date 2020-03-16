$(document).ready(function() {
	var changePsw = document.getElementById('changePsw');
	if (changePsw) {
		changePsw.onsubmit = function(e) {
			var oldPsw = document.getElementById('old-pass').value;
			if (oldPsw == '' || oldPsw == ' ') {
				document.getElementById('psw-error').innerHTML = 'Enter your current password';
				return false;
			}
			var psw1 = document.getElementById('new-pass').value;
			var psw2 = document.getElementById('confirm-pass').value;
			if (psw1 == '' || psw1 == ' ') {
				document.getElementById('psw-error').innerHTML = 'Empty Password';
				return false;
			} else if (psw1 !== psw2) {
				document.getElementById('psw-error').innerHTML = 'Passowrd is not matching..';
				return false;
			} else {
				document.getElementById('psw-error').innerHTML = 'Password changed successfully!!!';
				return true;
			}
			return false;
		};
	}

	var changeImage = document.getElementById('uploadImage');
	if (changeImage) {
		changeImage.onsubmit = function(e) {
			var image = document.getElementById('profile-image').value;
			if (image == '' || image == ' ') {
				document.getElementById('imageerror').innerHTML = 'Choose an image';
				return false;
			} else return true;
		};
	}
});
