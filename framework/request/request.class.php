<?php
/**
* The framework $_GET and $_POST data abstraction layer.
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

final class Request {
    private static $_request;

    public $get;
    
    public $post;
    
    /**
     * Returns the current instance of the object or instantiates it if it is not set.
     *
     * @return object
     */
    public static function getRequest() {
        if(!isset(self::$_request)) {
            self::$_request = new request();
        }
        
        return self::$_request;
    }
    
    /**
     * Instantiates a new instance of Request.
     *
     * @return void
     */
    public function __construct() {
        $this->get = new get();
        
        $this->post = new post();
    }
    
    /**
     * Retrieves a request variable value in either get or post.
     *
     * @param string $variable_name The name of the request variable to retrieve.
     * @return mixed
     */
    public function __get($variable_name) {
        $variable_value = NULL;
    
        if(!empty($this->get->$variable_name)) {
            $variable_value = $this->get->$variable_name;
        }
        elseif(!empty($this->post->$variable_name)) {
            $variable_value = $this->post->$variable_name;
        }
        
        return $variable_value;
    }
}