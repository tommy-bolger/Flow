<?php
/**
* Allows the rendering of a form text input field and performing validation on its submitted data dynamically.
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
class Textbox 
extends Field {
    /**
    * @var integerThe maximum number of allowable characters for this field.
    */
    private $max_length;

    /**
     * Initializes a new instance of Textbox.
     *      
     * @param string $textbox_name The textbox name.
     * @param string $textbox_label (optional) The textbox label.
     * @param string $textbox_value (optional) The textbox value.
     * @param array $css_classes (optional) A list of css classes for this field.     
     * @return void
     */
    public function __construct($textbox_name, $textbox_label = "", $textbox_value = NULL, $css_classes = array()) {
        parent::__construct("text", $textbox_name, $textbox_label, $css_classes);

        $this->setDefaultValue($textbox_value);
    }
    
    /**
     * Sets the maximum number of characters in a textbox.
     *      
     * @param integer $max_length The maximum length for this field.
     * @return void
     */
    public function setMaxLength($max_length) {
        $this->max_length = $max_length;
    
        $this->setAttribute('maxlength', $max_length);
    }
    
    /**
     * Validates the field's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(!empty($this->max_length)) {
                if(strlen($this->value) > $this->max_length) {
                    $this->setErrorMessage("{$this->label} has a length greater than the max allowed of {$this->max_length} characters.");
                
                    return false;
                }
            }
        }
        
        return true;
    }
}