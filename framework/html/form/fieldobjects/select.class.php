<?php
/**
* Allows the rendering of a form dropdown or listbox field and perform validation on the field's submitted data dynamically.
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

use \Exception;

class Select
extends Field {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'Select';

    /**
    * @var boolean Flag determining if the field is multi-select.
    */
    private $is_multi = false;
    
    /**
    * @var array A list of the selectable option values for this select.
    */
    protected $option_values = array();      
    
    /**
     * Instantiates a new instance of this SelectField.
     *      
     * @param string $select_name The select name.
     * @param string $select_label The select label.
     * @param array $options (optional) The options for the select field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name). Defaults to an empty array.
     * @param array $css_classes (optional) An array of css classes. Defaults to empty array   
     * @return void
     */
    public function __construct($select_name, $select_label, $options = array(), $css_classes = array()) {
        parent::__construct(NULL, $select_name, $select_label, $css_classes);
        
        if(!empty($options) && is_array($options[key($options)])) {
            foreach($options as $option_group => $options) {
                $this->addOptions($options, $option_group);
            }
        }
        else {
            $this->addOptions($options);
        }
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();

        $this->addJavascriptFile('form/fields/Select.js');
    }
    
    /**
     * Sets the select box's row size.
     *      
     * @param integer $size The element size.     
     * @return void
     */
    public function setSize($size) {
        $this->setAttribute('size', $size);
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
     * Sets the select as multi select.
     *      
     * @return void
     */
    public function setMultiSelect() {
        $this->is_multi = true;
    
        $this->setName("{$this->name}[]");
    
        $this->setAttribute('multiple', 'multiple');
    }
    
    /**
     * Sets the submitted field value.
     *      
     * @param array|string $field_index The submitted index of the option. Can also act as the direct field value when setting a default value.
     * @return void
     */
    public function setValue($field_index) {
        $field_value = NULL;
    
        if(!$this->is_multi) {
            if(isset($this->option_values[$field_index])) {
                $field_value = $this->option_values[$field_index];
            }
        }
        else {
            //If the selected value for the listbox contains an option that is not in the list of available options then discard all submitted values for this field.
            if(!empty($field_index)) {
                $field_value = array();
            
                foreach($field_index as $selected_index) {
                    if(isset($this->option_values[$selected_index])) {
                        $option_value = $this->option_values[$selected_index];
                    
                        $field_value[$option_value] = $option_value;
                    }
                }
            }
        }

        $this->value = $field_value;
    }
    
    /**
     * Sets the field's default value.
     *      
     * @param mixed $default_value The default value.
     * @return void
     */
    public function setDefaultValue($default_value) {
        $this->default_value = $default_value;

        if(!$this->submitted) {
            $option_indexes = array_flip($this->option_values);

            $field_index = NULL;
        
            if(!$this->is_multi) {
                if(isset($option_indexes[$default_value])) {
                    $field_index = $option_indexes[$default_value];
                }
            }
            else {
                $field_index = array();
            
                foreach($default_value as $value) {
                    if(isset($option_indexes[$value])) {
                        $field_index[] = $option_indexes[$value];
                    }
                }
            }
            
            $this->setValue($field_index);
        }
    }
    
    /**
     * Sets the field's default by its corresponding index.
     *      
     * @param array|string $field_index The index(es) of the option to make default.
     * @return void
     */
    public function setDefaultValueByIndex($field_index) {
        if(!$this->is_multi) {
            if(isset($this->option_values[$field_index])) {
                $this->default_value = $this->option_values[$field_index];
            }
        }
        else {        
            if(!is_array($field_index)) {
                throw new Exception("field_index must be an array.");
            }
        
            if(!empty($field_index)) {
                foreach($field_index as $index) {
                    if(isset($this->option_values[$index])) {
                        $this->default_value[] = $this->option_values[$index];
                    }
                }
            }
        }

        if(!$this->submitted) {
            $this->setValue($field_index);
        }
    }
    
    /**
     * Adds an option to the select field.
     *      
     * @param string $option_value The option's value.
     * @param string $option_text (optional) The option's display value. Defaults to an empty string.
     * @param string $option_group_name (optional) The name of the group this option belongs to. Defaults to null.
     * @param boolean $prepend (optional) A flag to tell this function to prepend this option to the beginning of the options list. Defaults to false.
     * @return void
     */
    public function addOption($option_value, $option_text = "", $option_group_name = NULL) {
        $option_index = count($this->option_values) + 1;
         
        $this->child_elements[$option_group_name][$option_value] = array(
            'index' => $option_index,
            'display_text' => $option_text
        );
            
        $this->option_values[$option_index] = $option_value;
    }
    
    /**
     * Adds several options to a select field.
     *      
     * @param array $options The options for the select field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name).
     * @param string $option_group_name (optional) The name of the option group.     
     * @return void
     */
    public function addOptions(array $options, $option_group_name = NULL) {    
        if(!empty($options)) {
            foreach($options as $option_value => $option_text) {
                if(!is_array($option_text)) {
                    $this->addOption($option_value, $option_text, $option_group_name);
                }
                else {
                    $option_group = $option_value;

                    foreach($option_text as $group_option_value => $group_option_text) {
                        $this->addOption($group_option_value, $group_option_text, $option_group);
                    }
                }
            }
        }
    }
    
    /**
     * Prepends a blank option to the select field.
     *      
     * @param string $blank_option_text (optional) The blank option's display text. Defaults to a blank string.
     * @return void
     */
    public function addBlankOption($blank_option_text = "") {
        if(empty($blank_option_text)) {
            $blank_option_text = '&nbsp;';
        }
        
        $this->child_elements[NULL] = array(
            '' => array(
                'index' => 0,
                'display_text' => $blank_option_text
            )
        ) + $this->child_elements[NULL];
            
        $this->option_values[0] = '';
    }
    
    /**
     * Renders and retrieves the select field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {
        $field_html = "<select{$this->renderAttributes()}>";

        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $group_name => $option_group) {
                $option_html = '';
            
                if(!empty($option_group)) {
                    foreach($option_group as $option_value => $option_attributes) {
                        $selected_attribute = '';

                        if($this->valueNotEmpty()) {                        
                            if(!is_array($this->value)) {
                                if((string)$option_value == (string)$this->value) {
                                    $selected_attribute = ' selected="selected"';
                                }
                            }
                            else {
                                if(isset($this->value[$option_value])) {
                                    $selected_attribute = ' selected="selected"';
                                }
                            }
                        }
                        
                        $option_html .= "<option value=\"{$option_attributes['index']}\"{$selected_attribute}>{$option_attributes['display_text']}</option>";
                    }
                }
                
                if(empty($group_name)) {
                    $field_html .= $option_html;
                }
                else {
                    $field_html .= "<optgroup label=\"{$group_name}\">{$option_html}</optgroup>";
                }
            }
        }
        
        $field_html .= "</select>";
        
        return $field_html;
    }
}