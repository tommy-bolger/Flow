<?php
/**
* Allows the rendering of a form textarea field and performing validation on its submitted data dynamically.
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

class Textarea
extends \Framework\Html\Form\FieldObjects\Field {
    /**
     * Initializes a new instance of TextArea.
     *      
     * @param string $textarea_name The name of the textarea.
     * @param string $textarea_label (optional) The label for the textarea.
     * @param string $textara_value (optional) The value of the textarea.
     * @param array $css_classes (optional) A list of css classes for this field.           
     * @return void
     */
    public function __construct($textarea_name, $textarea_label = "", $textarea_value = NULL, $css_classes = array()) {
        parent::__construct(NULL, $textarea_name, $textarea_label, $css_classes);
        
        $this->setDefaultValue($textarea_value);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/TextArea.css');
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
                throw new \Exception("Specified textarea wrap can only be 'off', 'physical', or 'virtual'.");
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