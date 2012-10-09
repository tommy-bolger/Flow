function Listbox(dom_object) {
    Select.call(this, dom_object);
}

Listbox.prototype = Object.create(Select.prototype);

Listbox.prototype.constructor = Listbox;

Listbox.prototype.valueInOptions = function() {
    var field_values = this.getValue();

    if(field_values.length > 0) {
        for(value_index in field_values) {
            if(field_values.hasOwnProperty(value_index)) {
                if(!this.options.hasOwnProperty(field_values[value_index])) {
                    this.last_error_message = this.wrong_option_error_message;
        
                    return false;
                }
            }
        }
    }
    
    return true;
}