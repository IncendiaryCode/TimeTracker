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
            document.getElementById("error").innerHTML = ele.name + " is required.";
            this.errorCount++;
            return false;
        } else {
            document.getElementById("error").innerHTML = " ";
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
            document.getElementById("error").innerHTML = "Email format is not correct.";
            this.errorCount++;
            e.focus();
            this.isValid = false;
            return this.isValid;
        } else {
            document.getElementById("error").innerHTML = " ";
            this.isValid = true;
            return this.isValid;
        }
        return this.isValid;
    }
};

var addUser = document.getElementById('addUser');
addUser.onsubmit = function(e) {
    var validateNewuser = new Validation(e.currentTarget);
    var finalValue = validateNewuser.correctCheck();
    if (finalValue.errorCount == 0) {
        document.getElementById('error').value = " ";
        return true;
    } else
        return false;
}