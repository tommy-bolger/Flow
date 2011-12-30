<?php
/**
* Performs various operations, including magic function set/get, on an array of http request data.
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
        throw new \Exception("Function name '{$function_name}' is not a valid function in this class.");
    }
    
    /**
     * Encode values of an Array, Object, or String
     *      
     * @param mixed $values The value(s) to encode.
     * @return mixed
     */
    private function encodeValues($values) {
        if(!is_array($values)) {
            $values = htmlentities(trim($values), ENT_QUOTES, 'UTF-8');
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
     * Retrieves a request variable and validates its value.
     *
     * @param string $variable_name The name of the request variable to retrieve.   
     * @param string $validation_type The type of validation to perform on the request value. Can either be 'integer' or 'float'.
     * @return mixed
     */
    public function getVariable($variable_name, $validation_type) {
        assert("\$validation_type == 'integer' || \$validation_type == 'float'");
    
        if($this->variableExists($variable_name)) {
            $variable_value = $this->request_values[$variable_name];
            
            switch($validation_type) {
                case 'integer':
                    $variable_value = filter_var($variable_value, FILTER_VALIDATE_INT);
                    break;
                case 'float':
                    $variable_value = filter_var($variable_value, FILTER_VALIDATE_FLOAT);
                    break;
            }
            
            if(empty($variable_value) && (string)$variable_value != '0') {
                $variable_value = NULL;
            }
            
            return $variable_value;
        }
        
        return '';
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
     * Retrieves all request variables that have a name containing the specified search string.
     *
     * @param string $search_string The string to search for.     
     * @return array
     */
    public function getByNameContaining($search_string) {
        $found_variables = array();
    
        foreach($this->request_values as $variable_name => $variable_value) {
            if(strpos($variable_name, $search_string) !== false) {
                $found_variables[$variable_name] = $variable_value;
            }
        }
        
        return $found_variables;
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
            throw new \Exception("Parameter '{$variable_name}' is required but not present in the request.");
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
            $variable_value = $this->request_values[$variable_name];
            
            if($variable_value == '') {
                $variable_value = NULL;
            }
            
            return $variable_value;
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
