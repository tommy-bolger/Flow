<?php
/**
* Allows the rendering of a <form> tag with form fields and perform validation on submitted data dynamically.
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
class Form 
extends Element {
    /**
    * @var string The form name attribute.
    */
    private $name;
    
    /**
    * @var array The submitted form data.
    */
    private $submitted_data;
    
    /**
    * @var array The values of the form fields.
    */
    private $field_data;

    /**
    * @var array The error messages of the form fields.
    */
    private $field_errors = array();
    
    /**
    * @var boolean A flag indicating if the form was submitted.
    */
    private $submitted = false;
    
    /**
    * @var boolean A flag indicating if the submitted values of the form fields are valid.
    */
    private $valid = false;
    
    /**
     * Initializes a new instance of Form.
     *      
     * @param string $form_name The form name.
     * @param string $form_action (optional) The form submit location.
     * @param string $form_method The field method. Defaults to 'post'.
     * @return void
     */
    public function __construct($form_name, $form_action = NULL, $form_method = "post") {    
        parent::__construct('form');
        
        $this->setAction($form_action);
        
        $this->setName($form_name);
        
        $this->setId($form_name);
        
        $this->setAttribute('accept-charset', 'utf-8');
    
        $this->setFormData($form_method);
        
        $this->processFormToken();
    }
    
    /**
     * The PHP __call function used to add form fields such as $form->addRadio().
     *      
     * @param string $function_name The name of the function.
     * @param array $arguments The function arguments.
     * @return object The new form field.
     */
    public function __call($function_name, $arguments) {
        assert('strpos($function_name, "add") !== false');
        
        $class_name = ltrim($function_name, "add");
        
        $reflection_object = new ReflectionClass($class_name); 

        $form_field = $reflection_object->newInstanceArgs($arguments);
        
        $this->addField($form_field);
        
        return $form_field;
    }
    
    /**
     * Sets the form's action.
     *      
     * @param string $action The form action location.
     * @return void
     */
    public function setAction($action) {    
        $this->setAttribute('action', $action);
    }
    
    /**
     * Sets the form's name.
     *      
     * @param string $name The form name.
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
        
        $this->setAttribute('name', $name);
    }

    /**
     * Retrieves the form's name.
     *      
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Initializes the form's submitted data.
     *      
     * @return void
     */
    private function setFormData($method) {
        assert("\$method == 'post' || \$method == 'get'");
        
        //Set the field's method
        $this->setAttribute('method', $method);
        
        $form_submitted_field = "{$this->name}_submitted";
        
        if(request()->$method->$form_submitted_field == 1) {
            $this->submitted_data = request()->$method->getAll();

            $this->submitted = true;
        }
        
        //Add a hidden field of the form's name to allow for multiple forms on a page
        $this->addHidden($form_submitted_field, 1);
    }
    
    /**
     * Adds form token or validates form token to session form token to prevent CSRF attacks.
     *      
     * @return void
     */
    private function processFormToken() {
        $form_token_name = "{$this->name}_token";
        
        if(!$this->submitted) {
            if(isset(session()->$form_token_name)) {
                unset(session()->$form_token_name);
            }
            
            $form_token = md5(uniqid(mt_rand(), true));

            //Add the token to the session
            session()->$form_token_name = $form_token;
            
            //Add a hidden token field
            $this->addHidden($form_token_name, $form_token);
        }
        else {
            if(!isset(session()->$form_token_name)) {
                throw new Exception("Form token '{$form_token_name}' for form '{$this->name}' does not exist in the session.");
            }
            
            if(session()->$form_token_name != $this->submitted_data[$form_token_name]) {
                throw new Exception("Token '{$form_token_name}' for form '{$this->name}' does not match up with session form token. A possible CSRF attack was attempted.");
            }
            
            //Add the hidden token field
            $this->addHidden($form_token_name, session()->$form_token_name);
        }
    }
    
    /**
     * Adds a field to the form.
     *      
     * @param object $form_field The form field object.
     * @return void
     */
    public function addField($form_field) {        
        assert('is_object($form_field)');
        assert('property_exists($form_field, "IS_FORM_FIELD")');
        
        $field_name = rtrim($form_field->getName(), '[]');
        
        assert('!isset($this->child_elements[$field_name])');
    
        if($this->submitted) {
            $field_submitted_value = NULL;
            
            if(!empty($this->submitted_data)) {
                if(isset($this->submitted_data[$field_name])) {
                    $field_submitted_value = $this->submitted_data[$field_name];
                }
            }

            $form_field->setValue($field_submitted_value);
        }
        
        if($form_field->getInputType() == 'file') {                
            $this->setAttribute('enctype', 'multipart/form-data');
        }
        
        $this->child_elements[$field_name] = $form_field;
    }
    
    /**
     * Retrieves a form field by name.
     *      
     * @param string $field_name The field's name.
     * @return object
     */
    public function getField($field_name) {
        return $this->getChild($field_name); 
    }
    
    /**
     * Sets the default values of all form fields specified by name.
     *      
     * @param array $field_values An array of field values in the following format: array(field_name => field_value).
     * @return void
     */
    public function setDefaultValues($field_values) {
        assert('is_array($field_values) && !empty($field_values)');
        
        foreach($field_values as $field_name => $field_value) {
            assert('isset($this->child_elements[$field_name])');

            $this->child_elements[$field_name]->setDefaultValue($field_value);
        }
    }
    
    /**
     * Sets specified form fields as required.
     *      
     * @param array $required_fields An array of field names to set as required.
     * @return void
     */
    public function setRequiredFields($required_fields = array()) {
        assert('is_array($required_fields) && !empty($required_fields)');
    
        foreach($required_fields as $required_field) {
            assert('isset($this->child_elements[$required_field])');
        
            $this->child_elements[$required_field]->setRequired();
        }
    }
    
    /**
     * Returns the form submitted flag indicating if it has been submitted.
     *      
     * @return boolean
     */
    public function wasSubmitted() {
        return $this->submitted;
    }
    
    /**
     * Adds an error message to the form.
     *      
     * @param string $error_message The error message.
     * @return void
     */
    public function addError($error_message) {
        $this->field_errors[] = $error_message;
    }
    
    /**
     * Checks for if all form fields are valid.
     *
     * @return boolean A flag indicating if the form fields are valid.
     */
    public function isValid() {
        if($this->submitted) {
            if($this->valid === false) {
                $fields_valid = true;
                
                foreach($this->child_elements as $form_field) {
                    if(!$form_field->isValid()) {
                        $fields_valid = false;
                        
                        $this->field_errors[] = $form_field->getErrorMessage();
                    }
                }
                
                $this->valid = $fields_valid;
            }
        }
        else {
            $this->valid = false;
        }
        
        return $this->valid;
    }
    
    /**
     * Retrieves the form's field values.
     *      
     * @return array
     */
    public function getData() {
        if(!isset($this->field_data)) {
            foreach($this->child_elements as $field_name => $field) {
                $this->field_data[$field_name] = $field->getValue();
            }
        }
        
        return $this->field_data;
    }
    
    /**
     * Resets all field values in the form.
     *      
     * @return void
     */
    public function reset() {
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $child_element) {
                $child_element->resetValue();
            }
        }
    }
    
    /**
     * Retrieves the form as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = array();
        $form_template_name = '{{' . strtoupper($this->name) . '}}';
        
        $template_array[$form_template_name] = $this->generateOpenTag();
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $field) {
                if($field->getInputType() != 'hidden') {
                    $template_array = array_merge($template_array, $field->toTemplateArray());
                }
                else {
                    $template_array[$form_template_name] .= $field->getFieldHtml();
                }
            }
        }
        
        return $template_array;
    }
    
    /**
     * Renders and retrieves the form's html.
     *      
     * @return string
     */
    public function toHtml() {
        $form_html = parent::toHtml();
        
        if(!empty($this->field_errors)) {
            $form_html = '<div class="form_errors">' . implode('<br />', $this->field_errors) . "</div>{$form_html}";
        }
        
        return $form_html;
    }
}
