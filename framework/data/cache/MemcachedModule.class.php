<?php
/**
* The memcached module of the framework cache abstraction layer.
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
class MemcachedModule {
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
}