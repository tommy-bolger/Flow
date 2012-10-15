/**
* An object that handles the validation and value retrieval of an email field split into its sections.
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
function SplitEmail(dom_object) {
    var dom_object_siblings = dom_object.siblings();

    this.user_name = dom_object_siblings.filter('.user_name');
    this.domain_name = dom_object_siblings.filter('.domain_name');
    this.domain_extension = dom_object_siblings.filter('.domain_extension');

    Email.call(this, dom_object);
}

SplitEmail.prototype = Object.create(Email.prototype);

SplitEmail.prototype.constructor = SplitEmail;

SplitEmail.prototype.isValid = function() {
    var user_name = $.trim(this.user_name.val());
    var domain_name = $.trim(this.domain_name.val());
    var domain_extension = $.trim(this.domain_extension.val());

    if(this.dom_object.hasClass('required') && user_name.length == 0 && domain_name.length == 0 && domain_extension.length == 0) {
        this.last_error_message = this.label_text + ' is required.';
        
        return false;
    }

    return this.emailIsValid(user_name + '@' + domain_name + '.' + domain_extension);
}

SplitEmail.prototype.getValue = function() {
    var user_name = this.user_name.val();
    var domain_name = this.domain_name.val();
    var domain_extension = this.domain_extension.val();

    if(user_name.length > 0 && domain_name.length > 0 && domain_extension.length > 0) {
        return this.user_name.val() + '@' + this.domain_name.val() + '.' + this.domain_extension.val();
    }
    
    return '';
}