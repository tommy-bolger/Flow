<?php
/**
* The APC module for the framework cache abstraction layer.
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

use \Exception();

class APC
extends Module {     
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
    public function connect(array $configuration = array()) {}
   
    /**
     * Sets a variable value in APC.
     *
     * @param string $key The name of the variable to cache.
     * @param mixed $value The value of the variable to cache.
     * @param integer $expire_time The lifetime of the cached variable.     
     * @return mixed
     */
    public function set($key, $value, $expire_time) {
        return apc_store($key, $value, $expire_time);
    }
    
    /**
     * Retrieves a cached variable value from APC.
     *
     * @param string $key The name of the variable in the cache.
     * @return mixed
     */
    public function get($key) {
        return apc_fetch($key);
    }
    
    /**
     * Clears the APC user cache.
     *
     * @return void
     */
    public function clear() {
        apc_clear_cache('user');
    }
}