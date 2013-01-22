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
    private static $instance;
    
    private $required_variables;

    public $get;
    
    public $post;
    
    /**
     * Returns the current instance of the object or instantiates it if it is not set.
     *
     * @return object
     */
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new Request();
        }
        
        return self::$instance;
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
     * Sets the specified request variables as required in either $_GET or $_POST.
     *
     * @param array $required_variables A list of request variable names.
     * @return void
     */
    public function setRequired($required_variables) {
        assert('is_array($required_variables) && !empty($required_variables)');
        
        $this->required_variables = array_flip($required_variables);
    }
    
    /**
     * Retrieves a request variable value in either get or post.
     *
     * @param string $variable_name The name of the request variable to retrieve.
     * @return mixed
     */
    public function __get($variable_name) {
        $variable_value = NULL;
    
        if(!empty($this->post->$variable_name)) {
            $variable_value = $this->post->$variable_name;
        }
        elseif(!empty($this->get->$variable_name)) {
            $variable_value = $this->get->$variable_name;
        }

        if(isset($this->required_variables[$variable_name]) && empty($variable_value)) {
            throw new \Exception("Variable '{$variable_name}' could not be found in \$_GET or \$_POST.");
        }
        
        return $variable_value;
    }
    
    /**
     * Indicates if a request variable value is in either get or post.
     *
     * @param string $variable_name The name of the request variable.
     * @return boolean
     */
    public function __isset($variable_name) {
        if(isset($this->post->$variable_name)) {
            return true;
        }
        
        if(isset($this->get->$variable_name)) {
            return true;
        }
        
        return false;
    }
}