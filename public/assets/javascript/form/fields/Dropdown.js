function Dropdown(dom_object) {    
    Select.call(this, dom_object);
}

Dropdown.prototype = Object.create(Select.prototype);

Dropdown.prototype.constructor = Dropdown;

Dropdown.prototype.valueInOptions = function() {
    var field_value = this.getValue();
    
    if(!this.options.hasOwnProperty(field_value)) {
        this.last_error_message = this.wrong_option_error_message;
        
        return false;
    }

    return true;
}