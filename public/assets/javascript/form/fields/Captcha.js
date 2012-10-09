function Captcha(dom_object) {
    Field.call(this, dom_object);
    
    this.dom_object.closest("form").bind('submit_cleanup', function() {
        Recaptcha.reload();
    });
}

Captcha.prototype = Object.create(Field.prototype);

Captcha.prototype.constructor = Captcha;

Captcha.prototype.isValid = function() {
    if(this.dom_object.hasClass('required')) {
        var response_value = Recaptcha.get_response();

        if(response_value == null || response_value != null && response_value.length == 0) {
            this.last_error_message = this.required_message;
            
            Recaptcha.reload();
            
            return false;
        }
    }
    
    return true;
}

Captcha.prototype.getValue = function() {
    return {
        recaptcha_challenge_field: Recaptcha.get_challenge(),
        recaptcha_response_field: Recaptcha.get_response()
    };
}