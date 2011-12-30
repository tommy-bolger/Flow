<?php
/**
* Allows the rendering of a form text input field and performing validation on its submitted data dynamically.
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

class Textbox 
extends \Framework\Html\Form\FieldObjects\Field {
    /**
    * @var integerThe maximum number of allowable characters for this field.
    */
    private $max_length;

    /**
     * Initializes a new instance of Textbox.
     *      
     * @param string $textbox_name The textbox name.
     * @param string $textbox_label (optional) The textbox label.
     * @param string $textbox_value (optional) The textbox value.
     * @param array $css_classes (optional) A list of css classes for this field.     
     * @return void
     */
    public function __construct($textbox_name, $textbox_label = "", $textbox_value = NULL, $css_classes = array()) {
        parent::__construct("text", $textbox_name, $textbox_label, $css_classes);

        $this->setDefaultValue($textbox_value);
    }
    
    /**
     * Sets the maximum number of characters in a textbox.
     *      
     * @param integer $max_length The maximum length for this field.
     * @return void
     */
    public function setMaxLength($max_length) {
        $this->max_length = $max_length;
    
        $this->setAttribute('maxlength', $max_length);
    }
    
    /**
     * Validates the field's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(!empty($this->max_length)) {
                if(strlen($this->value) > $this->max_length) {
                    $this->setErrorMessage("{$this->label} has a length greater than the max allowed of {$this->max_length} characters.");
                
                    return false;
                }
            }
        }
        
        return true;
    }
}