<?php
/**
* Allows the rendering of a group of radio buttons or checkboxes and perform validation on the field's submitted data dynamically.
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
class ToggleGroup
extends Field {
    /**
    * @var string The type of toggle group such as checkbox or radio.
    */
    private $group_type;
    
    /**
    * @var string The name of every option in this group.
    */
    private $option_name;

    /**
     * Initializes a new instance of ToggleGroup.
     *      
     * @param string $group_name The group name.
     * @param string $group_label The group label.
     * @param array $options (optional) The options for the group. Can have either format: array(value => option_label) or array(option_label).
     * @param array $css_classes (optional) An array of css classes.   
     * @return void
     */
	public function __construct($group_type, $group_name, $group_label, $options = array(), $css_classes = array()) {
		parent::__construct(null, $group_name, $group_label, $css_classes);
		
		$this->setGroupType($group_type);
		
		$this->addOptions($options);
	}
	
	/**
     * Sets the type of toggle fields that will appear in this group.
     *      
     * @param string $group_type The group type. Can either be 'checkbox' or 'radio'.
     * @return void
     */
	private function setGroupType($group_type) {
        switch($group_type) {
            case 'radio':
                $this->group_type = $group_type;
                $this->option_name = $this->name;
                break;
            case 'checkbox':
                $this->group_type = $group_type;
                $this->option_name = "{$this->name}[]";
                break;
            default:
                throw new Exception("Invalid toggle field group type '{$group_type}'");
                break;
        }
	}
	
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
        $new_option = new ToggleField($this->group_type, "", $option_label, array(), $option_value);
        $new_option->setName($this->option_name);
        
        $this->child_elements[$option_value] = $new_option;
	}
	
	/**
     * Adds several options to the group.
     *      
     * @param array $group_type The options to add
     * @return void
     */
	public function addOptions($options) {
        assert('is_array($options)');
	
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
	private function setOptionSelected($option) {
        assert('is_object($option) && get_class($option) == "ToggleField"');
	
        if(!empty($this->value)) {
            if(!is_array($this->value)) {
                if($option->getDefaultValue() == $this->value) {
                    $option->setChecked();
                }
            }
            else {
                if(in_array($option->getDefaultValue(), $this->value)) {
                    $option->setChecked();
                }
            }
        }
	}
	
	/**
     * Renders and retrieves the toggle group html.
     *      
     * @return string
     */
	public function getFieldHtml() {
        $field_html = "";
        
        if(!empty($this->child_elements)) {
            $option_name = strtoupper($this->name);
        
            $option_label_name = "{$option_name}_LABEL";
        
            foreach($this->child_elements as $option) {
                $this->setOptionSelected($option);
                
                $field_html .= $option->toHtml();
            }
        }
        
        return $field_html;
	}
}