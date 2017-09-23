<?php
/**
* Validates a file extension against an array of valid extensions.
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

use \Exception;

class Extensions
extends Type {        
    public function __construct(array $valid_extensions, $continue_on_fail = false) {
        $this->setCompareValue($valid_extensions);
        
        $this->setContinueOnFail($continue_on_fail);
    }

    public function validate() {    
        if(!is_array($this->compare_value)) {
            throw new Exception("compare_value must be an array.");
        }
        
        if(!is_array($this->variable_value)) {
            throw new Exception("Value must be an array. This validation type should only be used with file validation.");
        }
        
        if(empty($this->variable_value['name'])) {
            throw new Exception("File name not found. This validation type should only be used with file validation.");
        }
        
        $extension = pathinfo($this->variable_value['name'], PATHINFO_EXTENSION);
        
        if(!in_array($this->compare_value[$extension])) {
            $this->error_message = "must be one of the allowed extensions: '" . implode(', ', $this->compare_value) . "'";
        }
        else {
            $this->valid_value = $this->variable_value;
        }
    }
}