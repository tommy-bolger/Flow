<?php
/**
* Allows the rendering of a form textarea field and performing validation on its submitted data dynamically.
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
class TextArea 
extends Field {
    /**
     * Initializes a new instance of TextArea.
     *      
     * @param string $textarea_name The name of the textarea.
     * @param string $textarea_level (optional) The label for the textarea.
     * @param string $textara_value (optional) The value of the textarea.
     * @param array $css_classes (optional) A list of css classes for this field.           
     * @return void
     */
	public function __construct($textarea_name, $textarea_label = "", $textarea_value = NULL, $css_classes = array()) {
		parent::__construct(null, $textarea_name, $textarea_label, $css_classes);
		
		$this->setDefaultValue($textarea_value);
	}

    /**
     * Sets the wrapping style for the textarea.
     *      
     * @param string $wrap The wrapping style for the textarea. Valid values are 'off', 'physical', and 'virtual'
     * @return void
     */
	public function setWrap($wrap) {
		switch($wrap) {
			case 'off': case 'physical': case 'virtual':
				$this->setAttribute('wrap', $wrap);
				break;
			default:
				throw new Exception("Specified textarea wrap can only be 'off', 'physical', or 'virtual'.");
				break;
		}
	}
	
	/**
     * Sets the width (cols) of the textarea.
     *      
     * @param integer $width The width of the textarea.
     * @return void
     */
	public function setWidth($width) {
		$this->setAttribute('cols', $width);
	}
	
	/**
     * Sets the height (rows) of the textarea.
     *      
     * @param integer $height The height of the textarea.
     * @return void
     */
	public function setHeight($height) {
		$this->setAttribute('rows', $height);
	}
	
	/**
     * Sets the submitted field value.
     *      
     * @param mixed $field_value The submitted value.
     * @return void
     */
	public function setValue($field_value) {
        if(empty($field_value)) {
            $field_value = NULL;
        }
	
        $this->value = $field_value;
	}
	
	/**
     * Renders and retrieves the field's html.
     *      
     * @return string
     */
	public function getFieldHtml() {        
        return "<textarea{$this->renderAttributes()}>{$this->value}</textarea>";
	}
}