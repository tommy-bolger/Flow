<?php
/**
* Allows the rendering of a group of radio buttons or checkboxes and perform validation on the field's submitted data dynamically.
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

class ToggleGroup
extends Field {
    /**
    * @var string The type of toggle group such as checkbox or radio.
    */
    protected $group_type;
    
    /**
    * @var string The name of every option in this group.
    */
    protected $option_name;

    /**
     * Initializes a new instance of ToggleGroup.
     *      
     * @param string $group_name The group name.
     * @param string $group_label The group label.
     * @param array $options (optional) The options for the group. Can have either format: array(value => option_label) or array(option_label).
     * @param array $css_classes (optional) An array of css classes.   
     * @return void
     */
    public function __construct($group_name, $group_label, $options = array(), $css_classes = array()) {
        parent::__construct(NULL, $group_name, $group_label, $css_classes);
        
        $this->setGroupType();
        
        $this->addOptions($options);
    }
    
    /**
     * Sets the type of toggle fields that will appear in this group.
     *      
     * @return void
     */
    protected function setGroupType() {}
    
    /**
     * Enables the field.
     *      
     * @return void
     */
    public function enable() {
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $option) {
                $option->enable();
            }
        }
    }
    
    /**
     * Disables the field.
     *      
     * @return void
     */
    public function disable() {
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $option) {
                $option->disable();
            }
        }
    }
    
    /**
     * Adds an option to the group.
     *      
     * @param mixed $option_value The option's value.
     * @param string $option_label The option's label.
     * @return void
     */
    public function addOption($option_value, $option_label) {
        $new_option = new Toggle($this->group_type, "", $option_label, array(), $option_value);
        $new_option->setName($this->option_name);
        $new_option->removeDataAttribute();
        
        $this->child_elements[$option_value] = $new_option;
    }
    
    /**
     * Adds several options to the group.
     *      
     * @param array $group_type The options to add
     * @return void
     */
    public function addOptions($options) {    
        if(!empty($options)) {
            foreach($options as $option_value => $option_label) {
                $this->addOption($option_value, $option_label);
            }
        }
    }
    
    /**
     * Determines if an option was selected and calls that object's setChecked().
     *      
     * @param object $option The option object.
     * @return void
     */
    protected function setOptionSelected($option) {}
    
    /**
     * Renders and retrieves the toggle group html.
     *      
     * @return string
     */
    public function getFieldHtml() {
        $field_html = "<input type=\"hidden\"{$this->renderAttributes()} />";
        
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $option) {
                $this->setOptionSelected($option);
                
                $field_html .= "<div>{$option->getFieldHtml()}{$option->getLabelText()}</div>";
            }
        }
        
        return $field_html;
    }
}