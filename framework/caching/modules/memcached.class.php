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

class Memcached
extends Module {    
    /**
     * Initializes this instance of MemcachedModule.
     *
     * @return void
     */
    public function __construct() {
        $this->connection_object = new Memcached();
    }
    
    /**
     * Connects the module to the remote caching resource.     
     *
     * @return object The connection object instance.
     */
    public function connect(array $configuration = array()) {
        parent::connect($configuration);
        
        $this->connection_object = new Memcached();
        
        $this->connection_object->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->connection_object->setOption(Memcached::OPT_COMPRESSION, true);
        
        if(extension_loaded("igbinary")) {
            $this->connection_object->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
        }
        
        $this->connection_object->addServer($configuration['host'], $configuration['port']);
    }
    
    /**
     * Clears all stored values in  memcached.
     *
     * @return void
     */
    public function clear() {
        $this->connection_object->flush();
    }
}