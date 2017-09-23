<?php
/**
* Validates a phone number.
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

use \libphonenumber\PhoneNumberUtil;
use \libphonenumber\NumberParseException;
use \Framework\Core\Loader;

class PhoneNumber
extends Type {
    public function __construct($country, $continue_on_fail = false) {
        Loader::load('autoload.php', true, false);
    
        $this->setCompareValue($country);
        
        $this->setContinueOnFail($continue_on_fail);
    }

    public function validate() {        
        $error_message = "is not a valid phone number";
        
        $phone_number_utility = PhoneNumberUtil::getInstance();
        
        $phone_number_proto = NULL;
        
        try {
            $phone_number_proto = $phoneUtil->parse($this->variable_value, $this->compare_value);
        } 
        catch (NumberParseException $exception) {
            $this->error_message = $error_message;
        }
        
        if(empty($this->error_message)) {
            if(!$phone_number_utility->isValidNumber($phone_number_proto)) {
                $this->error_message = $error_message;
            }
        }
        else {
            $this->valid_value = array(
                'utility' => $phone_number_utility,
                'proto' => $phone_number_proto
            );
        }
    }
}