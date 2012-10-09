function CheckboxGroup(dom_object) {
    Listbox.call(this, dom_object);
}

CheckboxGroup.prototype = Object.create(Listbox.prototype);

CheckboxGroup.prototype.constructor = CheckboxGroup;

CheckboxGroup.prototype.loadOptions = function() {
    var instance = this;

    $('input', this.dom_object.siblings()).each(function() {
        var option_element = $(this);
    
        instance.options[option_element.attr('value')] = option_element;
    });
}

CheckboxGroup.prototype.getValue = function() {
    var values = new Array();

    $.each(this.options, function(value, toggle_field) {
        if(toggle_field.is(':checked')) {
            values.push(toggle_field.val());
        }
    });

    return values;
}