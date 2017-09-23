<?php
/**
* Validates request field.
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

use \ReflectionClass;
use \Exception;

class Field {
    protected $name;
    
    protected $value;
    
    protected $valid_value;
    
    protected $nullable = false;

    protected $validation_steps = array();
    
    protected $error_messages = array();
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setValue($value) {
        $this->value = $value;
    }
    
    public function nullable() {
        $this->nullable = true;
        
        return $this;
    }
    
    public function __call($method_name, array $arguments) {
        if(isset($this->validation_steps[$method_name])) {
            throw new Exception("Validation step '{$method_name}' already exists for field '{$this->name}'.");
        }
    
        $step_class_name = ucfirst($method_name);
        
        $reflection_object = new ReflectionClass("\\Framework\\Validation\\Types\\{$step_class_name}"); 

        $validation_step = $reflection_object->newInstanceArgs($arguments);
        
        $this->validation_steps[] = $validation_step;
        
        return $this;
    }
    
    public function validate() {    
        if(isset($this->value)) {
            if(!empty($this->validation_steps)) {
                $this->valid_value = $this->value;
            
                foreach($this->validation_steps as $validation_step) {
                    $validation_step->setVariableValue($this->valid_value);
                
                    $validation_step->validate();
                    
                    $this->valid_value = $validation_step->getValidValue();
                    
                    if(!$validation_step->valid()) {
                        $this->error_messages[] = $validation_step->getError();
                        
                        if(!$validation_step->continueOnFail()) {
                            break;
                        }
                    }
                }
            }
        }
        else {
            if($this->nullable) {
                $this->valid_value = NULL;
            }
        }
    }
    
    public function valid() {
        return empty($this->error_messages);
    }
    
    public function getErrors() {
        return $this->error_messages;
    }
    
    public function getValidValue() {
        return $this->valid_value;
    }
}