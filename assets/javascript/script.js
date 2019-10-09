$(document).ready(function () {
    $('#forgot').click(function () {
        $('#form2').show();
        $('#loginForm').hide();
        $('#form2').css("background-color", "white");
    });

});


var Validation = function (e) {
    this.isValid = false;
    this.errorCount = 0;
    this.blurAttached = false;
    this.formElement = e;
};

Validation.prototype.correctCheck = function (e) {
    var current = this;
    var inputTags = this.formElement.getElementsByTagName('input');
    for (var i = 0; i < inputTags.length; i++) {
        var input = inputTags[i];
        this.isValid = this.event(input);
        if (!this.isBlurAttached) {
            input.addEventListener('blur', function (e) {
                current.isBlurAttached = true;
                return current.correctCheck(this.formElement);
            });
        }
    }
    return this;
};

Validation.prototype.event = function (ele) {
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
    if (ele.type == 'passwprd') {
        this.isValid = true;
        return this.isValid;
    }
    return this.isValid;
}

Validation.prototype.isValidateEmail = function (e) {
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
loginForm.onsubmit = function (e) {
    var validateForm = new Validation(e.currentTarget);
    var finalValue = validateForm.correctCheck();
    if (finalValue.isValid == true) {
        

        var id = document.getElementById('Username').value;
        localStorage.setItem('id',id);

        document.getElementById('Username-error').value = " ";
        return true;
    } else
        return false;
}

var forgotPsw = document.getElementById('forgotPassword');
forgotPsw.onsubmit = function (e) {
    var validateForm = new Validation(e.currentTarget);
    var finalValue = validateForm.correctCheck();
    if (finalValue.isValid == true) {
        var valid = validateOtp();
        if (valid == true)
        {
            $(document).ready(function () {
        
            $('#formPsw').show();
            $('#form2').hide();
            return false;
        
    });
            var formPsw = document.getElementById('reEnterPsw');
            formPsw.onsubmit = function (e)
            {
               var psw1 = document.getElementById('psw1').value;
    var psw2 = document.getElementById('psw2').value;
    if (psw1 == "" || psw1 == " ") {
        document.getElementById('cnfrmPsw').innerHTML = "Empty Password";
        return false;
    }
    if (psw1 === psw2) {
         alert('password changed successfully!!!');
         
        return true;
    }
    else
    {
     document.getElementById('cnfrmPsw').innerHTML = "Enter correct Password!!!";
            return false;/**/
        }

        }
            return false;
    } else
        return false;
}return false;
}

function validateOtp() {
    $(document).ready(function () {
        $('#getOTP').click(function () {
            $('.here').show();
        });
    });

   
    var otp = document.getElementById('otp1').value;
            if (otp === "" || otp === " ") {
                document.getElementById('rotate-text').innerHTML = " OTP has sent to your mail. ";
                document.getElementById('here').innerHTML = " Enter OTP ";
                return false;
            }
            else
            {  
                document.getElementById('rotate-text').innerHTML = " ";
                /*validate OTP*/
                return true;
            }
}
