function FloatField(dom_object) {
    this.left_precision = dom_object.attr('data-left-precision');
    this.right_precision = dom_object.attr('data-right-precision');

    Textbox.call(this, dom_object);
}

FloatField.prototype = Object.create(IntField.prototype);

FloatField.prototype.constructor = FloatField;

FloatField.prototype.isValid = function() {
    if(!IntField.prototype.isValid.call(this)) {
        return false;
    }
    
    var field_value_split = this.dom_object.val().split('.');

    if(typeof this.left_precision != 'undefined' && field_value_split.hasOwnProperty(0) && field_value_split[0].length > this.left_precision) {
        this.last_error_message = this.label_text + " can only have " + this.left_precision + " digit(s) maximum before the decimal place.";
        
        return false;
    }
    
    if(typeof this.right_precision != 'undefined' && field_value_split.hasOwnProperty(1) && field_value_split[1].length > this.right_precision) {
        this.last_error_message = this.label_text + " can only have " + this.right_precision + " digit(s) maximum after the decimal place.";
        
        return false;
    }
    
    return true;
}