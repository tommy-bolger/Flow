<?php
/**
* A collection of low-level shortcut functions utilized by the framework.
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
* A shortcut function to generate a PHP user error.
*
* @param string $error_message
* @return void
*/
function throwError($error_message) {
    trigger_error($error_message, E_USER_ERROR);   
}

/**
* A shortcut function to the SquConfiguration::getConfiguration function.
*
* @param string $config_name (optional) The name of the configuration instance to retrieve.
* @return object
*/
function config($config_name = NULL) {
    return Configuration::getConfiguration($config_name);
}

/**
* A shortcut function to the SquDatabase::getDatabase function.
*
* @param string $database_connection_name (optional) The name of the database instance to retrieve.
* @return object
*/
function db($database_connection_name = NULL) {
    return Database::getDatabase($database_connection_name);
}

/**
* A shortcut function to the Request::getDatabase function.
*
* @return object
*/
function request() {
    return Request::getRequest();
}

/**
* A shortcut to the Session object instance.
*
* @return object
*/
function session() {
    return Session::getSession();
}

/**
* A shortcut to the Cache object instance.
*
* @return object
*/
function cache() {
    return Cache::getCache();
}

/**
* A shortcut to the FileCache object instance.
*
* @return object
*/
function file_cache() {
    return FileCache::getFileCache();
}

/**
* A shortcut to the Page object instance.
*
* @return object
*/
function page() {
    return Page::getPage();
}

/**
* A shortcut function to the Debug::dump function.
*
* @param mixed $data The data to dump.
* @return void
*/
function dump($data) {
    if(Framework::getEnvironment() != 'production') {
       Debug::dump($data);
    }
}