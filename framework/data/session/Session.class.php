<?php
/**
* The framework abstraction layer to the $_SESSION variable.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
            self::$session = new Session();
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
        $storage_engine = config('framework')->getParameter('session_storage_engine');
        
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
        
        if(config('framework')->parameterExists('session_name')) {
            session_name(config('framework')->getParameter('session_name'));
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
        throw new Exception("Function name '{$function_name}' is not a valid function in this class.");
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
                throw new Exception("Session variable '{$variable_name}' is not valid. Set it first.");
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