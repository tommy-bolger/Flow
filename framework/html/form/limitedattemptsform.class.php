<?php
/**
* Allows the rendering of a form with a limited number of submit attempts..
* Copyright (c) 2011, Tommy Bolger
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
namespace Framework\Html\Form;

use \Framework\Html\Element;
use \Framework\Html\Form\Fields\Captcha;

class LimitedAttemptsForm 
extends Form {
    /**
    * @var integer The maximum number of attempts allows for this form before action is taken.
    */
    protected $max_attempts;
    
    /**
    * @var integer The amount of time the form can be locked to the current user.
    */
    protected $timeout_duration;
    
    /**
    * @var boolean Indicates if the form should refuse a user from submitting the form again when the max limit has been hit.
    */
    protected $enforce_max_attempts = true;
    
    /**
    * @var integer The attempt number where a captcha field will begin appearing.
    */
    protected $captcha_attempt_number;
    
    /**
    * @var string The label of the captcha field.
    */
    protected $captcha_label;
    
    /**
    * @var string The name of the session variable storing the current instance's submit attempts.
    */
    protected $attempts_session_name;
    
    /**
    * @var string The name of the session variable storing the current instance's last timeout time.
    */
    protected $timeout_session_name;
    
    /**
    * @var integer The number of attempts a user has submitted the form unsuccessfully.
    */
    protected $number_of_attempts;
    
    /**
    * @var boolean Indicates if the form is locked for the current user.
    */
    protected $locked_from_timeout = false;
    
    /**
    * @var integer The unix timestamp of the last time the form reached it's max attempts and was locked.
    */
    protected $last_timeout;
    
    /**
     * Initializes a new instance of AttemptsForm.
     *      
     * @param string $form_name The form name.
     * @param string $form_action (optional) The form submit location.
     * @param string $form_method The field method. Defaults to 'post'.
     * @param boolean $enable_token A flag to enable/disable the form token.     
     * @return void
     */
    public function __construct($form_name, $form_action = NULL, $form_method = "post", $enable_token = true) {    
        parent::__construct($form_name, $form_action, $form_method, $enable_token);
        
        //Set default values
        $this->max_attempts = config('framework')->attempts_form_max_attempts;
        $this->timeout_duration = config('framework')->attempts_form_timeout_duration;
        
        //Initialize the form's session variable name to store submit attempts
        $attempts_session_name = "{$form_name}_attempts";
        
        $this->attempts_session_name = $attempts_session_name;
        
        //Initialize the current attempt number
        $this->number_of_attempts = 1;
        
        if(!empty(session()->$attempts_session_name)) {
            $this->number_of_attempts = session()->$attempts_session_name;
        }
        
        session()->$attempts_session_name = $this->number_of_attempts;
        
        //Initialize the last time the max limit was reached if it exists
        $timeout_session_name = "{$form_name}_last_timeout";
        
        $this->timeout_session_name = $timeout_session_name;
        
        if(!empty(session()->$timeout_session_name)) {
            $this->last_timeout = session()->$timeout_session_name;
        }
    }
    
    /**
     * Sets the maximum allowed number of failed submits a user can make. 
     *
     * @param integer $max_attempts The maximum allowed number of attempts.     
     * @return void
     */
    public function setMaxAttempts($max_attempts) {
        assert('!empty($max_attempts) && is_integer($max_attempts)');
        
        $this->max_attempts = $max_attempts;
    }
    
    /**
     * Sets the amount of time the form can be locked to the current user. 
     *
     * @param integer $timeout_duration The number of minutes the form will be locked for.     
     * @return void
     */
    public function setTimeoutDuration($timeout_duration) {
        assert('!empty($timeout_duration) && is_integer($timeout_duration)');
        
        $timeout_duration_seconds = $timeout_duration * 60;
        
        $this->timeout_duration = $timeout_duration;
    }
    
    /**
     * Sets the attempt number that a captcha will begin to appear. 
     *
     * @param integer $attempt_number The attempt number where the captcha will begin appearing.
     * @param string $captcha_label The label for the captcha field.          
     * @return void
     */
    public function captchaAtAttemptNumber($attempt_number, $captcha_label) {
        assert('!empty($attempt_number) && is_integer($attempt_number)');
        
        $this->captcha_attempt_number = $attempt_number;
        $this->captcha_label = $captcha_label;
    }
    
    /**
     * Retrieves the timeout error message.
     *
     * @return string
     */
    private function getTimeoutErrorMessage() {
        //Set the timeout error message to display to the user
        $timeout_duration_minutes = $this->timeout_duration / 60;
                
        return array("You have exceeded the max number of attempts for this page. Please wait {$timeout_duration_minutes} minutes and try again.");
    }
    
    /**
     * Indicates if the form is locked for the current user.
     *
     * @return boolean
     */
    public function isLocked() {
        if(!empty($this->last_timeout)) {
            $current_time = time();
                
            $elapsed_time = $current_time - $this->last_timeout;

            if($elapsed_time <= $this->timeout_duration) {
                $this->field_errors = $this->getTimeoutErrorMessage();
            
                $this->locked_from_timeout = true;
            }
            else {
                $attempts_session_name = $this->attempts_session_name;
                $timeout_session_name = $this->timeout_session_name;
                
                session()->$attempts_session_name = 1;
                unset(session()->$timeout_session_name);
            
                $this->locked_from_timeout = false;
            }
        }
        
        return $this->locked_from_timeout;
    }
    
    /**
     * Checks for if all form fields are valid.
     *
     * @return boolean A flag indicating if the form fields are valid.
     */
    public function isValid() {
        //If the submit attempt is at or over the one to begin showing the captcha add it. 
        if(!empty($this->captcha_attempt_number) && $this->number_of_attempts >= $this->captcha_attempt_number) {
            $captcha_field = new Captcha($this->captcha_label);
            
            $last_field = array_pop($this->child_elements);
            
            //If the last form field is not an interactive field (such as a button) 
            //then insert the captcha field just prior to the last field
            if(!empty($last_field) && !$last_field->isInteractive()) {
                $this->addField($captcha_field);
                
                //Re-add the last form field directly since it has already been processed by addField() already
                $this->child_elements[$last_field->getName()] = $last_field;
            }
        }
            
        $is_valid = parent::isValid();
    
        if(!$is_valid) {
            //If under the max attempts
            if($this->number_of_attempts < $this->max_attempts) {        
                $this->number_of_attempts += 1;
                
                //Set the new attempt number in the session
                $attempts_session_name = $this->attempts_session_name;
                
                session()->$attempts_session_name = $this->number_of_attempts;
            }
            //If the max attempts have been reached
            else {
                $this->locked_from_timeout = true;
                $this->last_timeout = time();
                
                //Set the timeout in the session
                $timeout_session_name = $this->timeout_session_name;
                
                session()->$timeout_session_name = $this->last_timeout;
                
                //Set the timeout error message to display to the user
                $timeout_duration_minutes = $this->timeout_duration / 60;
                
                $this->field_errors = $this->getTimeoutErrorMessage();
            }
        }
        
        return $is_valid;
    }
    
    /**
     * Retrieves the form as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        if(!$this->locked_from_timeout) {
            return parent::toTemplateArray();
        }
        else {
            return array(
                "{$this->name}_errors" => $this->getMessagesHtml('field_errors')
            );
        }
    }
    
    /**
     * Renders and retrieves the form's html.
     *      
     * @return string
     */
    public function toHtml() {
        if(!$this->locked_from_timeout) {
            return parent::toHtml();
        }
        else {
            return "<div class=\"form_errors\">{$this->getMessagesHtml('field_errors')}</div>";
        }
    }
}
