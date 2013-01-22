<?php
/**
* Allows the rendering of a group of form radio buttons and performing validation on its submitted data dynamically.
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

use \Framework\Html\Form\FieldObjects\ToggleGroup;

class RadioGroup
extends ToggleGroup {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'RadioGroup';
    
    /**
     * Instantiates a new instance of RadioGroup.
     *      
     * @param string $radio_group_name The radio group name.
     * @param string $radio_group_label (optional) The radio group label.
     * @param array $options (optional) The options for the group. Can have either format: array(value => option_label) or array(option_label).     
     * @param array $css_classes A list of classes for this field.
     * @return void
     */
    public function __construct($radio_group_name, $radio_group_label = NULL, $options = array(), $css_classes = array()) {
        parent::__construct($radio_group_name, $radio_group_label, $options, $css_classes);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
        
        $this->addJavascriptFile('form/fields/Select.js');
        $this->addJavascriptFile('form/fields/Dropdown.js');
        $this->addJavascriptFile('form/fields/RadioGroup.js');
    }
    
    /**
     * Sets the type of toggle fields that will appear in this group.
     *      
     * @return void
     */
    protected function setGroupType() {
        $this->group_type = 'radio';
        $this->option_name = $this->name;
    }
    
    /**
     * Determines if an option was selected and calls that object's setChecked().
     *      
     * @param object $option The option object.
     * @return void
     */
    protected function setOptionSelected($option) {
        parent::setOptionSelected($option);
    
        if(!empty($this->value)) {
            if($option->getDefaultValue() == $this->value) {
                $option->setChecked();
            }
        }
    }
}