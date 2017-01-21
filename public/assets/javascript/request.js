/**
* An object that handles AJAX requests.
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
function Request() {}

Request.in_progress = {
    POST: {},
    GET: {}
};

Request.submit = function(page_url, request_type, request_parameters, success_callback, loading_display_element) {
    if(Request.in_progress[request_type].hasOwnProperty(page_url)) {
        return false;
    }

    if(request_parameters == null) {
        request_parameters = new Object();
    }
    
    if(typeof loading_display_element != 'undefined') {
        loading_display_element.showLoading();
    }

    Request.in_progress[request_type][page_url] = 'running';

    $.ajax({
        type: request_type,
        cache: false,
        dataType: "json",
        url: page_url,
        data : request_parameters,
        success: function(response_data, text_status, jq_xhr) {
            if(typeof loading_display_element != 'undefined') {
                loading_display_element.hideLoading();
            }

            if(response_data.hasOwnProperty('redirect_location')) {
                window.location = response_data.redirect_location;
                
                return false;
            }
            
            if(response_data.hasOwnProperty('debug')) {
                alert(response_data.debug);
                
                return false;
            }
            
            switch(typeof success_callback) {
                case 'string':
                    window[success_callback](request_parameters, response_data);
                    break;
                case 'object':
                    success_callback.context[success_callback.method](request_parameters, response_data);
                    break;
            }

            delete Request.in_progress[request_type][page_url];
        },
        error: function (xhr, ajax_options, thrown_error) {                
            if(typeof loading_display_element != 'undefined') {
                loading_display_element.hideLoading();
            }
        
            if(ajax_options == 'error') {                
                alert(xhr.responseText);
            }
            
            delete Request.in_progress[request_type][page_url];                     
        }
    });    
}

Request.post = function(page_url, request_parameters, success_callback, loading_display_element) {
    Request.submit(page_url, 'POST', request_parameters, success_callback, loading_display_element);
}

Request.get = function(page_url, request_parameters, success_callback, loading_display_element) {
    Request.submit(page_url, 'GET', request_parameters, success_callback, loading_display_element);
}