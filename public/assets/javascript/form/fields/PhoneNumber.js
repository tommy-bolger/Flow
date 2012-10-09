function PhoneNumber(dom_object) {
    var dom_object_siblings = dom_object.siblings();

    this.area_code = dom_object_siblings.filter('.area_code');
    this.exchange = dom_object_siblings.filter('.exchange');
    this.line_number = dom_object_siblings.filter('.line_number');

    Field.call(this, dom_object);
}

PhoneNumber.prototype = Object.create(Field.prototype);

PhoneNumber.prototype.constructor = PhoneNumber;

PhoneNumber.prototype.isValid = function() {
    var area_code = $.trim(this.area_code.val());
    var exchange = $.trim(this.exchange.val());
    var line_number = $.trim(this.line_number.val());
    
    if(this.dom_object.hasClass('required') && area_code.length == 0 && exchange.length == 0 && line_number.length == 0) {
        this.last_error_message = this.label_text + ' is required.';
        
        return false;
    }
    
    var full_number = area_code + exchange + line_number;
    
    if(full_number.length != 10 || isNaN(full_number)) {
        this.last_error_message = this.label_text + ' requires a valid 10-digit phone number.';
    
        return false;
    }
    
    return true;
}

PhoneNumber.prototype.getValue = function() {
    return this.area_code.val() + this.exchange.val() + this.line_number.val();
}