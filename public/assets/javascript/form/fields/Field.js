function Field(dom_object) {
    this.dom_object = dom_object; 

    this.label = $('label[for="' + this.dom_object.attr('name') + '"]');
    
    this.label_text = this.label.text().replace('\*', '');
    
    this.required_message = this.label_text + ' is a required field.';

    this.last_error_message = '';
}

Field.prototype.isValid = function() {
    if(this.dom_object.hasClass('required')) {
        var field_value = this.getValue();
    
        if(typeof field_value == 'string') {
            field_value = $.trim(field_value);    
        }

        if(field_value == null || field_value != null && field_value.length == 0) {
            this.last_error_message = this.required_message;
            
            return false;
        }
    }
    
    return true;
}

Field.prototype.getLastErrorMessage = function() {
    return this.last_error_message;
}

Field.prototype.getValue = function() {
    return this.dom_object.val();
}

Field.prototype.getLabel = function() {
    return this.label.text();
}