var user_email;
$(document).ready(function () {
	$("#forgot").click(function () {
		$("#form2").show();
		$("#loginForm").hide();
		$("#form2").css("background-color", "white");
	});
	var forgotPsw = document.getElementById("forgotPassword");
	if (forgotPsw) {
		forgotPsw.onsubmit = function (e) {
			user_email = document.getElementById("Uname").value;
			var validateForm = new Validation(e.currentTarget);
			var finalValue = validateForm.correctCheck();
		return true;
		};
	}
	if(typeof email != "undefined" && (email != '' ))
	{
		document.getElementById("Uname").value = email;
		$("#enter-otp").show();
		$("#enter-email").hide();
	}
	var formPsw = document.getElementById("reEnterPsw");
	if(formPsw)
	{
		formPsw.onsubmit = function (e) {
			var psw1 = document.getElementById("psw1").value;
			var psw2 = document.getElementById("psw2").value;
			if (psw1 == "" || psw1 == " ") {
				document.getElementById("cnfrmPsw").innerHTML = "Empty Password";
				return false;
			}
			if (psw1 === psw2) {
				$.ajax({
					type: "POST",
					url: "../login/change_pass",
					data: { mail: document.getElementById("user-email").value, psw11: psw1, psw22: psw2 },
					success: function (data) { }
				});
				return true;
			} else {
				document.getElementById("cnfrmPsw").innerHTML =
					"Enter correct Password!!!";
				return false;
			}
		};
	}

	$('#getOTP1').click(function()
	{
		$('.resend-otp-spinner').css("display","block");
	})
});

var Validation = function (e) {
	this.isValid = false;
	this.errorCount = 0;
	this.blurAttached = false;
	this.formElement = e;
};

Validation.prototype.correctCheck = function (e) {
	var current = this;
	var inputTags = this.formElement.getElementsByTagName("input");
	for (var i = 0; i < inputTags.length; i++) {
		var input = inputTags[i];
		this.isValid = this.event(input);
		if (!this.isBlurAttached) {
			input.addEventListener("blur", function (e) {
				current.isBlurAttached = true;
				return current.correctCheck(this.formElement);
			});
		}
	}
	return this;
};

Validation.prototype.event = function (ele) {
	if (ele.classList.contains("has-empty-validation")) {
		if (ele.value == "" || ele.value == " ") {
			document.getElementById(ele.id + "-error").innerHTML =
				ele.name + " is required.";
			this.errorCount++;
			return false;
		} else {
			document.getElementById(ele.id + "-error").innerHTML = " ";
		}
	}
	if (ele.type == "email" && ele.classList.contains("has-email-validation")) {
		return this.isValidateEmail(ele);
	}
	if (ele.type == "password") {
		this.isValid = true;
		return this.isValid;
	}
	return this.isValid;
};

Validation.prototype.isValidateEmail = function (e) {
	if (e.type == "email") {
		var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (!emailRegEx.test(e.value)) {
			document.getElementById(e.id + "-error").innerHTML =
				"Email format is not correct.";
			this.errorCount++;
			e.focus();
			this.isValid = false;
			return this.isValid;
		} else {
			document.getElementById(e.id + "-error").innerHTML = " ";
			this.isValid = true;
			return this.isValid;
		}
		return this.isValid;
	}
};

var loginForm = document.getElementById("loginForm");
if (loginForm) {
	loginForm.onsubmit = function (e) {
		var validateForm = new Validation(e.currentTarget);
		var finalValue = validateForm.correctCheck();
		if (finalValue.isValid == true) {
			
			if(document.getElementById("Username").value != undefined)
			{
			var id = document.getElementById("Username").value;
			}
			localStorage.setItem("id", id);
			document.getElementById("Username-error").value = " ";
			return true;
		} else return false;
	};
}

function validateOtp() {
	document.getElementById("email-error").innerHTML = " ";
	$("#getOTP").click(function () {
		$(".alert-user").show();
	});

	var otpp = document.getElementById("otp1").value;
	if (otpp === "" || otpp === " ") {
		document.getElementById("email-error").innerHTML = " Enter OTP ";
		return false;
	} else {
		/*validate OTP*/
		$.ajax({
			type: "POST",
			url: timeTrackerBaseURL + "login/check_otp",
			data: { "otp": otpp, "email": document.getElementById('Uname').value },
			success: function (data) {
				if(!(email == "" || email == " "))
				{
					$("#enter-email").hide();
					$("#enter-otp").show();
					document.getElementById("email-error").innerHTML = "Wrong OTP.";
					return false;
				}
				 else {
					return true;
				}
			}
		});
	}
}

function sendOTP() {
	
	var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	var email1 = document.getElementById("Uname").value;
	document.getElementById("email-error").innerHTML = " ";

	if (!emailRegEx.test(email1)) {
            document.getElementById("email-error").innerHTML ="Email format is not correct.";
                $('#Uname').focus();
            return false;
        } 
	if (email1 == "" || email1 == " ") {
		document.getElementById("email-error").innerHTML = "Please enter valid email.";
	} else {
		$('.send-otp-spinner').css("display", "block");
		$.ajax({
			type: "POST",
			url: timeTrackerBaseURL + "login/send_otp",
			data: { email: email1 },
			success: function (data) {
				var data = JSON.parse(data);
				if(data["status"] == false)
				{
					document.getElementById("email-error").innerHTML = "Entered email address is not available";
					$('.send-otp-spinner').css("display", "none");
					return false;
				}
				document.getElementById("email-error").innerHTML = " ";
				$("#enter-otp").show();
				$("#enter-email").hide();
				$('#fill-otp').click(function () {
					var validate = validateOtp();
					if (validate) {
						forgotPsw.onsubmit = function (e) {
							return true;
						}

					}
					else
					{
						$("#enter-otp").show();
						$("#enter-email").hide();
					}
				});
			}
		});
	}
}
function resendOTP() {
	var email = document.getElementById("Uname").value;
	if (email == "" || email == " ") {
		document.getElementById("email-error").innerHTML = "Enter email.";
	} else {
		$('.resend-otp-spinner').css("display", "block");
		$.ajax({
			type: "POST",
			url: timeTrackerBaseURL + "login/send_otp",
			data: { email: email },
			success: function (data) {
				document.getElementById("email-error").innerHTML = " ";
				document.getElementById("resent-otp").innerHTML = "Otp sent successfully";
				$('.resend-otp-spinner').css("display", "none");
				$('#fill-otp').click(function () {
					var validate = validateOtp();
					if (validate) {
						forgotPsw.onsubmit = function (e) {
							return true;
						}

					}
				});
			}
		});
	}
}
