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
    * @var string The default module name to use when calling file_cache() with no arguments. 
    */
    private static $default_module_name;

    /**
    * @var array The list of all instances.
    */
    private static $instances;

    /**
    * @var string The base path to the cache directory.
    */    
    private $directory_path;
    
    /**
     * Sets the default module name to use when calling file_cache() with no arguments.
     *
     * @return void
     */
    public static function setDefaultModuleName($module_name) {
        self::$default_module_name = $module_name;
    }
    
    /**
     * Retrieves the instance of the specified object.
     *
     * @return object
     */
    public static function getFileCache($module_name = '') {
        if(empty($module_name)) {
            $module_name = self::$default_module_name;
        }
    
        if(!isset(self::$instances[$module_name])) {
            self::$instances[$module_name] = new file($module_name);
        }
        
        return self::$instances[$module_name];
    }
    
    /**
     * Initializes this instance of FileCache.
     *
     * @param string $base_path (optional) The base path to the file cache directory.
     * @return void
     */
    public function __construct($module_name) {
        assert('!empty($module_name) && is_string($module_name)');
        
        $this->directory_path = framework()->installation_path;
    
        if($module_name != 'framework') {
            $this->directory_path .= "/modules/{$module_name}";
        }
        
        $this->directory_path .= '/cache';             
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
     * Gets the hashed key of the cached file name.
     *
     * @param string $key The name of the cache file.
     * @param string $file_path The directory path of the cached file within the cache directory path.
     * @return string
     */
    private function getHashedKey($key, $file_path) {    
        return md5($key . $file_path . config('framework')->version);
    }
    
    /**
     * Retrieves the full path of the cache file.
     *
     * @param string $file_path The directory path of the cache file within the cache directory path.
     * @return string
     */
    private function getFullDirectoryPath($file_path) {
        $file_path = rtrim($file_path, '/');
        
        return "{$this->directory_path}/{$file_path}";
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

        if(is_readable("{$full_directory_path}/{$hashed_key}.{$extension}")) {
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
            $full_file_path = "{$full_directory_path}/{$hashed_key}.{$extension}";
        
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
            $full_file_path = "{$full_directory_path}/{$hashed_key}.{$extension}";
        
            if(is_file($full_file_path)) {
                return file_get_contents($full_file_path);
            }
        }
        else {
            throw new \Exception("Directory '{$full_directory_path}' is not readable.");
        }
        
        return "";
    }
    
    /**
     * Clears all cached files.
     *
     * @return void
     */
    public function clear() {
        $cache_directories = scandir($this->directory_path);
        
        foreach($cache_directories as $cache_directory) {
            $cache_directory_path = "{$this->directory_path}/{$cache_directory}";
        
            if(is_dir($cache_directory_path) && $cache_directory != '.' && $cache_directory != '..') {
                $cached_files = scandir($cache_directory_path);
                
                if(!empty($cached_files)) {
                    foreach($cached_files as $cached_file) {
                        if($cached_file != 'index.html' && strpos($cached_file, 'pie.') === false) {
                            $cache_file_path = "{$cache_directory_path}/{$cached_file}";
                            
                            if(is_file($cache_file_path)) {
                                unlink($cache_file_path);
                            }
                        }
                    }
                }
            }
        }
    }
}