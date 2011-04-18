<?php
/**
* Allows the rendering of a form file input field trained to a specific file and performing validation on its submitted data dynamically.
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
class SingleFileField 
extends FileField {
    /**
     * Instantiates a new instance of SingleFileField.
     *      
     * @param string $file_name The file input name.
     * @param string $file_label The file input label.
     * @param array $accepted_files (optional) A list of accepted file extenions without the preceding dot.
     * @param int $file_size (optional) The size limit the file input will allow in kilobytes. Defaults to 0 for no limit.
     * @param array $css_classes (optional) A list of classes for this field.
     * @return void
     */
	public function __construct($file_name, $file_label, $accepted_files = array(), $file_size_limit = 0, $css_classes = array()) {
		parent::__construct($file_name, $file_label, $accepted_files, $file_size_limit, $css_classes);
	}
	
	/**
     * Sets the field's submitted value.
     *      
     * @return void
     */
	public function setValue($field_value) {
        if(empty($field_value)) {
            if(isset($_FILES[$this->name])) {
                parent::setValue($field_value);
            }
        }
        else {
            $this->value = $field_value;
        }
	}
    
    /**
     * Renaders and retrieves the field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {
        $uploaded_file = 'No file uploaded.';
        
        if(!empty($this->value)) {            
            if(is_array($this->value) && isset($this->value['name'])) {
                $uploaded_file = $this->value['name'];
            }
            else {
                $uploaded_file = $this->value;
            }
        }
        
        if(isset($this->valid) && !$this->valid) {
            if(!empty($this->default_value)) {
                $uploaded_file = $this->default_value;
            }
        }
    
        return "
            <div class=\"single_file_name\">{$uploaded_file}</div>
            <input{$this->renderAttributes()} />
        ";
	}
}