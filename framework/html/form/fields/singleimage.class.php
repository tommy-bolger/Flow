<?php
/**
* Allows the rendering of a form file input field trained to a single image file and performing validation on its submitted data dynamically.
* Copyright (c) 2011, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Framework\Html\Form\Fields;

class SingleImage
extends File {
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
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        $this->addCssFile('framework/SingleImageField.css');
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