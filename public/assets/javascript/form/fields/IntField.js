function IntField(dom_object) {
    Textbox.call(this, dom_object);
}

IntField.prototype = Object.create(Textbox.prototype);

IntField.prototype.constructor = IntField;

IntField.prototype.isValid = function() {
    if(!Field.prototype.isValid.call(this)) {
        return false;
    }
    
    var field_value = this.dom_object.val();
    
    if(field_value.length > 0) {
        if(isNaN(field_value)) {
            this.last_error_message = this.label_text + ' is not a valid number.';
            
            return false;
        }
        
        if(this.max_length.length > 0) {
            if(!this.maxLengthIsValid()) {
                return false;
            }
            else {
                if(field_value.length > this.max_length) {
                    this.last_error_message = this.label_text + ' cannot be more than ' + this.max_length + ' digits long.';
            
                    return false;
                }
            }
        }
    }
    
    return true;
}