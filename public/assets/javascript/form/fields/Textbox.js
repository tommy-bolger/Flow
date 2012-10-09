function Textbox(dom_object) {
    this.max_length = dom_object.attr('maxlength');

    Field.call(this, dom_object);
}

Textbox.prototype = Object.create(Field.prototype);

Textbox.prototype.constructor = Textbox;

Textbox.prototype.isValid = function() {
    if(!Field.prototype.isValid.call(this)) {
        return false;
    }
    
    var field_value = this.dom_object.val();

    if(this.max_length > 0) {
        if(!this.maxLengthIsValid()) {
            return false;
        }
        else {
            if(field_value.length > this.max_length) {
                this.last_error_message = this.label_text + ' cannot be longer than ' + this.max_length + ' characters.';
        
                return false;
            }
        }
    }
    
    return true;
}

Textbox.prototype.maxLengthIsValid = function() {
    if(isNaN(this.max_length)) {
        this.last_error_message = this.label_text + ' has a non integer value set for max length attribute.';
    
        return false;
    }
    
    return true;
}