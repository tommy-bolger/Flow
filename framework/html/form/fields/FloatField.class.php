<?php
/**
* Allows the rendering of a form float field as a text input and performing validation on its submitted data dynamically.
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
class FloatField
extends Textbox {
    /**
    * @var integer The number of digits on the left side of the decimal.
    */
    private $left_precision;
    
    /**
    * @var integer The number of digits on the right side of the decimal.
    */
    private $right_precision;

    /**
     * Instantiates a new instance of a float field.
     *      
     * @param string $float_name The float field name.
     * @param string $float_label (optional) The float field label.
     * @param string $float_value (optional) The float field value.
     * @return void
     */
	public function __construct($float_name, $float_label = "", $float_value = NULL) {
		parent::__construct($float_name, $float_label, $float_value, array('float_field'));
	}
	
    public function setMaxLength($max_length) {}
	
	/**
     * Sets the precision of the float field.
     *      
     * @param integer $left_precision The number of digits allowed to the left of the decimal place.
     * @param integer $right_precision The number of digits allowed to the right of the decimal place.
     * @return void
     */
	public function setPrecision($left_precision, $right_precision) {
        if(filter_var($left_precision, FILTER_VALIDATE_INT) === false) {
            throw new Exception('Left precision can only be an integer value.');
        }
        
        if(filter_var($right_precision, FILTER_VALIDATE_INT) === false) {
            throw new Exception('right precision can only be an integer value.');
        }
	
        $this->left_precision = $left_precision;
        
        $this->right_precision = $right_precision;
        
        parent::setMaxLength($left_precision + $right_precision + 1);
	}
    
    /**
     * Validates the float field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_FLOAT) === false) {
                $this->setErrorMessage("{$this->label} is not a valid decimal value.");
            
                return false;
            }
            
            $value_split = explode('.', $this->value);
            
            if(isset($this->left_precision) && strlen($value_split[0]) > $this->left_precision) {
                $this->setErrorMessage("{$this->label} can only have {$this->left_precision} digit(s) before the decimal place.");
                
                return false;
            }
            
            if(isset($this->right_precision) && strlen($value_split[1]) > $this->right_precision) {
                $this->setErrorMessage("{$this->label} can only have {$this->right_precision} digit(s) after the decimal place.");
                
                return false;
            }
        }
        
        return true;
    }
}