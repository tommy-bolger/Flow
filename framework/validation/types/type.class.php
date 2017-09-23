<?php
/**
* Validates a value.
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
namespace Framework\Validation\Types;

class Type {
    protected $variable_value;
    
    protected $compare_value;
    
    protected $valid_value;
    
    protected $error_message;

    protected $error_message_template;
    
    protected $continue_on_fail = true;
    
    public function __construct($continue_on_fail = false) {
        $this->setContinueOnFail($continue_on_fail);
    }
    
    public function setVariableValue($variable_value) {
        $this->variable_value = $variable_value;
    }
    
    public function setCompareValue($compare_value) {
        $this->compare_value = $compare_value;
    }
    
    public function setContinueOnFail($continue_on_fail) {
        $this->continue_on_fail = !empty($continue_on_fail);
    }
    
    public function validate() {}
    
    public function valid() {
        return empty($this->error_message);
    }
    
    public function continueOnFail() {
        return $this->continue_on_fail;
    }
    
    public function getValidValue() {
        return $this->valid_value;
    }
    
    public function getError() {
        return $this->error_message;
    }
}