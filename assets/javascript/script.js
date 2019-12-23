var user_email;
$(document).ready(function() {
    $('#forgot').click(function() {
        $('#form2').show();
        $('#loginForm').hide();
        $('#form2').css("background-color", "white");
    });

});


var Validation = function(e) {
    this.isValid = false;
    this.errorCount = 0;
    this.blurAttached = false;
    this.formElement = e;
};

Validation.prototype.correctCheck = function(e) {
    var current = this;
    var inputTags = this.formElement.getElementsByTagName('input');
    for (var i = 0; i < inputTags.length; i++) {
        var input = inputTags[i];
        this.isValid = this.event(input);
        if (!this.isBlurAttached) {
            input.addEventListener('blur', function(e) {
                current.isBlurAttached = true;
                return current.correctCheck(this.formElement);
            });
        }
    }
    return this;
};

Validation.prototype.event = function(ele) {
    if (ele.classList.contains('has-empty-validation')) {
        if (ele.value == "" || ele.value == " ") {
            document.getElementById(ele.id + "-error").innerHTML = ele.name + " is required.";
            this.errorCount++;
            return false;
        } else {
            document.getElementById(ele.id + "-error").innerHTML = " ";
        }
    }
    if (ele.type == 'email' && ele.classList.contains('has-email-validation')) {

        return this.isValidateEmail(ele);
    }
    if (ele.type == 'password') {
        this.isValid = true;
        return this.isValid;
    }
    return this.isValid;
}

Validation.prototype.isValidateEmail = function(e) {
    if (e.type == 'email') {
        var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!emailRegEx.test(e.value)) {
            document.getElementById(e.id + "-error").innerHTML = "Email format is not correct.";
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

var loginForm = document.getElementById('loginForm');
if(loginForm)
{
loginForm.onsubmit = function(e) {
    var validateForm = new Validation(e.currentTarget);
    var finalValue = validateForm.correctCheck();
    if (finalValue.isValid == true) {


        var id = document.getElementById('Username').value;
        localStorage.setItem('id', id);

        document.getElementById('Username-error').value = " ";
        return true;
    } else
        return false;
}
}
var forgotPsw = document.getElementById('forgotPassword');
if(forgotPsw)
{
forgotPsw.onsubmit = function(e) {
    user_email = document.getElementById('Uname').value;
    console.log(user_email);
    var validateForm = new Validation(e.currentTarget);
    var finalValue = validateForm.correctCheck();
    if (finalValue.isValid == true) {

        var valid = validateOtp();
        var formPsw = document.getElementById('reEnterPsw');
        formPsw.onsubmit = function(e) {
            document.getElementById('user-email').value = user_email;

            var psw1 = document.getElementById('psw1').value;
            var psw2 = document.getElementById('psw2').value;
            if (psw1 == "" || psw1 == " ") {
                document.getElementById('cnfrmPsw').innerHTML = "Empty Password";
                return false;
            } else if (email == "" || email == " ") {
                document.getElementById('Username-errorr').innerHTML = "Empty Email";
                return false;
            }
            if (psw1 === psw2) {

                $.ajax({
                    type: "POST",
                    url: '../php/change_pwd.php',
                    data: { Username: email, psw11: psw1, psw22: psw2 },
                    success: function(data) {
                    }
                });
                alert('password changed successfully!!!');

                return true;
            } else {
                document.getElementById('cnfrmPsw').innerHTML = "Enter correct Password!!!";
                return false; /**/
            }

        }
        return false;

    }
    return false;
}
}
function validateOtp() {
    $(document).ready(function() {
        $('#getOTP').click(function() {
            $('.email-error').show();
        });
    });


    var otpp = document.getElementById('otp1').value;
    if (otpp === "" || otpp === " ") {
        document.getElementById('email-error').innerHTML = " Enter OTP ";
        return false;
    } else {
        document.getElementById('rotate-text').innerHTML = " ";
        /*validate OTP*/
        $.ajax({
            type: "POST",
            url: 'php/check_otp.php',
            data: { otp: otpp },
            success: function(data) {
                if (data == null || data == "") {
                    $(document).ready(function() {
                        $('#formPsw').show();
                        $('#form2').hide();
                    });
                    return true;
                } else {
                    document.getElementById("email-error").innerHTML = "Wrong OTP.";
                    return false;
                }
            }
        });
    }
}

function validateEmail(email)
{
var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!emailRegEx.test(email)) {
            document.getElementById("email-error").innerHTML = "Email format is not correct.";
            return false;
        } else {
            document.getElementById("email-error").innerHTML = " ";
            return true;
        }
}
function sendOTP() {
    var email = document.getElementById('Uname').value;
    if (email == "" || email == " ") {
        document.getElementById('Uname-error').innerHTML = "Enter email.";
    }
    else {
        var validate_mail = validateEmail(email);
        if(validate_mail)
        {
        $.ajax({
            type: "POST",
            url: '../login/send_otp',
            data: { email: email },
            success: function(data) {
                var result = JSON.parse(data);
                if(result['status'] == true){
                    $('#email-error').addClass('text-success');
                    $('#email-error').removeClass('text-danger');
                    document.getElementById('email-error').innerHTML = result['msg'];
                $(document).ready(function() {
                    $('#form2').show();
                    });
                return true;
            }else{
                document.getElementById('email-error').innerHTML = result['msg'];
                return false;
            }
        }
        });
        }
    }
}