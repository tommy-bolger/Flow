<?php
/**
* The parent of all cache module objects.
* Copyright (c) 2015, Tommy Bolger
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
namespace Framework\Caching\Modules;

use \Exception;

class Module {
    /**
    * @var array The connection configuration.
    */ 
    protected $connection_configuration = array();

    /**
    * @var object The connection instance.
    */ 
    protected $connection_object;
    
    /**
     * Catches all function calls not present in this class and passes them to the connection object.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->connection_object, $function_name), $arguments);
    }
    
    /**
     * Connects the module to the remote caching resource.     
     *
     * @return object The connection object instance.
     */
    public function connect(array $configuration = array()) {
        if(empty($configuration['host']) && empty($configuration['port']) && empty($configuration['socket'])) {    
            throw new Exception("Connection configuration must either specify a host and port or a socket.");
        }
    }
    
    /**
     * Returns the object used to directly interact with the cache.
     * This is for operations that require references to be passed to connection object function functions.     
     *
     * @return object The connection object instance.
     */
    public function getConnectionObject() {
        return $this->connection_object;
    }
    
    /**
     * Retrieves the host used to connect to this cache instance.
     *
     * @return string
     */
    public function getHost() {
        if(empty($this->connection_configuration['host'])) {
            throw new Exception('This cache connection does not have a host option.');
        }
        
        return $this->connection_configuration['host'];
    }
    
    /**
     * Retrieves the port used to connect to this cache instance.
     *
     * @return string
     */
    public function getPort() {
        if(empty($this->connection_configuration['port'])) {
            throw new Exception('This cache connection does not have a port option.');
        }
        
        return $this->connection_configuration['port'];
    }
    
    /**
     * Sets a variable value in the cache.
     *
     * @param string $value_key The name of the variable to cache.
     * @param mixed $value The value of the variable to cache.
     * @param string $value_category (optional) The category group name this variable belongs to.
     * @param integer $cache_time (optional) The lifetime of the cached variable.     
     * @return void
     */
    public function set($value_key, $value, $value_category = '', $cache_time = NULL) {                           
        return $this->connection_object->set("{$value_category}_{$value_key}", $value, $cache_time);
    }
    
    /**
     * Sets multiple variable values via the cache instance.
     *
     * @param array $values The values to cache. Format is value_name => value.
     * @param string $value_category (optional) The category group name the variables belong to.
     * @param integer $cache_time (optional) The lifetime of the cached variables.     
     * @return void
     */
    public function setMultiple(array $values, $value_category = '', $cache_time = NULL) {    
        if(!empty($values)) {
            foreach($values as $value_key => $value) {
                $this->set($value_key, $value, $value_category, $cache_time);
            }
        }
    }
    
    /**
     * Retrieves a cached variable value from the cache instance.
     *
     * @param string $key The name of the variable in the cache.
     * @param string $value_category (optional) The category group name this variable belongs to.
     * @return mixed
     */
    public function get($value_key, $value_category = '') {
        $cache_entry_name = $value_key;
        
        if(!empty($value_category)) {
            $cache_entry_name = "{$value_category}_{$value_key}";
        }
    
        return $this->connection_object->get($cache_entry_name);
    }
    
    /**
     * Retrieves several cached variable values from the cache instance.
     *
     * @param string $keys The names of the variables in the cache.
     * @param string $value_category (optional) The category group name these variables belong to.
     * @return mixed
     */
    public function getMultiple(array $keys, $value_category = '') {    
        $cache_entry_name = $value_key;
        
        if(!empty($value_category)) {
            $cache_entry_name = "{$value_category}_{$value_key}";
        }
        
        $retrieved_values = array();
        
        if(!empty($keys)) {
            foreach($keys as $key) {            
                $retrieved_values[$key] = $this->get($key, $value_category);
            }
        }
    
        return $retrieved_values;
    }
    
    /**
     * Clears all stored values via the connection object.
     *
     * @return void
     */
    public function clear() {}
}