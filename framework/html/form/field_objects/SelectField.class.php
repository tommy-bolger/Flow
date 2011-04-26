<?php
/**
* Allows the rendering of a form dropdown or listbox field and perform validation on the field's submitted data dynamically.
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
class SelectField 
extends Field {
    /**
    * @var boolean Flag determining if the field is multi-select.
    */
    private $is_multi = false;
    
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
        
        $this->addOptions($options);
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
     * @param string $field_value The submitted value.
     * @return void
     */
    public function setValue($field_value) {
        $this->value = $field_value;
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
    public function addOption($option_value, $option_text = "", $option_group_name = NULL, $prepend = false) {            
        if(!$prepend) {              
              $this->child_elements[$option_group_name][$option_value] = $option_text;
          }
          else {
              if(!isset($this->child_elements[NULL])) {
                  $this->child_elements = array(NULL => array()) + $this->child_elements;
              
                  $this->child_elements[NULL] = array();
              }
              
              $this->child_elements[NULL] = array($option_value => $option_text) + $this->child_elements[NULL];
          }
    }
    
    /**
     * Adds several options to a select field.
     *      
     * @param array $options The options for the select field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name).
     * @param string $option_group_name (optional) The name of the option group.     
     * @return void
     */
    public function addOptions($options, $option_group_name = NULL) {
        assert('is_array($options)');
    
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
        $this->addOption('none', $blank_option_text, NULL, true);
    }
    
    /**
     * Validates the select's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if($this->required) {
            if((!$this->is_multi && $this->value == 'none') || ($this->is_multi && empty($this->value))) {
                $this->setRequiredError();
                
                return false;
            }
        }
        
        return true;
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
                    foreach($option_group as $option_value => $option_text) {
                        $selected_attribute = '';

                        if($this->valueNotEmpty()) {                        
                            if(!is_array($this->value)) {
                                if((string)$option_value == (string)$this->value) {
                                    $selected_attribute = ' selected';
                                }
                            }
                            else {
                                if(in_array($option_value, $this->value)) {
                                    $selected_attribute = ' selected';
                                }
                            }
                        }
                        
                        $option_html .= "<option value=\"{$option_value}\"{$selected_attribute}>{$option_text}</option>";
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