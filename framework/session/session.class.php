<?php
/**
* The framework abstraction layer to the $_SESSION variable.
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
namespace Framework\Session;

class Session {
    /**
    * @var object The instance of this class.
    */
    private static $session;

    /**
     * Retrieves the instance of Session.
     *     
     * @return object
     */
    public static function getSession() {
        if(!isset(self::$session)) {
            self::$session = new session();
        }
        
        return self::$session;
    }
    
    /**
     * Sets the session save handler, configures the session, and starts the session.
     *     
     * @return void
     */
    public function start() {
        //Determine which session storage engine should be loaded if specified
        $storage_engine = config('framework')->session_storage_engine;
        
        switch($storage_engine) {
            case 'cache':
                break;
            case 'database':
                session_set_save_handler(
                    array('DatabaseSession','open'),
                    array('DatabaseSession','close'),
                    array('DatabaseSession','load'),
                    array('DatabaseSession','save'),
                    array('DatabaseSession','destroy'),
                    array('DatabaseSession','garbageCollection')
                );
                break;
        }
        
        session_cache_limiter('private');
        
        if(isset(config('framework')->session_name)) {
            session_name(config('framework')->session_name);
        }
        
        session_start();
    }
    
    /**
     * Catches invalid function calls and throws an exception to avoid a fatal error.
     *
     * @param string $function_name The name of the function being called.
     * @param mixed $function_arguments The arguments for the called function.          
     * @return void
     */
    public function __call($function_name, $function_arguments) {
        throw new \Exception("Function name '{$function_name}' is not a valid function in this class.");
    }
    
    /**
     * Retrieves of a $_SESSION variable value.
     *
     * @param string $variable_name The name of the variable to retrieve.
     * @return mixed
     */
    public function __get($variable_name) {
        $this->variableExists($variable_name);
        
        return $_SESSION[$variable_name];
    }
    
    /**
     * Sets a variable value in $_SESSION.
     *
     * @param string $variable_name The name of the variable to manipulate.
     * @param mixed $variable_value The value of the variable to set.
     * @return void
     */
    public function __set($variable_name, $variable_value) {
        $_SESSION[$variable_name] = $variable_value;
    }
    
    /**
     * Magic function to check if a value in $_SESSION exists.
     *
     * @param string $variable_name The name of the variable.
     * @return boolean
     */
    public function __isset($variable_name) {
        return $this->variableExists($variable_name, false);
    }
    
    /**
     * Removes a $_SESSION variable.
     *
     * @param string $variable_name The name of the variable to unset.
     * @return void
     */
    public function __unset($variable_name) {
        $this->variableExists($variable_name);
        
        unset($_SESSION[$variable_name]);
    }
    
    /**
     * Checks for if a variable exists in $_SESSION. Can either return a boolean or throw an exception if the variable doesn't exist.
     *
     * @param string $variable_name The name of the variable.
     * @param boolean $throw_exception (optional) Determine whether to return a boolean or throw an exception on failure. Defaults to true.
     * @return mixed
     */
    private function variableExists($variable_name, $throw_exception = true) {
        if(!isset($_SESSION[$variable_name])) {
            if($throw_exception) {
                throw new \Exception("Session variable '{$variable_name}' is not valid. Set it first.");
            }
            else {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Ends the session and destroys all data in it.
     *
     * @return void
     */
    public function end() {
        session_unset();
        session_destroy();
    }
}