<?php
/**
* Abstraction layer of the $_GET variable.
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
namespace Framework\Request;

use \Framework\Utilities\Encryption;

final class Get
extends RequestData {
    /**
     * Initializes a new instance of GetData.
     * 
     * @return void
     */
    public function __construct() {
        $request_values = $_GET;
    
        if(isset($request_values['e'])) {
            $encoded_request_string = $request_values['e'];
            
            unset($request_values['e']);
        
            $encrypted_query_string = str_pad(strtr($encoded_request_string, '-_', '+/'), strlen($encoded_request_string) % 4, '=', STR_PAD_RIGHT);
        
            $request_string = Encryption::decrypt($encrypted_query_string, array('encrypted_url'));
            
            $request_parameters = array();
            
            parse_str($request_string, $request_parameters);
            
            $request_values = array_merge($request_values, $request_parameters);
        }
    
        parent::__construct($request_values);
    }
}