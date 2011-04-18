<?php
/**
* Performs various operations, including magic function set/get, on an array of http request data.
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
class RequestData {
    /**
    * @var array A list of required request values.
    */
    private $required_values = array();

    /**
    * @var array All values for this request type.
    */
    protected $request_values;
    
    /**
	 * Initializes a new instance of RequestData.
	 *
	 * @param string $request_values (optional) The values to load into this request.
	 * @return void
	 */
    public function __construct($request_values = array()) {
        //If the request values are not empty loop through each and sanitize them
        if(!empty($request_values)) {
            $request_values = $this->encodeValues($request_values);
        }

        $this->request_values = $request_values;
    }
    
    /**
	 * Catches invalid function calls and throws an exception to prevent a fatal error.
	 *
	 * @param string $function_name The name of the function being called.
	 * @param mixed $function_arguments The arguments for the called function.	 	 
	 * @return void
	 */
	public function __call($function_name, $function_arguments) {
		throw new Exception("Function name '{$function_name}' is not a valid function in this class.");
	}
	
	/**
     * Encode values of an Array, Object, or String
     *      
     * @param mixed $values The value(s) to encode.
     * @return mixed
     */
    private function encodeValues($values) {
        if(!is_array($values)) {
            $values = htmlentities(trim($values));
        }
        else {
            foreach($values as $variable_name => $value) {
                $values[$variable_name] = $this->encodeValues($value);
            }
        }
        
        return $values;
    }
	
	/**
	 * Sets which request values are required.
	 *
	 * @param array $required_values The request values to make required.
	 * @return void
	 */
	public function setRequired($required_values) {
        assert('is_array($required_values)');
	
        $this->required_values = array_combine($required_values, $required_values);
	}
	
	/**
	 * Returns all request variables for this request type.
	 *	 	 
	 * @return array
	 */
	public function getAll() {
	   return $this->request_values;
	}
    
    /**
	 * Checks for if a variable exists in the request.
	 *
	 * @param string $variable_name The name of the variable. 	 
	 * @return mixed
	 */
    private function variableExists($variable_name) {
        //If the variable exists in the request then return true
        if(isset($this->request_values[$variable_name])) {
            return true;
        }
        
        //If the variable doesn't exist and is required then throw an exception 
        if(isset($this->required_values[$variable_name])) {
            throw new Exception("Parameter '{$variable_name}' is required but not present in the request.");
        }
        
        return false;
    }
    
    /**
	 * Retrieves the request variable value as a class property.
	 *
	 * @param string $variable_name The name of the request variable to retrieve.
	 * @return mixed
	 */
    public function __get($variable_name) {
        if($this->variableExists($variable_name)) {
            return $this->request_values[$variable_name];
        }
        
        return "";
    }
    
    /**
	 * Checks if a variable exists in the request.
	 *
	 * @param string $variable_name The name of the request variable.
	 * @return boolean
	 */
    public function __isset($variable_name) {
        return $this->variableExists($variable_name);
    }
}
