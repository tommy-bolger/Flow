function Email(dom_object) {
    Field.call(this, dom_object);
}

Email.prototype = Object.create(Field.prototype);

Email.prototype.constructor = Email;

Email.prototype.isValid = function() {
    if(!Field.prototype.isValid.call(this)) {
        return false;
    }

    return this.emailIsValid(this.dom_object.val());
}

Email.prototype.emailIsValid = function(email_address) {
    //The following regex expression was found here: http://stackoverflow.com/a/9204568
    if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_address) == false) {
        this.last_error_message = this.label_text + ' is not a valid email address.';
        
        return false;
    }
    
    return true;
}