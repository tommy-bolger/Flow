<?php
/**
* Allows the rendering of a form email field via a textbox and performing validation on its submitted data dynamically.
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

namespace Framework\Html\Form\Fields;

class Email
extends Textbox {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'Email';

    /**
     * Initializes a new instance of EmailField.
     *      
     * @param string $email_name The email name.
     * @param string $email_label (optional) The email label.
     * @param string $email_value (optional) The email value.
     * @param array $css_classes (optional) A list of css classes.
     * @return void
     */
    public function __construct($email_name, $email_label = "", $email_value = NULL) {
        parent::__construct($email_name, $email_label, $email_value, array('email_field'));
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
    
        page()->addCssFile('framework/SplitEmail.css');
        page()->addJavascriptFile('form/fields/Email.js');
    }

    /**
     * Validates the email field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            $email_error_message = "{$this->label} is not a valid email address.";
        
            if(filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
                $this->setErrorMessage($email_error_message);
                
                return false;
            }
            else {
                //Check if there is a dot in the domain since filter_var doesn't validate this
                $value_split = explode('@', $this->value);
                
                if(strpos($value_split[1], '.') === false) {
                    $this->setErrorMessage($email_error_message);
                
                    return false;
                }
            }
        }
        
        return true;
    }
}