<?php
/**
* Allows the rendering of a form date field via a text input and performing validation on its submitted data dynamically.
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
class DateField
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
	protected function addFieldFiles() {
        page()->addCssFile(page()->getThemeDirectoryPath() . 'css/jquery-ui-1.8.6.custom.css', false);
        page()->addJavascriptFile('./assets/javascript/jquery-1.4.4.min.js', false);
		page()->addJavascriptFile('./assets/javascript/jquery-ui-1.8.6.custom.min.js', false);
		page()->addJavascriptFile('./assets/javascript/datepicker.js', false);
    }
    
    public function setMaxLength($max_length) {}
    
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