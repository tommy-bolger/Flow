<?php
/**
* The memcached module of the framework cache abstraction layer.
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

class Memcached {
    /**
    * @var array The memcache servers this module connects to.
    */ 
    private static $servers = array(
        'web_server' => array(
            'host' => 'localhost',
            'port' => 11211
        )
    );
    
    /**
    * @var object The instance of the memcached library this module utilizes.
    */ 
    private $memcached_object;
    
    /**
     * Initializes this instance of MemcachedModule.
     *
     * @return void
     */
    public function __construct() {
        $this->memcached_object = new Memcached();
        
        $this->memcached_object->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->memcached_object->setOption(Memcached::OPT_COMPRESSION, true);
        
        if(extension_loaded("igbinary")) {
            $this->memcached_object->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
        }
        
        $this->memcached_object->addServers(self::$servers);
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the memcached library object.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->memcached_object, $function_name), $arguments);
    }
    
    /**
     * Clears all stored values in  memcached.
     *
     * @return void
     */
    public function clear() {
        $this->memcached_object->flush();
    }
}