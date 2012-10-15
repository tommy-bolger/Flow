/**
* An object that handles the validation and value retrieval of a phone number field.
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
function PhoneNumber(dom_object) {
    var dom_object_siblings = dom_object.siblings();

    this.area_code = dom_object_siblings.filter('.area_code');
    this.exchange = dom_object_siblings.filter('.exchange');
    this.line_number = dom_object_siblings.filter('.line_number');

    Field.call(this, dom_object);
}

PhoneNumber.prototype = Object.create(Field.prototype);

PhoneNumber.prototype.constructor = PhoneNumber;

PhoneNumber.prototype.isValid = function() {
    var area_code = $.trim(this.area_code.val());
    var exchange = $.trim(this.exchange.val());
    var line_number = $.trim(this.line_number.val());
    
    if(this.dom_object.hasClass('required') && area_code.length == 0 && exchange.length == 0 && line_number.length == 0) {
        this.last_error_message = this.label_text + ' is required.';
        
        return false;
    }
    
    var full_number = area_code + exchange + line_number;
    
    if(full_number.length != 10 || isNaN(full_number)) {
        this.last_error_message = this.label_text + ' requires a valid 10-digit phone number.';
    
        return false;
    }
    
    return true;
}

PhoneNumber.prototype.getValue = function() {
    return this.area_code.val() + this.exchange.val() + this.line_number.val();
}