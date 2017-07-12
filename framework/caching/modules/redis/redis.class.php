<?php
/**
* The redis module of the framework cache abstraction layer.
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
namespace Framework\Caching\Modules\Redis;

use \Redis as RedisClient;
use \Framework\Caching\Modules\Module;

class Redis
extends Module {            
    /**
     * Initializes this instance of RedisModule.
     *
     * @return void
     */
    public function __construct() {
        $this->connection_object = new RedisClient();
    }
    
    /**
     * Connects the module to the remote caching resource.     
     *
     * @return object The connection object instance.
     */
    public function connect(array $configuration = array()) {
        parent::connect($configuration);
        
        $this->connection_configuration = $configuration;
        
        if(!empty($configuration['host'])) {
            $this->connection_object->connect($configuration['host'], $configuration['port']);
        }
        elseif(!empty($configuration['socket'])) {
            $this->connection_object->connect($configuration['socket']);
        }
        
        $this->connection_object->setOption(RedisClient::OPT_SCAN, RedisClient::SCAN_RETRY);
        
        $this->connection_object->setOption(RedisClient::OPT_SERIALIZER, RedisClient::SERIALIZER_NONE);
        
        if(!empty($configuration['database'])) {
            $this->connection_object->select($configuration['database']);
        }
    }
    
    /**
     * Sets a variable value in the cache.
     *
     * @param string $value_key The name of the variable to cache.
     * @param mixed $value The value of the variable to cache.
     * @param string $value_category (optional) The category group name this variable belongs to.
     * @param integer $cache_time (optional) The lifetime of the cached variable. Ignored when value_category is specified.    
     * @return void
     */
    public function set($value_key, $value, $value_category = '', $cache_time = NULL) {
        $set_success = false;
        
        if(!empty($value_category)) {
            $set_success = $this->connection_object->hSet($value_category, $value_key, $value);
        }
        else {        
            $set_success = $this->connection_object->set($value_key, $value);
            
            if(!empty($cache_time)) {
                $this->connection_object->setTimeout($value_key, $cache_time);
            }
        }
    
        return $set_success;
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
            if(!empty($value_category)) {
                $this->connection_object->hMSet($value_category, $values);
            }
            else {
                $this->connection_object->mSet($values);
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
        $value = NULL;
        
        if(!empty($value_category)) {            
            $value = $this->connection_object->hGet($value_category, $value_key);
        }
        else {
            $value = $this->connection_object->get($value_key);
        }
    
        return $value;
    }
    
    /**
     * Retrieves several cached variable values from the cache instance.
     *
     * @param string $keys The name of the variable in the cache.
     * @param string $value_category (optional) The category group name this variable belongs to.
     * @return mixed
     */
    public function getMultiple(array $keys, $value_category = '') {
        $retrieved_values = array();
    
        if(!empty($value_category)) {
            $retrieved_values = $this->connection_object->hMGet($value_category, $keys);
        }
        else {
            $retrieved_values = $this->connection_object->mGet($keys);
        }
    
        return $retrieved_values;
    }
    
    /**
     * Clears all stored values in redis.
     *
     * @return void
     */
    public function clear() {
        $this->connection_object->flushAll();
    }
    
    /**
     * Initializes the transaction object.
     *
     * @return void
     */
    public function transaction() {
        return new Transaction($this->connection_object);
    }
    
    /**
     * Executes HGETALL on multiple hash record names and returns the records as an array.
     *
     * @param array $hash_record_names The names of the records to execute HGETALL on.
     * @return array The retrieved hash records.
     */
    public function hGetAllMulti(array $hash_record_names) {
        $hash_record_values = array();
    
        if(!empty($hash_record_names)) {
            $transaction = $this->connection_object->multi(RedisClient::PIPELINE);
            
            foreach($hash_record_names as $hash_record_name) {
                $transaction->hGetAll($hash_record_name);
            }
            
            $hash_record_values = $transaction->exec();
        }
        
        return $hash_record_values;
    }
    
    /**
     * Executes LRANGE 0 -1 on multiple list records and returns the records as an array.
     *
     * @param array $record_names The names of the records to execute LRANGE on.
     * @return array The retrieved records.
     */
    public function lRangeMulti(array $record_names) {
        $record_values = array();
    
        if(!empty($record_names)) {
            $transaction = $this->connection_object->multi(RedisClient::PIPELINE);

            foreach($record_names as $record_name) {
                $transaction->lRange($record_name, 0, -1);
            }
            
            $record_values = $transaction->exec();
        }
        
        return $record_values;
    }
    
    /**
     * Executes the EVAL command on Redis.
     *
     * @param string $lua_script The LUA script to run.
     * @param array $arguments (optional) The arguments to pass into the script.
     * @return void
     */
    public function reval($lua_script, array $arguments = array()) {
        $current_read_timeout = $this->connection_object->getOption(RedisClient::OPT_READ_TIMEOUT);
        
        //Set a temporary read timeout to make PHP wait while the Lua script executes
        $this->connection_object->setOption(RedisClient::OPT_READ_TIMEOUT, 360);
        
        $this->connection_object->eval($lua_script, $arguments);
        
        //Reset the read timeout to its previous value
        $this->connection_object->setOption(RedisClient::OPT_READ_TIMEOUT, $current_read_timeout);
    }
}