Form.prototype.showErrors = function(error_messages) {
    this.errors.html(error_messages);
        
    this.errors.show();
    
    Recaptcha.reload();
};