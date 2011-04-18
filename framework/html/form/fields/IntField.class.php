<?php
/**
* Allows the rendering of a form integer field via a text input and performing validation on its submitted data dynamically.
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
class IntField
extends Textbox {
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
	
	public function setMaxLength($max_length) {}
	
	/**
     * Sets the maximum number of digits allowable in this field.
     *      
     * @param integer $$max_digits The maximum allowable digits.
     * @return void
     */
	public function setMaxDigits($max_digits) {
        if(filter_var($max_digits, FILTER_VALIDATE_INT) === false) {
            throw new Exception('Max digits can only be an integer value.');
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
                $this->setErrorMessage("{$this->label} is not a valid integer value.");
                
                return false;
            }
            
            if(isset($this->max_digits) && strlen($this->value) > $this->max_digits) {
                $this->setErrorMessage("{$this->label} cannot be more than {$this->max_digits} digits.");
                
                return false;
            }
        }
        
        return true;
    }
}