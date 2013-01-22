<?php
/**
* Allows the rendering of a form IP Address field via a textbox and performing validation on its submitted data dynamically.
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

class IPAddress
extends Textbox {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'IPAddress';

    /**
     * Initializes a new instance of IPAddress.
     *      
     * @param string $field_name The field name.
     * @param string $field_label (optional) The field label.
     * @param string $field_value (optional) The field value.
     * @param array $css_classes (optional) A list of css classes.
     * @return void
     */
    public function __construct($field_name, $field_label = "", $field_value = NULL) {
        parent::__construct($field_name, $field_label, $field_value, array('ip_address_field'));
        
        parent::setMaxLength(15);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();

        $this->addJavascriptFile('form/fields/IPAddress.js');
    }
    
    /**
     * Sets the maximum number of characters in a textbox.
     *      
     * @param integer $max_length The maximum length for this field.
     * @return void
     */
    public function setMaxLength($max_length) {}

    /**
     * Validates the field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
                $this->setErrorMessage("{$this->label} is not a valid IP Address.");
                
                return false;
            }
        }
        
        return true;
    }
}