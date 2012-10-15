/**
* An object that handles the validation and value retrieval of an IP address field.
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
function IPAddress(dom_object) {
    this.max_length = dom_object.attr('maxlength');

    Textbox.call(this, dom_object);
}

IPAddress.prototype = Object.create(Textbox.prototype);

IPAddress.prototype.constructor = IPAddress;

IPAddress.prototype.isValid = function() {
    if(!Textbox.prototype.isValid.call(this)) {
        return false;
    }
    
    var field_value = this.dom_object.val();
    
    if(field_value.length > 0) {
        var field_value_split = field_value.split('.');
        
        if(field_value_split.length != 4) {
            this.last_error_message = this.label_text + ' is not a valid IP Address.';
            
            return false;
        }
        
        for(ip_segment_index in field_value_split) {
            if(field_value_split.hasOwnProperty(ip_segment_index)) {
                ip_segment = field_value_split[ip_segment_index];
            
                if(isNaN(ip_segment)) {
                    this.last_error_message = this.label_text + ' cannot contain letters.';
            
                    return false;
                }
                
                if(ip_segment < 0 || ip_segment > 254) {
                    this.last_error_message = this.label_text + ' is not a valid IP Address.';
            
                    return false;
                }
            }
        }
    }
    
    return true;
}