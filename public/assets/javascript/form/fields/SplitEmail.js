function SplitEmail(dom_object) {
    var dom_object_siblings = dom_object.siblings();

    this.user_name = dom_object_siblings.filter('.user_name');
    this.domain_name = dom_object_siblings.filter('.domain_name');
    this.domain_extension = dom_object_siblings.filter('.domain_extension');

    Email.call(this, dom_object);
}

SplitEmail.prototype = Object.create(Email.prototype);

SplitEmail.prototype.constructor = SplitEmail;

SplitEmail.prototype.isValid = function() {
    var user_name = $.trim(this.user_name.val());
    var domain_name = $.trim(this.domain_name.val());
    var domain_extension = $.trim(this.domain_extension.val());

    if(this.dom_object.hasClass('required') && user_name.length == 0 && domain_name.length == 0 && domain_extension.length == 0) {
        this.last_error_message = this.label_text + ' is required.';
        
        return false;
    }

    return this.emailIsValid(user_name + '@' + domain_name + '.' + domain_extension);
}

SplitEmail.prototype.getValue = function() {
    var user_name = this.user_name.val();
    var domain_name = this.domain_name.val();
    var domain_extension = this.domain_extension.val();

    if(user_name.length > 0 && domain_name.length > 0 && domain_extension.length > 0) {
        return this.user_name.val() + '@' + this.domain_name.val() + '.' + this.domain_extension.val();
    }
    
    return '';
}