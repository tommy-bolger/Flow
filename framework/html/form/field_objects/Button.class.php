<?php
/**
* Allows the rendering of a form button field and perform validation on the button's submitted data dynamically.
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
class Button
extends Field {
    /**
    * @var boolean A flag indicating if the button was clicked.
    */
	private $clicked = false;

    /**
     * Instantiates a new instance of a Button.
     *      
     * @param string $input_type (optional) The input type.
     * @param string $field_name The field name.
     * @param string $button_label The button label.
     * @param array $css_classes (optional) A list of classes for this button.
     * @return void
     */
	public function __construct($input_type = 'button', $field_name, $button_label, $css_classes = array()) {
        $css_classes['class'] = 'form_button';
	
		parent::__construct($input_type, $field_name, NULL, $css_classes);
		
		$this->setDefaultValue($button_label);
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

	public function setRequired() {
        $this->__call('setRequired', array());
	}
	
	/**
     * Sets the button to having been clicked.
     * 
     * @param string $field_value The button submitted value - not used.
     * @return void
     */
	public function setValue($field_value = NULL) {
        if(!empty($field_value)) {
            parent::setValue($field_value);
                
            $this->clicked = true;
        }
	}
	
	/**
     * Retrieves the button's clicked status.
     *      
     * @return boolean
     */
	public function wasClicked() {
        return $this->clicked;
	}
	
	/**
     * Retrieves the button field's output as an array suitable for a template.
     *      
     * @return array
     */
	public function toTemplate() {        
        $index_base_name = strtoupper($this->getName());
        
        return array("{{{$index_base_name}}}" => $this->getFieldHtml());
	}
}