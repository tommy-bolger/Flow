<?php
/**
* Allows the rendering of a form radio button or checkbox field and perform validation on the field's submitted data dynamically.
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
class ToggleField
extends Field {
    /**
    * @var boolean A flag determining if the field has been checked.
    */
    private $checked;

    /**
     * Instantiates a new instance of ToggleField.
     *      
     * @param string $input_type The input type.
     * @param string $field_name The field name.
     * @param string $field_label (optional) The field label.
     * @param array $css_classes (optional) An array of classes.
     * @param string $field_value (optional) The field value, defaults to 1.  
     * @param boolean $is_checked (optional) Field checked flag, defaults to false.
     * @return void
     */
	public function __construct($input_type, $field_name, $field_label = NULL, $css_classes = array(), $field_value = '1', $is_checked = false) {
		parent::__construct($input_type, $field_name, $field_label, $css_classes);

		$this->setDefaultValue($field_value);

		if($is_checked) {
            $this->setChecked();
		}
		else {
            $this->setUnchecked();
		}
	}

	public function setWidth($width) {
        $this->__call('setWidth', array());
	}
	
	public function setReadOnly() {
        $this->__call('setReadOnly', array());
	}
	
	public function setWriteable() {
        $this->__call('setWriteable', array());
	}
	
	/**
     * Sets the field as checked.
     *      
     * @return void
     */
	public function setChecked() {
        $this->checked = true;
		
		switch($this->input_type) {
            case 'checkbox':
                 $this->setAttribute('checked', 'checked');
                break;
            case 'radio':
                 $this->setAttribute('checked', true);
                break;
		}
	}
	
	/**
     * Sets the field as unchecked.
     *      
     * @return void
     */
	public function setUnchecked() {
        $this->checked = false;
        
        $this->removeAttribute('checked', false);
	}
	
	/**
     * Validates the field's value.
     *      
     * @return boolean
     */
	protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if($this->required) {
    		if(!$this->checked) {
                $this->setRequiredError();
                
                return false;
    		}
		}
		
		return true;
	}
	
	/**
     * Sets the field to being either checked or not checked based on if its submitted data is empty or not.
     *      
     * @param string $submitted_value (optional) The field's submitted value.
     * @return void
     */
	public function setValue($submitted_value = "") {
		if(empty($submitted_value)) {
			$this->setUnchecked();
		}
		else {
			$this->setChecked();
			
            parent::setValue($submitted_value);
		}
	}
	
	/**
     * Gets the field's submitted value.
     *      
     * @return string
     */
	public function getValue() {
        if($this->checked) {
            return $this->value;
        }
        
        return "";
	}
	
	/**
     * Renders and retrieves the toggle field's html.
     *      
     * @return string
     */
    public function toHtml() {
        $field_html = "<ul class=\"form_field\">";
        
        if(!empty($this->error_message)) {
            $field_html .= "<li>{$this->getErrorMessageHtml()}</li>";
        }
        
        $field_html .= "<li>{$this->getFieldHtml()}";
        
        if(!empty($this->label)) {
            $field_html .= "&nbsp;{$this->getLabelHtml()}";
        }
        
        $field_html .= "</li></ul>";
        
        return $field_html;
	}
}