<?php
/**
* A collection of low-level shortcut functions utilized by the framework.
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

use \Framework\Core\Framework;

/**
* Retrieves a PHP environment variable value.
*
* @param string $environment_variable The name of the variable value.
* @return mixed
*/
function environment($environment_variable) {
    if(isset($_SERVER[$environment_variable])) {
        return $_SERVER[$environment_variable];
    }

    if(isset($_ENV[$environment_variable])) {
        return $_ENV[$environment_variable];
    }

    if(function_exists("getenv")) {
        return getenv($environment_variable);
    }

    return false;
}

/**
* A shortcut function to the SquConfiguration::getConfiguration function.
*
* @param string $config_name (optional) The name of the configuration instance to retrieve.
* @return object
*/
function config($config_name = NULL) {
    return \Framework\Core\Configuration::getConfiguration($config_name);
}

/**
* A shortcut function to the Database::getDatabase function.
*
* @param string $database_connection_name (optional) The name of the database instance to retrieve.
* @return object
*/
function db($database_connection_name = NULL) {
    return \Framework\Data\Database::getDatabase($database_connection_name);
}

/**
* A shortcut function to the Request::getDatabase function.
*
* @return object
*/
function request() {
    return \Framework\Request\Request::getRequest();
}

/**
* A shortcut to the Session object instance.
*
* @return object
*/
function session() {
    return \Framework\Session\Session::getSession();
}

/**
* A shortcut to the Cache object instance.
*
* @return object
*/
function cache() {
    return \Framework\Caching\Cache::getCache();
}

/**
* A shortcut to the FileCache object instance.
*
* @return object
*/
function file_cache($module_name = '') {
    return \Framework\Caching\File::getFileCache($module_name);
}

/**
* A shortcut to the Page object instance.
*
* @return object
*/
function page() {
    return \Framework\Html\Page::getPage();
}

/**
* A shortcut function to the Debug::dump function.
*
* @param mixed $data The data to dump.
* @return void
*/
function dump($data) {
    if(Framework::$environment != 'production') {
        \Framework\Debug\Debug::dump($data);
    }
}