<?php
/**
* Allows the rendering of a form phone number field as several text inputs and performing validation on its submitted data dynamically.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class PhoneNumber
extends Field {
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
        
        $this->area_code = new Textbox("{$this->name}[]", "", "", array('class' => 'area_code'));
        $this->area_code->setMaxLength(3);
        
        $this->exchange = new Textbox("{$this->name}[]", "", "", array('class' => 'exchange'));
        $this->exchange->setMaxLength(3);
        
        $this->line_number = new Textbox("{$this->name}[]", "", "", array('class' => 'line_number'));
        $this->line_number->setMaxLength(4);
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