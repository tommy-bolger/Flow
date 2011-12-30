<?php
/**
* Allows the rendering of a form file input field trained to a single image file and performing validation on its submitted data dynamically.
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
class SingleImageField
extends FileField {
    /**
    * @var string The file path to where the field's image is stored.
    */
    private $image_directory_path;

    /**
     * Instantiates a new instance of SingleImageField.
     *      
     * @param string $file_name The file input name.
     * @param string $file_label The file input label.
     * @param string $image_directory_path The path to the directory where the image is stored.     
     * @param int $file_size (optional) The size limit the file input will allow in kilobytes. Defaults to 0 for no limit.
     * @param array $css_classes A list of classes for this field.
     * @return void
     */
    public function __construct($file_name, $file_label, $image_directory_path, $file_size_limit = 0, $css_classes = array()) {
        parent::__construct($file_name, $file_label, array('jpg', 'jpeg', 'gif', 'png'), $file_size_limit, $css_classes);
        
        $this->image_directory_path = rtrim($image_directory_path, '/') . '/';
    }
    
    /**
     * Sets the submitted field value.
     *      
     * @return void
     */
    public function setValue($field_value) {
        if(isset($_FILES[$this->name])) {
            parent::setValue($field_value);
        }
        else {
            $this->value = $field_value;
        }
    }
    
    /**
     * Renders and retrieves the field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {
        $uploaded_file_name = '';
        $saved_image_html = '';
        
        if(!empty($this->value)) {
            if(is_array($this->value) && isset($this->value['name'])) {
                $uploaded_file_name = $this->value['name'];
            }
            else {
                $uploaded_file_name = $this->value;
            }
        }

        if(empty($uploaded_file_name)) {
            $uploaded_file_name = "No image uploaded.";
        }
        else {
            $saved_image_html = "<img class=\"single_image\" src=\"{$this->image_directory_path}{$uploaded_file_name}\" />";
        }
    
        return "
            {$saved_image_html}
            <div class=\"single_file_name\">{$uploaded_file_name}</div>
            <input{$this->renderAttributes()} />
        ";
    }
}