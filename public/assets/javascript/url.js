/**
* A JS object that allows easy parsing and access to the current browser url.
* Copyright (c) 2017, Tommy Bolger
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
function Url() {
    var instance = this;
    
    this.url = window.location.href;
    this.base_url;
    this.variables = {};
    
    var url_split = this.url.split('?');
    
    if(url_split.length == 2) {
        var variables_string = url_split[1];
        
        var variables_string_split = variables_string.split('&');
        
        var variables_length = variables_string_split.length;
        
        for(var index = 0; index < variables_length; index++) {
            var variable_string = variables_string_split[index];
            
            var variable_string_split = variable_string.split('=');
            
            var variable_name = variable_string_split[0];
            
            var variable_value = null;
            
            if(variable_string_split.length == 2) {
                variable_value = variable_string_split[1];
            }
            
            this.variables[variable_name] = variable_value;
        }
    }
    
    this.base_url = url_split[0];
};

Url.generateUrl = function(base_url, request_variables) {
    var url = base_url;
    
    var query_string_segments = [];
    
    for(var variable_name in request_variables) {
        if (request_variables.hasOwnProperty(variable_name)) {
            query_string_segments.push(variable_name + '=' + encodeURI(request_variables[variable_name]));
        }
    }
    
    if(query_string_segments.length > 0) {
        url += '?' + query_string_segments.join('&');
    }
    
    return url;
};

Url.prototype.getUrl = function() {
    return this.url;  
};

Url.prototype.getBaseUrl = function() {
    return this.base_url;  
};

Url.prototype.getValue = function(variable_name) {
   var variable_value = null;
   
   if(this.variables[variable_name] != null) { 
       variable_value = this.variables[variable_name];
   }
   
   return variable_value;
};