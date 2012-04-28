<?php
/**
* The framework file cache abstraction layer.
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

class File {
    /**
    * @var object The instance of this object.
    */
    private static $instance;

    /**
    * @var array A temporary cache of hashed file keys to avoid calling md5 more than once per key.
    */  
    private static $hashed_keys = array();

    /**
    * @var string The base path to the cache directory.
    */    
    private $cache_directory_path;
    
    /**
     * Retrieves the current instance of this object.
     *
     * @return object
     */
    public static function getFileCache() {
        if(!isset(self::$instance)) {
            self::$instance = new file();
        }
        
        return self::$instance;
    }
    
    /**
     * Initializes this instance of FileCache.
     *
     * @param string $cache_directory_path (optional) The path to the file cache directory.
     * @return void
     */
    public function __construct($cache_directory_path = '') {
        if(empty($cache_directory_path)) {
            $this->cache_directory_path = framework()->installation_path . '/cache/';
        }
        else {
            $this->cache_directory_path = $cache_directory_path;
        }
    }
    
    /**
     * Catches all function calls not present in this class and throws an exception to avoid a fatal error.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        throw new \Exception("Function '{$function_name}' does not exist in this class.");
    }
    
    /**
     * Retrieves the file cache directory path.
     *
     * @return string
     */
    public function getCacheDirectoryPath() {
        return $this->cache_directory_path;
    }
    
    /**
     * Gets the hashed key of the cached file name.
     *
     * @param string $key The name of the cache file.
     * @param string $file_path The directory path of the cached file within the cache directory path.
     * @return string
     */
    private function getHashedKey($key, $file_path) {    
        $full_key = $key . $file_path . config('framework')->version;
        
        $hashed_key = '';
        
        if(isset(self::$hashed_keys[$full_key])) {
            $hashed_key = self::$hashed_keys[$full_key];
        }
        else {
            $hashed_key = md5($full_key);
            
            self::$hashed_keys[$full_key] = $hashed_key;
        }
        
        return $hashed_key;
    }
    
    /**
     * Retrieves the full path of the cache file.
     *
     * @param string $file_path The directory path of the cache file within the cache directory path.
     * @return string
     */
    private function getFullDirectoryPath($file_path) {
        $file_path = rtrim($file_path, '/');
        
        return "{$this->cache_directory_path}/{$file_path}";
    }
    
    /**
     * Checks for if a cache file exists in the file system.
     *
     * @param string $key The name of the cache file.     
     * @param string $file_path The directory path of the cache file within the cache directory path.
     * @param string $extension (optional) The file extension of the cache file. Defaults to 'txt'.
     * @return string|boolean The hashed key if exists or false if it doesn't exist.
     */
    public function exists($key, $file_path, $extension = 'txt') {
        $hashed_key = $this->getHashedKey($key, $file_path);
        
        $full_directory_path = $this->getFullDirectoryPath($file_path);

        if(is_readable("{$full_directory_path}{$hashed_key}.{$extension}")) {
            return $hashed_key;
        }
        
        return false;
    }
    
    /**
     * Adds a value to a file cache.
     *
     * @param string $key The name of the cache file.     
     * @param string $value The contents of the cache file.     
     * @param string $file_path The directory path of the cache file within the cache directory path.
     * @param string $extension (optional) The file extension of the cache file. Defaults to 'txt'.
     * @return string The hashed key of the stored file.
     */
    public function set($key, $value, $file_path, $extension = 'txt') {    
        $hashed_key = $this->getHashedKey($key, $file_path);
        
        $full_directory_path = $this->getFullDirectoryPath($file_path);
        
        if(is_writable($full_directory_path)) {
            $full_file_path = "{$full_directory_path}{$hashed_key}.{$extension}";
        
            if(!is_file($full_file_path)) {
                file_put_contents($full_file_path, $value);
            }
        }
        else {
            throw new \Exception("Directory '{$full_directory_path}' is not writable.");
        }
        
        return $hashed_key;
    }
    
    /**
     * Retrieves the contents of a cached file.
     *
     * @param string $key The name of the cache file.     
     * @param string $file_path The directory path of the cache file within the cache directory path.
     * @param string $extension (optional) The file extension of the cache file. Defaults to 'txt'.
     * @return string Returns empty string of the cached file doesn't exist.
     */
    public function get($key, $file_path, $extension = 'txt') {
        $hashed_key = $this->getHashedKey($key, $file_path);
        
        $full_directory_path = $this->getFullDirectoryPath($file_path);
        
        if(is_readable($full_directory_path)) {
            $full_file_path = "{$full_directory_path}{$hashed_key}.{$extension}";
        
            if(is_file($full_file_path)) {
                return file_get_contents($full_file_path);
            }
        }
        else {
            throw new \Exception("Directory '{$full_directory_path}' is not readable.");
        }
        
        return "";
    }
}