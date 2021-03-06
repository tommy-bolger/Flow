<?php
/**
* The error handling class for the framework's api mode.
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
namespace Framework\Core\Modes\Api;

use \Framework\Core\Modes\Ajax\Error as BaseError;

class Error
extends BaseError {    
    /**
     * Retrieves the html output of an error when running in api mode.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return string
     */
    protected function getDisplay($error_code, $error_message, $error_file, $error_line, $error_trace) {    
        $environment = $this->framework->getEnvironment();
        $display_message = '';
        
        if(empty($environment) || $environment == 'production') {
            $display_message = "An unexpected error has been encountered. Please contact support for assistance and provide this incident number: " . strtoupper($this->incident_number);
        }
        else {
            $display_message = parent::getDisplay($error_code, $error_message, $error_file, $error_line, $error_trace);
        }
        
        $return_output = json_encode(array(
            'error_code' => $this->incident_number,
            'error_message' => $display_message
        ));
        
        return $return_output;
    }
}