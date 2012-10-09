function Select(dom_object) {
    Field.call(this, dom_object);
    
    this.wrong_option_error_message = this.label_text + ' has a selected option that is not one of its available options.';
    
    this.options = {};     

    this.loadOptions();
}

Select.prototype = Object.create(Field.prototype);

Select.prototype.constructor = Select;

Select.prototype.loadOptions = function() {
    var instance = this;

    $('option', this.dom_object).each(function() {
        var option_element = $(this);
    
        instance.options[option_element.attr('value')] = option_element;
    });
}

Select.prototype.isValid = function() {
    if(!Field.prototype.isValid.call(this)) {
        return false;
    }
    
    return this.valueInOptions();
}

Select.prototype.valueInOptions = function() {}