function Request() {}

Request.in_progress = {
    POST: {},
    GET: {}
};

Request.submit = function(page_url, request_type, request_parameters, success_callback, loading_display_element) {
    if(Request.in_progress[request_type].hasOwnProperty(page_url)) {
        return false;
    }

    if(request_parameters == undefined || request_parameters == null) {
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