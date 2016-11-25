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

use \Framework\Core\Framework;

class Session {
    /**
    * @var object The instance of this class.
    */
    private static $instance;
    
    /**
    * @var array The list of required session variables for this request.
    */
    protected $required_variables;

    /**
     * Retrieves the instance of Session.
     *     
     * @return object
     */
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new Session();
        }
        
        return self::$instance;
    }
    
    /**
     * Initializes a new instance of Session.
     *     
     * @return void
     */
    public function __construct() {
        /*
            The session name and storage engine are being pulled from the framework configuration
            instead of being required constructor arguments because this enables the session to be
            initialized on demand when session() is called instead of it being loaded every page and ajax
            request. This has the potential to gain some performance on web requests that do not require
            a session.
        */
        $framework = Framework::getInstance();
    
        //Determine which session storage engine should be loaded if specified
        $storage_engine = $framework->configuration->session_storage_engine;
        
        //Set the session save handler based on the storage engine
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
        
        if(isset($framework->configuration->session_name)) {
            session_name($framework->configuration->session_name);
        }
        
        session_start();
    }
    
    /**
     * Retrieves of a $_SESSION variable value.
     *
     * @param string $variable_name The name of the variable to retrieve.
     * @return mixed
     */
    public function __get($variable_name) {
        $variable_value = '';
        
        if(isset($_SESSION[$variable_name])) {
            $variable_value = $_SESSION[$variable_name];
        }
        else {
            if(isset($this->required_variables)) {
                throw new \Exception("Session variable '{$variable_name}' is required but cannot be found in the session.");
            }
        }
        
        return $variable_value;
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
        return isset($_SESSION[$variable_name]);
    }
    
    /**
     * Removes a $_SESSION variable.
     *
     * @param string $variable_name The name of the variable to unset.
     * @return void
     */
    public function __unset($variable_name) {
        if(isset($_SESSION[$variable_name])) {
            unset($_SESSION[$variable_name]);
        }
    }
    
    /**
     * Sets the variable names that are required in the session.
     *
     * @param array $variable_names The names of the variables that required.
     * @return void
     */
    public function setRequired(array $variable_names) {
        assert('!empty($variable_names)');
        
        $this->required_variables = array_combine($variable_names, $variable_names);
    }
    
    /**
     * Returns all session variables.
     *
     * @return array
     */
    public function getAll() {
        return $_SESSION;
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