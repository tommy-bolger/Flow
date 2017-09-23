<?php
/**
* Validates request values.
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
namespace Framework\Validation;

use \Exception;

class Validation {
    protected $fields = array();
    
    protected $field_values = array();
    
    protected $valid_field_values = array();
    
    protected $error_messages = array();
    
    public function setFieldValues(array $field_values) {
        $this->field_values = $field_values;
    }
    
    public function field($field_name) {    
        if(!isset($this->fields[$field_name])) {
            $field = new Field();

            $field->setName($field_name);

            if(array_key_exists($field_name, $this->field_values)) {
                $field->setValue($this->field_values[$field_name]);
            }
        
            $this->fields[$field_name] = $field;
        }

        return $this->fields[$field_name];
    }
    
    public function validate() {
        if(!empty($this->fields)) {
            foreach($this->fields as $field_name => $field) {
                $field->validate();

                if(!$field->valid()) {
                    $this->error_messages[$field_name] = $field->getErrors();
                }
                else {
                    
                    $this->valid_field_values[$field_name] = $field->getValidValue();
                }
            }
        }
    }
    
    public function valid() {
        return empty($this->error_messages);
    }
    
    public function addError($error_message) {
        $this->error_messages[] = $error_message;
    }
    
    public function getErrors() {
        return $this->error_messages;
    }
    
    public function getValidFieldValues() {
        return $this->valid_field_values;
    }
}