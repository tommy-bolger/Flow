<?php
/**
* Allows the rendering of a <form> tag with form fields and perform validation on submitted data dynamically.
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
namespace Framework\Html\Form;

use \Exception;
use \Framework\Core\Framework;
use \Framework\Html\Element;
use \Framework\Utilities\Http;
use \Framework\Utilities\Encryption;

class Form 
extends Element {
    /**
    * @var object The instance of the framework.
    */
    protected $framework;

    /**
    * @var string The form name attribute.
    */
    protected $name;
    
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
     * Initializes a new instance of Form.
     *      
     * @param string $form_name The form name.
     * @param string $form_action (optional) The form submit location.
     * @param string $form_method The field method. Defaults to 'post'.
     * @param boolean $enable_token A flag to enable/disable the form token.     
     * @return void
     */
    public function __construct($form_name, $form_action, $form_method = "post", $enable_token = true) {
        parent::__construct('form');
        
        $this->framework = Framework::getInstance();
        
        if(empty($form_action)) {
            $form_action = Http::getCurrentUrl();
        }
        
        $this->setAction($form_action);
        
        $this->setName($form_name);
        
        $this->setId($form_name);
        
        $this->addClass('form');
        
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
        $class_name = ltrim($function_name, "add");
        
        $reflection_object = new \ReflectionClass("\\Framework\\Html\\Form\\Fields\\{$class_name}"); 

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
     * Initializes the form's field data.
     *      
     * @param array $field_values The field values to set. Must be an associative array of format "field_name => field_value".
     * @return void
     */
    protected function setFieldValues(array $field_values) {
        $this->field_data = $field_values;
    
        if(!empty($field_values)) {
            foreach($field_values as $field_name => $field_value) {
                $this->child_elements[$field_name]->setValue($field_value);
            }
        }
    }
    
    /**
     * Adds form token or validates form token to session form token to prevent CSRF attacks.
     *      
     * @return void
     */
    /*protected function processFormToken() {
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
                throw new Exception("Form token '{$form_token_name}' for form '{$this->name}' does not exist in the session.");
            }
            
            if(session()->$form_token_name != $this->submitted_data[$form_token_name]) {
                throw new Exception("Token '{$form_token_name}' for form '{$this->name}' does not match up with session form token. A possible CSRF attack was attempted.");
            }
            
            //Add the hidden token field
            $this->addHidden($form_token_name, session()->$form_token_name);
        }
    }*/
    
    /**
     * Adds a field to the form.
     *      
     * @param object $form_field The form field object.
     * @return void
     */
    public function addField($form_field) {        
        $field_name = $form_field->getName();
        
        if(array_key_exists($field_name, $this->field_data)) {
            $field_name->setValue($this->field_data[$field_name]);
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
     * Sets specified form fields as required.
     *      
     * @param array $required_fields An array of field names to set as required.
     * @return void
     */
    public function setRequiredFields(array $required_fields) {    
        foreach($required_fields as $required_field) {
            if(isset($this->child_elements[$required_field])) {
                $this->child_elements[$required_field]->setRequired();
            }
        }
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
     * Retrieves the form's messages of a specified type.
     * 
     * @param string $message_type The type of message to render. Valid values are field_errors, warnings, and confirmations.     
     * @return string
     */
    protected function getMessagesHtml($message_type) {
        switch($message_type) {
            case 'field_errors':
            case 'warnings':
            case 'confirmations':
                break;
            default:
                throw new Exception("message_type can only be 'field_errors', 'warnings', or 'confirmations'.");
                break;
        }
        
        if(!empty($this->$message_type)) {
            return '<p>' . implode('</p><p>', $this->$message_type) . '</p>';
        }
        else {
            return '';
        }
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
            $errors_hidden_class = '';
        
            if(empty($this->field_errors)) {
                $errors_hidden_class = ' hidden';
            }
            
            $errors_html = "<div class=\"form_errors{$errors_hidden_class}\">{$this->getMessagesHtml('field_errors')}</div>";
            
            $warnings_hidden_class = '';
            
            if(empty($this->warnings)) {
                $warnings_hidden_class = ' hidden';
            }
            
            $warnings_html = "<div class=\"form_warnings{$warnings_hidden_class}\">{$this->getMessagesHtml('warnings')}</div>";
            
            $confirmations_hidden_class = '';
            
            if(empty($this->confirmations)) {
                $confirmations_hidden_class = ' hidden';
            }
            
            $confirmations_html = "<div class=\"form_confirmations{$confirmations_hidden_class}\">{$this->getMessagesHtml('confirmations')}</div>";
            
            array_unshift($this->child_elements, $errors_html, $warnings_html, $confirmations_html);
        }
        
        $form_html .= parent::toHtml();
        
        return $form_html;
    }
}
