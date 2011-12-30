<?php
/**
* Allows the rendering of a form field and perform validation on the field's submitted data dynamically.
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

class Field
extends \Framework\Html\Element {
    /**
    * @var boolean A flag indicating that this object is a field form.
    */
    public $IS_FORM_FIELD = true;
    
    /**
    * @var string The input type attribute of the field.
    */
    protected $input_type;
    
    /**
    * @var string The name attribute of the field.
    */
    protected $name;
    
    /**
    * @var string The field label.
    */
    protected $label;
    
    /**
    * @var boolean Flag indicating whether the form field has been submitted.
    */
    protected $submitted = false;
    
    /**
    * @var mixed The value of the field.
    */
    protected $value = NULL;
    
    /**
    * @var mixed The default value of the field.
    */
    protected $default_value;
    
    /**
    * @var boolean A flag indicating that the field's value is valid.
    */
    protected $valid;
    
    /**
    * @var boolean A flag indicating that the field requires a value to be submitted.
    */
    protected $required = false;
    
    /**
    * @var string The error message of the field if the submitted value is invalid.
    */
    protected $error_message;

    /**
     * Instantiates a new instance of Field.
     *      
     * @param string (optional) $input_type The input type.
     * @param string (optional) $field_name The field name.
     * @param string (optional) $field_label The field label.
     * @param array (optional) $css_classes An array of css classes.
     * @return void
     */
    public function __construct($input_type = NULL, $field_name = "", $field_label = "", $css_classes = array()) {
        parent::__construct('input');
        
        if(!empty($input_type)) {
            $this->setInputType($input_type);
        }
        
        if(!empty($field_name)) {
            $this->setName($field_name);
        
            $this->setId($field_name);
        }
        
        $this->setLabel($field_label);
        
        $this->addClasses($css_classes);
    }
    
    /**
     * Catches calls to functions that do not exist in this class and throws an Exception to prevent a fatal error.
     *      
     * @param string $function_name The name of the function.
     * @param array $arguments The arguments to the function.
     * @return void
     */
    public function __call($function_name, $arguments) {        
        throw new \Exception("Function name '{$function_name}' does not exist for this class.");
    }
    
    /**
     * Sets the html input type.
     *      
     * @param string $input_type The input type.
     * @return void
     */
    private function setInputType($input_type) {
        $this->input_type = $input_type;
        
        $this->setAttribute('type', $input_type);
    }
    
    /**
     * Retrieves the input type.
     *      
     * @return string
     */
    public function getInputType() {
        return $this->input_type;
    }
    
    /**
     * Sets the field input name.
     *      
     * @param string $field_name The field name.
     * @return void
     */
    public function setName($field_name) {
        assert('!empty($field_name)');
    
        $this->name = $field_name;
            
        $this->setAttribute('name', $field_name);
    }
    
    /**
     * Retrieves the field input name. Trims appended '[]' if detected.
     *      
     * @return string
     */
    public function getName() {
        return rtrim($this->name, '[]');
    }
    
    /**
     * Sets the field's label.
     *      
     * @param string $field_label The field label.
     * @return void
     */
    public function setLabel($field_label) {
        $this->label = $field_label;
    }
    
    /**
     * Disables the field.
     *      
     * @return void
     */
    public function disable() {        
        $this->setAttribute('disabled', 'disabled');
    }
    
    /**
     * Enables the field.
     *      
     * @return void
     */
    public function enable() {                
        $this->removeAttribute('disabled');
    }
    
    /**
     * Sets the field to be read-only.
     *      
     * @return void
     */
    public function setReadOnly() {                
        $this->setAttribute('readonly', 'readonly');
    }
    
    /**
     * Sets the field to be writeable.
     *      
     * @return void
     */
    public function setWriteable() {                
        $this->removeAttribute('readonly');
    }
    
    /**
     * Sets the field to be readable and writeable.
     *      
     * @param int $field_width The new width of the field.
     * @return void
     */
    public function setWidth($field_width) {
        assert('is_int($field_width)');
    
        $this->setAttribute("size", $field_width);
    }
    
    /**
     * Sets the field as having been submitted.
     *      
     * @return void
     */
    public function setSubmitted() {
        $this->submitted = true;
    }
    
    /**
     * Sets the field as required.
     *      
     * @return void
     */
    public function setRequired() {
        $this->required = true;
    }
    
    /**
     * Returns the value of the required flag.
     *      
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }
    
    /**
     * Sets the required field error message.
     *      
     * @return void
     */
    protected function setRequiredError() {                
        $this->setErrorMessage("{$this->label} is a Required Field.");
    }
    
    /**
     * Sets the field error message.
     *      
     * @return void
     */
    public function setErrorMessage($error_message) {
        $this->error_message = $error_message;
    }
    
    /**
     * Retrieves the field error message.
     *      
     * @return string
     */
    public function getErrorMessage() {
        return $this->error_message;
    }
    
    /**
     * Sets the submitted field's value.
     *      
     * @param string $field_value The submitted value.
     * @return void
     */
    public function setValue($field_value) {
        if(empty($field_value)) {
            $field_value = NULL;
        }
    
        $this->value = $field_value;
        
        $this->setAttribute('value', $field_value);
    }
    
    /**
     * Retrieves the field's submitted value.
     *      
     * @return mixed
     */
    public function getValue() {
        return $this->value;
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
            $this->setValue($default_value);
        }
    }
    
    /**
     * Retrieves the field's default value.
     *      
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->default_value;
    }
    
    /**
     * Resets the field's value to its default state.
     *      
     * @return void
     */
    public function resetValue() {
        if(isset($this->default_value)) {            
            $this->setValue($this->default_value);
        }
        else {
            $this->value = NULL;
            
            $this->removeAttribute('value');
        }
    }
    
    /**
     * Checks if the submitted value is not empty.
     *      
     * @return boolean
     */
    protected function valueNotEmpty() {
        if(empty($this->value) && $this->value !== false && $this->value !== 0 && $this->value !== '0') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validates the field's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if($this->required && !$this->valueNotEmpty()) {
            $this->setRequiredError();
            
            return false;
        }

        return true;
    }
    
    /**
     * Retrieves's the field's validity status.
     *      
     * @return boolean
     */
    public function isValid() {
        $this->valid = $this->validate();

        return $this->valid;
    }
    
    /**
     * Retrieves the field's label html.
     *      
     * @return string
     */
    protected function getLabelHtml() {        
        if(!empty($this->label)) {
            $label = $this->label;
        
            if($this->required) {
                $label = "<span class=\"required\">*{$label}</span>";
            }
        
            return "<label for=\"{$this->name}\">{$label}:</label>";
        }
        
        return "";
    }
    
    /**
     * Retrieves the field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {        
        return "<input{$this->renderAttributes()} />";
    }
    
    /**
     * Retrieves the field's error message html.
     *      
     * @return string
     */
    protected function getErrorMessageHtml() {
        if(!$this->valid) {
            return "<span class=\"field_error_message\">{$this->error_message}</span>";
        }
        
        return "";
    }
    
    /**
     * Retrieves the field as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = array();
        
        $index_base_name = $this->getName();
        
        //Add the field label html
        $template_array["{$index_base_name}_label"] = $this->getLabelHtml();
       
        //Add the field html        
        $template_array[$index_base_name] = $this->getFieldHtml();
        
        //Add field error message html
        $template_array["{$index_base_name}_error"] = $this->getErrorMessageHtml();
        
        return $template_array;
    }
    
    /**
     * Retrieves the field's html with label wrapped in a list.
     *      
     * @return string
     */
    public function toHtml() {    
        $field_html = "<ul class=\"form_field\">";
        
        if(!empty($this->label)) {
            $field_html .= "<li class=\"label\">{$this->getLabelHtml()}</li>";
        }
        
        $field_html .= "<li class=\"field\">{$this->getFieldHtml()}</li></ul>";
        
        return $field_html;
    }
}