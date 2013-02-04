/**
* An object that handles the validation and value retrieval of a reCaptcha field.
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
function Captcha(dom_object) {
    Field.call(this, dom_object);
    
    this.dom_object.closest("form").bind('submit_cleanup', function() {
        Recaptcha.reload();
    });
};

Captcha.prototype = Object.create(Field.prototype);

Captcha.prototype.constructor = Captcha;

Captcha.prototype.isValid = function() {
    if(this.dom_object.hasClass('required')) {
        var response_value = Recaptcha.get_response();

        if(response_value == null || response_value != null && response_value.length == 0) {
            this.last_error_message = this.required_message;
            
            Recaptcha.reload();
            
            return false;
        }
    }
    
    return true;
};

Captcha.prototype.getValue = function() {
    return {
        recaptcha_challenge_field: Recaptcha.get_challenge(),
        recaptcha_response_field: Recaptcha.get_response()
    };
};