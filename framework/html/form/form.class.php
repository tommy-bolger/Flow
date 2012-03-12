<?php
/**
* Allows the rendering of a <form> tag with form fields and perform validation on submitted data dynamically.
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
namespace Framework\Html\Form;

use \Framework\Html\Element;
use \Framework\Utilities\Http;
use \Framework\Utilities\Encryption;

class Form 
extends Element {
    /**
    * @var string The form name attribute.
    */
    protected $name;
    
    /**
    * @var array The list of all interactive fields of the form.
    */
    protected $interactive_fields = array();
    
    /**
    * @var array The submitted form data.
    */
    protected $submitted_data;
    
    /**
    * @var array The values of the form fields.
    */
    protected $field_data;

    /**
    * @var array The error messages of the form fields.
    */
    protected $field_errors = array();
    
    /**
    * @var array The warning messages of the form.
    */
    protected $warnings = array();
    
    /**
    * @var array The confirmations messages of the form.
    */
    protected $confirmations = array();
    
    /**
    * @var boolean A flag indicating if the form was submitted.
    */
    protected $submitted = false;
    
    /**
    * @var boolean A flag indicating if the submitted values of the form fields are valid.
    */
    protected $valid;
    
    /**
     * Initializes a new instance of Form.
     *      
     * @param string $form_name The form name.
     * @param string $form_action (optional) The form submit location.
     * @param string $form_method The field method. Defaults to 'post'.
     * @param boolean $enable_token A flag to enable/disable the form token.     
     * @return void
     */
    public function __construct($form_name, $form_action = NULL, $form_method = "post", $enable_token = true) {    
        parent::__construct('form');
        
        if(empty($form_action)) {
            $form_action = Http::getCurrentUrl();
        }
        
        $this->setAction($form_action);
        
        $this->setName($form_name);
        
        $this->setId($form_name);
        
        $this->setAttribute('accept-charset', 'utf-8');
    
        $this->setFormData($form_method);
        
        if($enable_token) {
            $this->processFormToken();
        }
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
        
        $reflection_object = new \ReflectionClass("\\Framework\\Html\\Form\\Fields\\{$class_name}"); 

        $form_field = $reflection_object->newInstanceArgs($arguments);
        
        $this->addField($form_field);
        
        return $form_field;
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/Form.css');
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
    protected function setFormData($method) {
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
    protected function processFormToken() {
        $form_token_name = "{$this->name}_token";
        
        if(!$this->submitted) {
            $form_token = '';
        
            if(isset(session()->$form_token_name)) {
                $form_token = session()->$form_token_name;
            }
            else {
                $form_token = Encryption::generateShortHash();

                //Add the token to the session
                session()->$form_token_name = $form_token;
            }
            
            //Add a hidden token field
            $this->addHidden($form_token_name, $form_token);
        }
        else {
            if(!isset(session()->$form_token_name)) {
                throw new \Exception("Form token '{$form_token_name}' for form '{$this->name}' does not exist in the session.");
            }
            
            if(session()->$form_token_name != $this->submitted_data[$form_token_name]) {
                throw new \Exception("Token '{$form_token_name}' for form '{$this->name}' does not match up with session form token. A possible CSRF attack was attempted.");
            }
            
            //Add the hidden token field
            $this->addHidden($form_token_name, session()->$form_token_name);
        }
    }
    
    /**
     * Adds a field to the form.
     *      
     * @param object $form_field The form field object.
     * @param boolean $add_to_children A flag indicating whether to add the field to the form's child elements for rendering.     
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
                    $form_field->setSubmitted();
                
                    $field_submitted_value = $this->submitted_data[$field_name];
                }
            }

            $form_field->setValue($field_submitted_value);
        }
        
        if($form_field->getInputType() == 'file') {                
            $this->setAttribute('enctype', 'multipart/form-data');
        }
        
        $this->child_elements[$field_name] = $form_field;
        
        if($form_field->isInteractive()) {
            $this->interactive_fields[$field_name] = $form_field;
        }
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
     * Retrieves the form's interactive fields.
     *      
     * @return array
     */
    public function getInteractiveFields() {
        return $this->interactive_fields;
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
            if(isset($this->child_elements[$field_name])) {
                $this->child_elements[$field_name]->setDefaultValue($field_value);
            }
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
            if(isset($this->child_elements[$required_field])) {
                $this->child_elements[$required_field]->setRequired();
            }
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
     * Adds a warning message to the form.
     *      
     * @param string $warning_message The warning message.
     * @return void
     */
    public function addWarning($warning_message) {
        $this->warnings[] = $warning_message;
    }
    
    /**
     * Adds a confirmation message to the form.
     *      
     * @param string $confirmation_message The confirmation message.
     * @return void
     */
    public function addConfirmation($confirmation_message) {
        $this->confirmations[] = $confirmation_message;
    }
    
    /**
     * Checks for if all form fields are valid.
     *
     * @return boolean A flag indicating if the form fields are valid.
     */
    public function isValid() {
        if($this->submitted) {
            if(is_null($this->valid)) {
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
     * $param boolean $interactive_fields_only When set to true the values of all fields excluding hidden and button fields are returned.
     * @return array
     */
    public function getData($interactive_fields_only = false) {
        if(!$interactive_fields_only) {
            if(!isset($this->field_data['all_fields'])) {
                foreach($this->child_elements as $field_name => $field) {
                    $this->field_data['all_fields'][$field_name] = $field->getValue();
                }
            }
            
            return $this->field_data['all_fields'];
        }
        else {
            if(!isset($this->field_data['interactive_fields'])) {
                foreach($this->child_elements as $field_name => $field) {
                    if($field->isInteractive()) {
                        $this->field_data['interactive_fields'][$field_name] = $field->getValue();
                    }
                }
            }
            
            return $this->field_data['interactive_fields'];
        }
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
     * Retrieves the form's messages of a specified type.
     * 
     * @param string $message_type The type of message to render. Valid values are field_errors, warnings, and confirmations.     
     * @return string
     */
    protected function getMessagesHtml($message_type) {
        assert("\$message_type == 'field_errors' || \$message_type == 'warnings' || \$message_type == 'confirmations'");
    
        return '<p>' . implode('</p><p>', $this->$message_type) . '</p>';
    }
    
    /**
     * Retrieves the form as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = array();
        
        $form_open_template_name = "{$this->name}_open";
        $form_close_template_name = "{$this->name}_close";
        
        $template_array[$form_open_template_name] = $this->generateOpenTag();
        $template_array[$form_close_template_name] = "</form>";
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $field) {
                if($field->getInputType() != 'hidden') {
                    $template_array = array_merge($template_array, $field->toTemplateArray());
                }
                else {
                    $template_array[$form_open_template_name] .= $field->getFieldHtml();
                }
            }
        }
        
        if(!empty($this->field_errors)) {
            $template_array["{$this->name}_errors"] = $this->getMessagesHtml('field_errors');
        }
        
        if(!empty($this->warnings)) {
            $template_array["{$this->name}_warnings"] = $this->getMessagesHtml('warnings');
        }
        
        if(!empty($this->confirmations)) {
            $template_array["{$this->name}_confirmations"] = $this->getMessagesHtml('confirmations');
        }
        
        return $template_array;
    }
    
    /**
     * Renders and retrieves the form's html.
     *      
     * @return string
     */
    public function toHtml() {
        $form_html = '';
        
        if(empty($this->template)) {
            if(!empty($this->field_errors)) {
                $form_html .= "<div class=\"form_errors\">{$this->getMessagesHtml('field_errors')}</div>";
            }
            
            if(!empty($this->warnings)) {
                $form_html .= "<div class=\"form_warnings\">{$this->getMessagesHtml('warnings')}</div>";
            }
            
            if(!empty($this->confirmations)) {
                $form_html .= "<div class=\"form_confirmations\">{$this->getMessagesHtml('confirmations')}</div>";
            }
        }
        
        $form_html .= parent::toHtml();
        
        return $form_html;
    }
}
