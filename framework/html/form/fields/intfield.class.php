<?php
/**
* Allows the rendering of a form integer field via a text input and performing validation on its submitted data dynamically.
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

class IntField
extends Textbox {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'IntField';

    /**
    * @var integer The maximum allowable digits in the field.
    */
    private $max_digits;

    /**
     * Instantiates a new instance of IntField.
     *      
     * @param string $int_name The int field name.
     * @param string $int_label (optional) The int field label.
     * @param string $int_value (optional) The int field value.    
     * @return void
     */
    public function __construct($int_name, $int_label = "", $int_value = NULL) {
        parent::__construct($int_name, $int_label, $int_value, array('int_field'));
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();

        $this->addJavascriptFile('form/fields/IntField.js');
    }
    
    public function setMaxLength($max_length) {}
    
    /**
     * Sets the maximum number of digits allowable in this field.
     *      
     * @param integer $$max_digits The maximum allowable digits.
     * @return void
     */
    public function setMaxDigits($max_digits) {
        if(filter_var($max_digits, FILTER_VALIDATE_INT) === false) {
            throw new \Exception('Max digits can only be an integer value.');
        }
    
        $this->max_digits = $max_digits;
        
        parent::setMaxLength($max_digits);
    }
    
    /**
     * Validates the integer field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_INT) === false) {
                $this->setErrorMessage("{$this->label} is not a number.");
                
                return false;
            }
            
            if(isset($this->max_digits) && strlen($this->value) > $this->max_digits) {
                $this->setErrorMessage("{$this->label} cannot be more than {$this->max_digits} digits long.");
                
                return false;
            }
        }
        
        return true;
    }
}