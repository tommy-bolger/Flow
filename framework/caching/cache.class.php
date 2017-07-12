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

use \Exception;

class Cache { 
    /**
    * @const integer The default amount of time a vaue is stored in cache before it expires.
    */    
    const DEFAULT_CACHE_TIME = 2592000;
     
    /**
    * @var array The list of all possible cache module names and their namespace to include.
    */
    protected static $cache_modules = array(
        'apc' => '\Framework\Caching\Modules\APC',
        'memcached' => '\Framework\Caching\Modules\Memcached',
        'redis' => '\Framework\Caching\Modules\Redis\Redis',
        'sql' => '\Framework\Caching\Modules\SQL'
    );
    
    /**
    * @var array All cache connections specified in cache_configurations.php located in the protected folder.
    */
    protected static $connections;
    
    /**
    * @var object The instance of this object.
    */
    protected static $instances = array();
    
    /**
    * @var boolean Indicates if the instance is initalized with a correct connection to the cache source.
    */
    protected $initialized = false;
    
    /**
    * @var string The name of the connection available in the cache_connections configuration file in protected/.
    */
    protected $connection_name;
    
    /**
    * @var object The instance of the loaded cache module.
    */
    protected $cache_module;

    /**
     * Retrieves the current instance of this object.
     *
     * @return object
     */
    public static function getInstance($connection_name = '', $new_connection = false) {
        if(empty($connection_name)) {
            $connection_name = 'default';
        }
        
        $instance = NULL;
    
        if(!isset(self::$instances[$connection_name]) || $new_connection) {
            if(empty(self::$connections)) {
                self::$connections = (require_once(dirname(dirname(__DIR__)) . '/protected/cache_connections.php'));
            }
            
            if(!empty(self::$connections[$connection_name])) {
                $connection_configuration = self::$connections[$connection_name];

                if(is_array($connection_configuration)) {
                    $instance = new Cache($connection_name);
                    $instance->connect();
                
                    if(!isset(self::$instances[$connection_name])) {
                        self::$instances[$connection_name] = $instance;
                    }
                }
                else {
                    if(!isset(self::$instances[$connection_name])) {
                        $instance = self::getInstance($connection_configuration, $new_connection);
                    
                        self::$instances[$connection_name] = $instance;
                    }
                }
            }
            else {                
                $instance = new Cache($connection_name);
            
                if(!isset(self::$instances[$connection_name])) {
                    self::$instances[$connection_name] = $instance;
                }
            }
        }
        else {
            $instance = self::$instances[$connection_name];
        }
        
        return $instance;
    }
    
    /**
     * Initializes this instance of the Cache object. 
     *
     * @param string $connection_name The name of the connection to use with this instance.  
     * @return void
     */
    public function __construct($connection_name) {
        $this->connection_name = $connection_name;
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the loaded cache module.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->cache_module, $function_name), $arguments);
    }
    
    /**
     * Connects to this instance's module.
     * 
     * @return void
     */
    public function connect() {
        if(empty(self::$connections[$this->connection_name])) {
            throw new Exception("Specified connection name '{$this->connection_name}' does not exist in cache_connections.php.");
        }
        
        $connection_configuration = self::$connections[$this->connection_name];
        
        if(empty($connection_configuration['module'])) {
            throw new Exception("Specified connection name '{$this->connection_name}' does not specify a module in cache_connections.php.");
        }
        
        $module_name = $connection_configuration['module'];
        
        if(empty(self::$cache_modules[$module_name])) {
            throw new Exception("Specified module '{$module_name}' for connection name '{$this->connection_name}' in cache_connections.php is not valid.");
        }
        
        $connection_configuration_options = array();
        
        if(!empty($connection_configuration['options'])) {
            $connection_configuration_options = $connection_configuration['options'];
        }
        
        $cache_module = self::$cache_modules[$module_name];
        
        $this->cache_module = new $cache_module();
        
        $this->cache_module->connect($connection_configuration_options);
        
        $this->initialized = true;
    }
    
    /**
     * Indicates if the current instance is intialized with a connection.
     * 
     * @return boolean
     */
    public function initialized() {
        return $this->initialized;
    }

    /**
     * Retrieves the name of this connection as set in the cache_connections configurations.
     *
     * @return void
     */
    public function getConnectionName() {
        return $this->connection_name;
    }
}