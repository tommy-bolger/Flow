<?php
/**
* Allows the rendering of a form date field via a text input and performing validation on its submitted data dynamically.
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

class Date
extends Textbox {
    /**
     * Instantiates a new instance of DateField.
     *      
     * @param string $date_name The date field name.
     * @param string $date_label (optional) The date field label.
     * @param string $date_value (optional) The date field value.
     * @return void
     */
    public function __construct($date_name, $date_label = "", $date_value = NULL) {
        parent::__construct($date_name, $date_label, $date_value, array('date_field'));
        
        parent::setMaxLength(10);
    }
    
    /**
     * Adds the css and javascript for this field.
     *
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
    
        $this->addCssFile('jquery-ui.custom.css');
        $this->addJavascriptFile('jquery.min.js');
        $this->addJavascriptFile('jquery-ui.min.js');
        $this->addJavascriptFile('datepicker.js');
    }
    
    public function setMaxLength($max_length) {}
    
    /**
     * Retrieves the field's submitted value.
     *      
     * @return mixed
     */
    public function getValue() {        
        if(!empty($this->value) && $this->valid) {
            return date('Y-m-d', strtotime($this->value));
        }
    
        return $this->value;
    }
    
    /**
     * Sets the date field's default value.
     *      
     * @param string $default_value The default value of this field.
     * @return void
     */
    public function setDefaultValue($default_value) {
        if(!empty($default_value)) {
            $default_value = date('m/d/Y', strtotime($default_value));
        }
        
        parent::setDefaultValue($default_value);
    }
    
    /**
     * Validates the date field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            $date_error_message = "{$this->label} is not a valid date.";
            
            $value_length = strlen($this->value);
            
            if($value_length < 8 || $value_length > 10) {
                $this->setErrorMessage($date_error_message);
                
                return false;
            }
            
            if(strpos($this->value, '/') === false) {
                $this->setErrorMessage($date_error_message);
                
                return false;
            }
            
            $date_split = explode('/', $this->value);
            
            $month = 0;
            $day = 0;
            $year = 0;
            
            if(isset($date_split[0])) {
                $month = (int)$date_split[0];
            }
            
            if(isset($date_split[1])) {
                $day = (int)$date_split[1];
            }
            
            if(isset($date_split[2])) {
                $year = (int)$date_split[2];
            }
            
            if(checkdate($month, $day, $year) == false) {
                $this->setErrorMessage($date_error_message);
            
                return false;
            }
        }
        
        return true;
    }
}