<?php
/**
* Allows the rendering of a read only form field.
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

use \Framework\Html\Form\FieldObjects\Field;

class ReadOnly
extends Field {
    /**
    * @var boolean Indicates if the field is interactive to the user.
    */
    protected $is_interactive = false;

    /**
    * @var object The hidden input field used to store the read-only value.
    */
    private $value_field;

    /**
     * Initializes a new instance of ReadOnly.
     *      
     * @param string $name The field name.
     * @param string $display_text The text to display in place of the field html.     
     * @param string $label (optional) The field label.
     * @param string $value (optional) The field value.
     * @param array $css_classes (optional) A list of css classes for this field.     
     * @return void
     */
    public function __construct($name, $label = "", $value = NULL, $css_classes = array()) {
        parent::__construct(NULL, $name, $label, $css_classes);

        $this->value_field = new Hidden("{$name}_value", $value);
    }
    
    /**
     * Sets the submitted field value.
     *      
     * @return void
     */
    public function setValue($field_value) {
        if(!is_null($field_value) && empty($this->value)) {            
            $this->value_field->setValue($field_value);
        }
    }
    
    /**
     * Retrieves the field's submitted value.
     *      
     * @return mixed
     */
    public function getValue() {
        return $this->value_field->getValue();
    }
    
    /**
     * Sets the field's default value.
     *      
     * @param mixed $default_value The default value.
     * @return void
     */
    public function setDefaultValue($default_value) {
        $this->value_field->setValue($field_value);
    }
    
    /**
     * Renders and retrieves the field html.
     *      
     * @return string
     */
    public function getFieldHtml() {        
        return "
            <span>{$this->value_field->getValue()}</span>
           {$this->value_field->toHtml()}
        ";
    }
}