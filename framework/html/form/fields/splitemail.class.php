<?php
/**
* Allows the rendering of a form email field as several text inputs and performing validation on its submitted data dynamically.
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

class SplitEmail
extends \Framework\Html\Form\FieldObjects\Field {
    /**
    * @var string The part of the email before the '@' symbol.
    */
    private $user_name;
    
    /**
    * @var string The part of the email before the '.' and after the '@' symbols.
    */
    private $domain_name;
    
    /**
    * @var string The part of the email after the domain name.
    */
    private $domain_extension;

    /**
     * Initializes a new instance of SplitEmail.
     *      
     * @param string $field_name (optional) The field name.
     * @param string $field_label (optional) The field label.
     * @return void
     */
    public function __construct($field_name = '', $field_label = '') {
        parent::__construct(NULL, $field_name, $field_label);
        
        $this->user_name = new textbox("{$this->name}[]", "", "", array('class' => 'user_name'));
        
        $this->domain_name = new textbox("{$this->name}[]", "", "", array('class' => 'domain_name'));
        
        $this->domain_extension = new textbox("{$this->name}[]", "", "", array('class' => 'domain_extension'));
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/SplitEmail.css');
    }
    
    /**
     * Sets the submitted field's value.
     *      
     * @param string|array $value The submitted value.
     * @return void
     */
    public function setValue($value) {
        if(!empty($value)) {
            $value_array = array();
        
            if(is_array($value)) {
                $value_array = $value;
            }
            else {
                //Split into user_name as first element, domain and extension as second
                $username_domain_split = explode('@', $value);
                
                //Split the domain and extension
                $domain_extension_split = array();
                
                if(isset($username_domain_split[1])) {
                    $domain_extension_split = explode('.', $username_domain_split[1]);
                }
                
                $value_array[0] = '';
                $value_array[1] = '';
                $value_array[2] = '';

                //The user name
                if(isset($username_domain_split[0])) {
                    $value_array[0] = $username_domain_split[0];
                }
                
                //The domain
                if(isset($domain_extension_split[0])) {
                    $value_array[1] = $domain_extension_split[0];
                }
                
                //The domain extension
                if(isset($domain_extension_split[1])) {
                    $value_array[2] = $domain_extension_split[1];
                }
            }
            
            $this->value = '';
            
            //The username
            if(isset($value_array[0]) && !empty($value_array[0])) {
                $this->user_name->setValue($value_array[0]);
                
                $this->value .= "{$value_array[0]}";
            }
            
            //The domain name
            if(isset($value_array[1]) && !empty($value_array[1])) {
                $this->domain_name->setValue($value_array[1]);
                
                $this->value .= "@{$value_array[1]}.";
            }
            
            //The domain extension
            if(isset($value_array[2]) && !empty($value_array[2])) {
                $this->domain_extension->setValue($value_array[2]);
                
                $this->value .= "{$value_array[2]}";
            }
        }
    }
    /**
     * Validates the aplit email's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
                $this->setErrorMessage("{$this->label} is not a valid email address.");
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Renders and retrieves the field html.
     *      
     * @return string
     */
    public function getFieldHtml() {        
        return "{$this->user_name->getFieldHtml()}&nbsp;@&nbsp;{$this->domain_name->getFieldHtml()}&nbsp;.&nbsp;{$this->domain_extension->getFieldHtml()}";
    }
}