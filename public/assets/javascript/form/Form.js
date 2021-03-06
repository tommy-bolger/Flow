/**
* An object that handles the validation and AJAX submission of form fields.
* Copyright (c) 2012, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
function Form(dom_element) {
    var instance = this;

    this.form = dom_element;
    this.form.bind('submit', {
        instance: this
    }, this.submit);
    
    //Create a custom event that fields can bind to for cleanup after a submit happens
    this.form.on('submit_cleanup', function() {});
    
    //Add ajax/ into the action url
    this.action = this.form.attr('action').replace(window.location.host, window.location.host + '/ajax');

    if(this.action.indexOf('?') != -1) {
        this.action += '&';
    }
    else {
        this.action += '?';
    }
    
    this.action += 'method=submit';

    this.errors = $('.form_errors', this.form);
    this.warnings = $('.form_warnings', this.form);
    this.confirmations = $('.form_confirmations', this.form);

    this.fields = {};
    this.default_value_signature = '';
    
    $('.form_field_element', this.form).each(function() {
        var dom_element = $(this);

        if(typeof dom_element.attr('data-object') != 'undefined') {
            var element_name = dom_element.attr('name');
            
            var field = new window[$(this).data('object')](dom_element);
                
            instance.fields[element_name] = field;
            
            //Concatenate this field's value to the default value signature
            var default_value = field.getValue();
            
            if(typeof default_value != 'string' && typeof default_value != 'object') {
                default_value = default_value.join('');
            }
            
            instance.default_value_signature += default_value;
        }
    });
    
    this.last_value_signature = '';

    this.parent_container = $('#canvas');
    
    if(typeof this.parent_container.attr('id') == 'undefined') {
        this.parent_container = undefined;
    }
};

Form.prototype.submit = function(event) {
    event.preventDefault();

    var instance = event.data.instance;

    if(!instance.errors.is(':hidden')) {
        instance.errors.hide();
    }
    
    if(!instance.warnings.is(':hidden')) {
        instance.warnings.hide();
    }
    
    if(!instance.confirmations.is(':hidden')) {
        instance.confirmations.hide();
    }

    var values = {
        form_name: instance.form.attr('name')
    };
    
    var error_messages = new Array();
    instance.last_value_signature = '';

    for(field_name in instance.fields) {
        if(instance.fields.hasOwnProperty(field_name)) {
            var field = instance.fields[field_name];

            if(field.isValid()) {
                var value = field.getValue();
                
                if(typeof value != 'object') {
                    values[field_name] = value;
                
                    if(typeof value != 'string') {
                        value = value.join('');
                    }
                    
                    instance.last_value_signature += value;
                }
                else {
                    $.each(value, function(sub_field_name, sub_field_value) {
                        values[sub_field_name] = sub_field_value;
                    });
                }
            }
            else {
                error_messages.push(field.getLastErrorMessage());
            }
        }
    }

    if(error_messages.length > 0) {
        instance.showErrors('<p>' + error_messages.join('</p><p>') + '</p>');
        
        return false;
    }
    
    if(instance.last_value_signature == instance.default_value_signature) {
        instance.showWarnings('<p>No changes have been made so the form will not be submitted.</p>');
        
        return false;
    }

    Request.post(instance.action, values, {
        context: instance,
        method: 'submitSuccess'
    }, instance.parent_container);
    
    return false;
};

Form.prototype.submitSuccess = function(request, response) {
    if(response.errors.length > 0) {
        this.showErrors(response.errors);
    }

    if(response.warnings.length > 0) {
        this.showWarnings(response.warnings);
    }

    if(response.confirmations.length > 0) {
        this.showConfirmations(response.confirmations);
        
        this.default_value_signature = this.last_value_signature;
    }
    
    if(response.reset) {
        this.form.trigger('reset');
    }
    
    this.form.trigger('submit_cleanup');
};

Form.prototype.showErrors = function(error_messages) {
    this.errors.html(error_messages);
        
    this.errors.show();
};

Form.prototype.showWarnings = function(warning_messages) {
    this.warnings.html(warning_messages);
        
    this.warnings.show();
};

Form.prototype.showConfirmations = function(confirmation_messages) {
    this.confirmations.html(confirmation_messages);
        
    this.confirmations.show();
};

$(document).ready(function() {
    $('.form').each(function() {
        var form_element = $(this);
        
        if(!form_element.hasClass('no_js')) {
            var form = new Form(form_element);
        }
    });
});