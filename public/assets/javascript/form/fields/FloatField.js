/**
* An object that handles the validation and value retrieval of a float (decimal) field.
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
function FloatField(dom_object) {
    this.left_precision = dom_object.attr('data-left-precision');
    this.right_precision = dom_object.attr('data-right-precision');

    Textbox.call(this, dom_object);
};

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
};