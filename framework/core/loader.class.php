<?php
/**
* Loads external libraries.
* Copyright (c) 2013, Tommy Bolger
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
namespace Framework\Core;

use \Exception;

class Loader {
    /**
    * @var array The list of available base paths to search in.
    */
    protected static $base_paths = array();
    
    /**
    * @var array The list of external files already loaded.
    */
    protected static $loaded = array();

    /**
     * Adds a base path to the loader.
     *       
     * @param string $base_path The base file path to add.
     * @return void
     */
    public static function addBasePath($base_path) {    
        self::$base_paths[] = rtrim($base_path, '/');
    }
    
    /**
     * Loads an external file from the list of available base paths.
     *    
     * @param string $file_path The path to the file.
     * @param boolean $relative Indicates if the file path is relative inside of one of the base paths.     
     * @param boolean $load_all_verions Indicates if only the first matched file should be loaded (true), or if all matched files should be loaded (false). Defaults to true.
     * @return void
     */
    public static function load($file_path, $relative = true, $load_first_match = true) {
        if($relative) {
            if(empty(self::$base_paths)) {
                throw new Exception("No external base paths are set.");
            }
            
            $file_path = ltrim($file_path, '/');
            
            $file_exists = false;

            if(empty(self::$loaded[$file_path])) {
                foreach(self::$base_paths as $base_path) {
                    $full_file_path = "{$base_path}/{$file_path}";

                    if(is_file($full_file_path)) {
                        $file_exists = true;
                    
                        include_once($full_file_path);
                        
                        self::$loaded[$file_path] = $full_file_path;
                        
                        if(!empty($load_first_match)) {
                            break;
                        }
                    }
                }
                
                if(!$file_exists) {
                    throw new Exception("Relative file path '{$file_path}' cannot be read or doesn't exist in any registered base paths.");
                }
            }
        }
        else {
            if(empty(self::$loaded[$file_path])) {
                if(is_file($file_path)) {
                    include_once($file_path);
                    
                    self::$loaded[$file_path] = $file_path;
                }
                else {
                    throw new Exception("Absolute file path '{$file_path}' is not readable or does not exist.");
                }
            }
        }
    }
}