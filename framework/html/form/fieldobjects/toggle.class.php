<?php
/**
* Allows the rendering of a form radio button or checkbox field and perform validation on the field's submitted data dynamically.
* Copyright (c) 2017, Tommy Bolger
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
namespace Framework\Html\Form\FieldObjects;

class Toggle
extends Field {
    /**
    * @var boolean A flag determining if the field has been checked.
    */
    protected $checked;

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
}