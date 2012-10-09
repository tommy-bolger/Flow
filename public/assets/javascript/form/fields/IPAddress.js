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