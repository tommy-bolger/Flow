<?php
/**
* The framework memory cache abstraction layer.
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
namespace Framework\Caching;

class Cache {
    /**
    * @var array The configuration of the cache object.
    */  
    private static $configuration = array(
        'default_cache_time' => 2592000,
        'cache_object_names' => array(
            'apc' => '\Framework\Caching\Modules\APC',
            'memcached' => '\Framework\Caching\Modules\Memcached',
            'redis' => '\Framework\Caching\Modules\Redis',
        )
    );
    
    /**
    * @var string The key of the cache module to utilize in the configuration.
    */
    private static $cache_object_name = 'redis';

    /**
    * @var object The instance of this object.
    */
    private static $instance;
    
    /**
    * @var object The instance of the loaded cache module.
    */
    private $cache_object;
    
    /**
    * @var object The default time that a cached variable is stored in the cache.
    */
    private $default_cache_time;

    /**
     * Retrieves the current instance of this object.
     *
     * @return object
     */
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new cache();
        }
        
        return self::$instance;
    }
    
    /**
     * Initializes this instance of the Cache object. 
     *
     * @return void
     */
    public function __construct() {
        $cache_object_name = self::$configuration['cache_object_names'][self::$cache_object_name];
    
        $this->cache_object = new $cache_object_name();
        
        $this->default_cache_time = self::$configuration['default_cache_time'];
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the loaded cache module.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->cache_object, $function_name), $arguments);
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
        if(empty($cache_time) && !empty($value_category)) {
            $category_cache_time_name = "{$value_category}_cache_time";
        
            if(isset(self::$configuration[$category_cache_time_name])) {
                $cache_time = self::$configuration[$category_cache_time_name];
            }
        }
        
        if(empty($cache_time)) {
            $cache_time = $this->default_cache_time;
        }
        
        $set_success = $this->cache_object->set("{$value_category}_{$value_key}", $value, $cache_time);

        if($set_success === false) {
            throw new \Exception("Could not set variable '{$value_key}' in the cache.");
        }
    }
    
    /**
     * Sets multiple variable values in the cache.
     *
     * @param array $values The values to cache. Format is value_name => value.
     * @param string $value_category (optional) The category group name the variables belong to.
     * @param integer $cache_time (optional) The lifetime of the cached variables.     
     * @return void
     */
    public function setMultiple($values, $value_category = '', $cache_time = NULL) {    
        if(!empty($values)) {
            foreach($values as $value_key => $value) {
                $this->set($value_key, $value, $value_category, $cache_time);
            }
        }
    }
    
    /**
     * Retrieves a cached variable value from the cache.
     *
     * @param string $key The name of the variable in the cache.
     * @param string $value_category (optional) The category group name this variable belongs to.
     * @return mixed
     */
    public function get($value_key, $value_category = '') {
        return $this->cache_object->get("{$value_category}_{$value_key}");
    }
    
    /**
     * Clears all stored values in cache.
     *
     * @return void
     */
    public function clear() {
        $this->cache_object->clear();
    }
}