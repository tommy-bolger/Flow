<?php
/**
* Allows the rendering of a form button field and perform validation on the button's submitted data dynamically.
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

namespace Framework\Html\Form\FieldObjects;

class Button
extends Field {
    /**
    * @var boolean Flag indicating that the field's value gets included when retrieving the data of only interactive fields in the form objects.
    */
    protected $is_interactive = false;
    
    /**
    * @var boolean Flag indicating that the field can have a label.
    */
    protected $has_label = false;

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
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/Button.css');
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
        return array($this->getName() => $this->getFieldHtml());
    }
}