<?php
/**
* Allows the rendering of a form file input field trained to a specific file and performing validation on its submitted data dynamically.
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

class SingleFile
extends File {
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
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/SingleFileField.css');
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