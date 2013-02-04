/**
* An object that handles the validation and value retrieval of a field.
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
function Field(dom_object) {
    this.dom_object = dom_object; 

    this.label = $('label[for="' + this.dom_object.attr('name') + '"]');
    
    this.label_text = this.label.text().replace('\*', '');
    
    this.required_message = this.label_text + ' is a required field.';

    this.last_error_message = '';
};

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
};

Field.prototype.getLastErrorMessage = function() {
    return this.last_error_message;
};

Field.prototype.getValue = function() {
    return this.dom_object.val();
};

Field.prototype.getLabel = function() {
    return this.label.text();
};