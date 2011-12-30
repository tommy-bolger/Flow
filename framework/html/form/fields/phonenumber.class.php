<?php
/**
* Allows the rendering of a form phone number field as several text inputs and performing validation on its submitted data dynamically.
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

class PhoneNumber
extends \Framework\Html\Form\FieldObjects\Field {
    /**
    * @var string The area code of the phone number.
    */
    private $area_code;
    
    /**
    * @var string The first 3 digits of the phone number after the area code.
    */
    private $exchange;
    
    /**
    * @var string The last 4 digits of the phone number.
    */
    private $line_number;

    /**
     * Instantiates a new instance of PhoneNumber.
     *      
     * @param string $field_name (optional) The field name.
     * @param string $field_label (optional) The field label.
     * @return void
     */
    public function __construct($field_name = '', $field_label = '') {
        parent::__construct(NULL, $field_name, $field_label);
        
        $this->area_code = new textbox("{$this->name}[]", "", "", array('class' => 'area_code'));
        $this->area_code->setMaxLength(3);
        
        $this->exchange = new textbox("{$this->name}[]", "", "", array('class' => 'exchange'));
        $this->exchange->setMaxLength(3);
        
        $this->line_number = new textbox("{$this->name}[]", "", "", array('class' => 'line_number'));
        $this->line_number->setMaxLength(4);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/PhoneNumber.css');
    }
    
    /**
     * Sets the submitted phone number field value.
     *      
     * @param string $value The submitted value.
     * @return void
     */
    public function setValue($value) {
        if(!empty($value)) {
            $value_array = array();
        
            if(is_array($value)) {
                $value_array = $value;
            }
            else {
                $value = trim($value);
            
                if(strpos($value, '-') !== false) {
                    $value = str_replace('-', '', $value);
                }
                
                $value_array[0] = '';
                $value_array[1] = '';
                $value_array[2] = '';

                $value_split = str_split($value, 3);
                
                //The area code
                if(isset($value_split[0])) {
                    $value_array[0] = $value_split[0];
                }
                
                //The exchange
                if(isset($value_split[1])) {
                    $value_array[1] = $value_split[1];
                }
                
                //The first part of the line number
                if(isset($value_split[2])) {
                    $value_array[2] = $value_split[2];
                }
                
                //The last part of the line number (single character)
                if(isset($value_split[3])) {
                    $value_array[2] .= $value_split[3];
                }
            }
            
            $this->value = implode($value_array);
                
            if(isset($value_array[0])) {
                $this->area_code->setValue($value_array[0]);
            }
            
            if(isset($value_array[1])) {
                $this->exchange->setValue($value_array[1]);
            }
            
            if(isset($value_array[2])) {
                $this->line_number->setValue($value_array[2]);
            }
            
            if(is_null($this->area_code->getValue()) && is_null($this->exchange->getValue()) && is_null($this->line_number->getValue())) {
                $this->value = NULL;
            }
        }
    }
    
    /**
     * Validates the phone number field's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            $field_error_message = "{$this->label} requires a valid 10-digit phone number.";
            
            if(strlen($this->area_code->getValue()) != 3) {
                $this->setErrorMessage($field_error_message);
                
                return false;
            }
            
            if(strlen($this->exchange->getValue()) != 3) {
                $this->setErrorMessage($field_error_message);
                
                return false;
            }
            
            if(strlen($this->line_number->getValue()) != 4) {
                $this->setErrorMessage($field_error_message);
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Renders and retrieves the phone number field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {        
        return "{$this->area_code->getFieldHtml()}&nbsp;-&nbsp;{$this->exchange->getFieldHtml()}&nbsp;-&nbsp;{$this->line_number->getFieldHtml()}";
    }
}