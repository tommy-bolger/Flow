function RadioGroup(dom_object) {
    Dropdown.call(this, dom_object);
}

RadioGroup.prototype = Object.create(Dropdown.prototype);

RadioGroup.prototype.constructor = RadioGroup;

RadioGroup.prototype.loadOptions = function() {
    var instance = this;

    $('input', this.dom_object.siblings()).each(function() {
        var option_element = $(this);
    
        instance.options[option_element.attr('value')] = option_element;
    });
}

RadioGroup.prototype.getValue = function() {
    var field_value = '';

    $.each(this.options, function(value, toggle_field) {    
        if(toggle_field.is(':checked')) {
            field_value = toggle_field.val();
        }
    });
    
    return field_value;
}