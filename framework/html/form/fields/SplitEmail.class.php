<?php
/**
* Allows the rendering of a form email field as several text inputs and performing validation on its submitted data dynamically.
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
class SplitEmail
extends Field {
    /**
    * @var string The part of the email before the '@' symbol.
    */
    private $user_name;
    
    /**
    * @var string The part of the email before the '.' and after the '@' symbols.
    */
    private $domain_name;
    
    /**
    * @var string The part of the email after the domain name.
    */
    private $domain_extension;

    /**
     * Initializes a new instance of SplitEmail.
     *      
     * @param string $field_name (optional) The field name.
     * @param string $field_label (optional) The field label.
     * @return void
     */
    public function __construct($field_name = '', $field_label = '') {
        parent::__construct(NULL, $field_name, $field_label);
        
        $this->user_name = new Textbox("{$this->name}[]", "", "", array('class' => 'user_name'));
        
        $this->domain_name = new Textbox("{$this->name}[]", "", "", array('class' => 'domain_name'));
        
        $this->domain_extension = new Textbox("{$this->name}[]", "", "", array('class' => 'domain_extension'));
    }
    
    /**
     * Sets the submitted field's value.
     *      
     * @param string|array $value The submitted value.
     * @return void
     */
    public function setValue($value) {
        if(!empty($value)) {
            $value_array = array();
        
            if(is_array($value)) {
                $value_array = $value;
            }
            else {
                //Split into user_name as first element, domain and extension as second
                $username_domain_split = explode('@', $value);
                
                //Split the domain and extension
                $domain_extension_split = array();
                
                if(isset($username_domain_split[1])) {
                    $domain_extension_split = explode('.', $username_domain_split[1]);
                }
                
                $value_array[0] = '';
                $value_array[1] = '';
                $value_array[2] = '';

                //The user name
                if(isset($username_domain_split[0])) {
                    $value_array[0] = $username_domain_split[0];
                }
                
                //The domain
                if(isset($domain_extension_split[0])) {
                    $value_array[1] = $domain_extension_split[0];
                }
                
                //The domain extension
                if(isset($domain_extension_split[1])) {
                    $value_array[2] = $domain_extension_split[1];
                }
            }
            
            $this->value = '';
            
            //The username
            if(isset($value_array[0]) && !empty($value_array[0])) {
                $this->user_name->setValue($value_array[0]);
                
                $this->value .= "{$value_array[0]}";
            }
            
            //The domain name
            if(isset($value_array[1]) && !empty($value_array[1])) {
                $this->domain_name->setValue($value_array[1]);
                
                $this->value .= "@{$value_array[1]}.";
            }
            
            //The domain extension
            if(isset($value_array[2]) && !empty($value_array[2])) {
                $this->domain_extension->setValue($value_array[2]);
                
                $this->value .= "{$value_array[2]}";
            }
        }
    }
    /**
     * Validates the aplit email's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
                $this->setErrorMessage("{$this->label} is not a valid email address.");
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Renders and retrieves the field html.
     *      
     * @return string
     */
    public function getFieldHtml() {        
        return "{$this->user_name->getFieldHtml()}&nbsp;@&nbsp;{$this->domain_name->getFieldHtml()}&nbsp;.&nbsp;{$this->domain_extension->getFieldHtml()}";
    }
}