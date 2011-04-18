<?php
/**
* The framework memory cache abstraction layer.
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
class Cache {
    /**
    * @var array The configuration of the cache object.
    */  
    private static $configuration = array(
        'default_cache_time' => 2592000,
        'cache_object_names' => array(
            'apc' => 'APCModule',
            'memcached' => 'MemcachedModule'
        )
    );
    
    /**
    * @var string The key of the cache module to utilize in the configuration.
    */
    private static $cache_object_name = 'apc';

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
    public static function getCache() {
        if(!isset(self::$instance)) {
            self::$instance = new Cache();
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
        
        require_once("{$cache_object_name}.class.php");
    
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
            trigger_error("Could not set variable '{$value_key}' in the cache.");
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
}