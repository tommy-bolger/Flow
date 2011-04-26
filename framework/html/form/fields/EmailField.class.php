<?php
/**
* Allows the rendering of a form email field via a textbox and performing validation on its submitted data dynamically.
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
class EmailField 
extends Textbox {
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