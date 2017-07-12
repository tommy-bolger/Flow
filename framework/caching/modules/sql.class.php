<?php
/**
* The SQL module for the framework cache abstraction layer.
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
namespace Framework\Caching\Modules;

use \Exception;
use \DateTime;
use \DateInterval;
use \Framework\Data\Database\QueryGenerator;

class SQL
extends Module {   
    /**
    * @var boolean Indicates if this instance is in the middle of a transaction.
    */ 
    protected $in_transaction = false;

    /**
     * Catches all function calls not present in this class and passes them to the connection object. Not used for this module.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        throw new Exception("Function '{$function_name}' is not a valid function in this module.");
    }

    /**
     * Connects the module to the remote caching resource. Not used for this module. 
     *
     * @return void
     */
    public function connect(array $configuration = array()) {
        $this->connection_object = db();
    }
    
    /**
     * Generates and returns the timestamp that a variable will expire on.
     *
     * @param integer $expire_time (optional) The lifetime of the cached variable in seconds. Defaults to NULL to not expire.   
     * @return string|null
     */
    protected function getExpiresTimestamp($expire_time = NULL) {
        $expires = NULL;
    
        if(is_integer($expire_time)) {
            $current_time = new DateTime();
            
            $current_time->add(new DateInterval("PT{$expire_time}S"));
            
            $expires = $current_time->format('Y-m-d H:i:s');
        }
    
        return $expires;
    }
   
    /**
     * Sets a variable value in the cache instance.
     *
     * @param string $key The name of the variable to cache.
     * @param mixed $value The value of the variable to cache.
     * @param string $category (optional) The category that this key falls under. Defaults to an empty string.
     * @param integer $expire_time (optional) The lifetime of the cached variable in seconds. Defaults to NULL to not expire.   
     * @return mixed
     */
    public function set($key, $value, $category = '', $expire_time = NULL) {
        if(!isset($category)) {
            $category = '';
        }
        
        if(!$this->in_transaction) {
            $this->connection_object->beginTransaction();
        }
    
        $this->connection_object->delete('cms_cache', array(
            'category' => $category,
            'key' => $key
        ));

        $this->connection_object->insert('cms_cache', array(
            'category' => $category,
            'key' => $key,
            'value' => $value,
            'expires' => $this->getExpiresTimestamp($expire_time)
        ), '', false);
        
        if(!$this->in_transaction) {
            $this->connection_object->commit();
        }
    }
    
    /**
     * Sets multiple variable values via the cache instance.
     *
     * @param array $values The values to cache. Format is key => value.
     * @param string $category (optional) The category group name the variables belong to. Defaults to an empty string.
     * @param integer $cache_time (optional) The lifetime of the cached variables. 
     * @return void
     */
    public function setMultiple(array $values, $category = '', $expire_time = NULL) {    
        if(!empty($values)) {
            if(!isset($category)) {
                $category = '';
            }
        
            $expires = $this->getExpiresTimestamp($expire_time);
            
            $keys = array();
            $records_to_insert = array();
        
            foreach($values as $key => $value) {
                $keys[] = $key;
            
                $records_to_insert[] = array(
                    'category' => $category,
                    'key' => $key,
                    'value' => $value,
                    'expires' => $expires
                );
            }
            
            $delete_placeholder_values = array(
                $category
            );
        
            $database_driver_name = $this->connection_object->getDriverName();
            
            $delete_key_placeholder = '';
            
            if($database_driver_name == 'pgsql') {
                $delete_key_placeholder = '= ANY(?::varchar[])';
                
                $delete_placeholder_values[] = "{" . implode(",", $keys) . "}";
            }
            else {
                $delete_key_placeholder = QueryGenerator::getInStatement(count($keys));
            
                $delete_placeholder_values = array_merge($delete_placeholder_values, $keys);
            }
            
            if(!$this->in_transaction) {
                $this->connection_object->beginTransaction();
            }
            
            $this->connection_object->query("
                DELETE FROM cms_cache
                WHERE category = ?
                    AND key {$delete_key_placeholder}
            ", $delete_placeholder_values);
            
            $this->connection_object->insertMulti('cms_cache', $records_to_insert);
            
            if(!$this->in_transaction) {
                $this->connection_object->commit();
            }
        }
    }
    
    /**
     * Retrieves a cached variable value from the cache instance.
     *
     * @param string $key The name of the variable in the cache.
     * @param string $category (optional) The category for this key. Defaults to an empty string.
     * @return mixed
     */
    public function get($key, $category = '') {
        if(!isset($category)) {
            $category = '';
        }
    
        if(!$this->in_transaction) {
            $this->connection_object->beginTransaction();
        }
        
        $value = $this->connection_object->getOne("
            SELECT value
            FROM cms_cache
            WHERE category = :category
                AND key = :key
                AND (
                    expires IS NULL
                    OR expires > :expires
                )
        ", array(
            ':category' => $category,
            ':key' => $key,
            ':expires' => date('Y-m-d H:i:s')
        ));
        
        if(!$this->in_transaction) {
            $this->connection_object->commit();
        }
        
        return $value;
    }
    
    /**
     * Retrieves several cached variable values from the cache instance.
     *
     * @param string $keys The names of the variables in the cache.
     * @param string $category (optional) The category group name these variables belong to. Defaults to an empty string.
     * @return mixed
     */
    public function getMultiple(array $keys, $category = '') {   
        $values = array();
    
        if(!empty($keys)) {
            if(!isset($category)) {
                $category = '';
            }
        
            $placeholder_values = array(
                $category
            );
        
            $database_driver_name = $this->connection_object->getDriverName();

            $key_placeholder = '';
            
            if($database_driver_name == 'pgsql') {
                $key_placeholder = '= ANY(?::varchar[])';
                
                $placeholder_values[] = "{" . implode(",", $keys) . "}";
            }
            else {
                $key_placeholder = QueryGenerator::getInStatement(count($keys));
            
                $placeholder_values = array_merge($placeholder_values, $keys);
            }
            
            $placeholder_values[] = date('Y-m-d H:i:s');
        
            if(!$this->in_transaction) {
                $this->connection_object->beginTransaction();
            }
        
            $values = $this->connection_object->getMappedColumn("
                SELECT 
                    key,
                    value
                FROM cms_cache
                WHERE category = ?
                    AND key {$key_placeholder}
                    AND (
                        expires IS NULL
                        OR expires > ?
                    )
            ", $placeholder_values);
            
            if(!$this->in_transaction) {
                $this->connection_object->commit();
            }
        }
        
        return $values;
    }
    
    /**
     * Initializes a new transaction.
     *
     * @return object This instance.
     */
    public function transaction() {
        if(!$this->connection_object->inTransaction()) {
            $this->connection_object->beginTransaction();
        }
        
        $this->in_transaction = true;
        
        return $this;
    }
    
    public function commit() {
        $this->connection_object->commit();
        
        $this->in_transaction = false;
    }
    
    /**
     * Clears all stored keys in the cache instance.
     *
     * @return void
     */
    public function clear() {
        if(!$this->in_transaction) {
            $this->connection_object->beginTransaction();
        }
    
        $this->connection_object->delete('cms_cache');
        
        if(!$this->in_transaction) {
            $this->connection_object->commit();
        }
    }
}