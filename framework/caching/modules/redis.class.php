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
namespace Framework\Caching\Modules;

use \Redis as RedisClient;

class Redis {
    /**
    * @var array The memcache servers this module connects to.
    */ 
    protected static $server = array(
        'host' => '127.0.0.1',
        'port' => 6379
    );
    
    /**
    * @var object The instance of the redis library this module utilizes.
    */ 
    protected $redis_object;
    
    /**
     * Initializes this instance of RedisModule.
     *
     * @return void
     */
    public function __construct() {
        $this->redis_object = new RedisClient();
        
        $this->redis_object->pconnect(self::$server['host'], self::$server['port']);
        
        $this->redis_object->setOption(RedisClient::OPT_SCAN, RedisClient::SCAN_RETRY);
        
        if(extension_loaded("igbinary")) {
            $this->redis_object->setOption(RedisClient::OPT_SERIALIZER, RedisClient::SERIALIZER_IGBINARY);
        }
        else {
            $this->redis_object->setOption(RedisClient::OPT_SERIALIZER, RedisClient::SERIALIZER_PHP);
        }
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the redis library object.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->redis_object, $function_name), $arguments);
    }
    
    /**
     * Returns the object used to directly interact with redis.
     * This is for operations that require references to be passed to RedisClient functions, such as scan.     
     *
     * @return RedisClient the RedisClient instance.
     */
    public function getRedisObject() {
        return $this->redis_object;
    }
    
    /**
     * Clears all stored values in  redis.
     *
     * @return void
     */
    public function clear() {
        $this->redis_object->flushAll();
    }
}