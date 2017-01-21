<?php
/**
* The conductor class for the file mode of the framework to handle file requests.
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
namespace Framework\Core\Modes\File;

use \Framework\Core\Modes\Web;
use \Framework\Utilities\File;

require_once(dirname(__DIR__) . '/web.class.php');

class Framework
extends Web {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Core\\Modes\\File\\Error';

    /**
    * @var string The name of the assets folder to use in the full path.
    */
    protected $assets_folder = 'files';
    
    /**
    * @var array The uri segments containing the module, theme, and file path of this request.
    */
    protected $uri_segments;
    
    /**
    * @var string The name of the module in the request.
    */
    protected $module_name;
    
    /**
    * @var string The name of the module's theme in the request.
    */
    protected $theme_name;
    
    /**
    * @var string The file name of the requested file.
    */
    protected $full_name;
    
    /**
    * @var string The name portion of the requested file.
    */
    protected $name;
    
    /**
    * @var string The extension of the requested file.
    */
    protected $extension;
    
    /**
    * @var string The full path to the requested file.
    */
    protected $full_path;
    
    /**
     * Initializes an instance of the framework in file mode and processes a file request.
     *
     * @return void
     */
    public function __construct($mode = 'file') {
        $this->sendInitialHeaders();
                    
        parent::__construct($mode);
        
        $this->parsed_uri_segments = $this->getParsedUri();

        $this->full_name = $this->sanitizePathParameter('file');

        $this->module_name = $this->sanitizePathParameter('module');
        
        $this->theme_name = $this->sanitizePathParameter('theme');
        
        $file_name_split = explode('.', $this->full_name);
        
        if(!empty($file_name_split[0])) {
            $this->name = $file_name_split[0];
        } 
     
        if(!empty($file_name_split[1])) {
            $this->extension = $file_name_split[1];
        }
        
        $this->validateExtension();
        
        $this->constructFilePath();
        
        if(!empty($this->full_path)) {
            /* ----- The following code is based on this example: http://www.php.net/manual/en/function.header.php#85146 ----- */
            $last_modified_time = NULL;
            $etag = NULL;
        
            if($this->enable_cache) {
                $last_modified_time = cache()->get($this->full_path, "last_modified_time");
                $etag = cache()->get($this->full_path, "etag");
            }
            
            if(empty($last_modified_time)) {
                $last_modified_time = filemtime($this->full_path);
            }
            
            if(empty($etag)) {
                $etag = md5_file($this->full_path);
            }
            
            if($this->enable_cache) {                
                cache()->set($this->full_path, $last_modified_time, "last_modified_time");
                cache()->set($this->full_path, $etag, "etag");
            }

            header("Etag: {$etag}");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT"); 

            //If the file is still cached on the client side do not send it.
            $modified_since = '';
            
            if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            }

            $none_match = '';
            
            if(!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
                $none_match = trim($_SERVER['HTTP_IF_NONE_MATCH']);
            }
            
            if ($modified_since == $last_modified_time || $none_match == $etag) {
                header("HTTP/1.1 304 Not Modified");

                exit;
            }
        }
    }
    
    /**
     * Displays the contents of the requested file.
     *
     * @return void
     */
    public function run() {
        readfile($this->full_path);
        
        exit;
    }
    
    /**
     * Retrieves the parsed request uri.
     *
     * @return string
     */
    public function getParsedUri() {
        $unparsed_uri = parent::getParsedUri();
        
        $unparsed_uri = str_replace("assets/{$this->assets_folder}/", '', $unparsed_uri);

        $unparsed_uri_segments = explode('/', $unparsed_uri);

        //If the first element is a blank string then remove it
        if(isset($unparsed_uri_segments[0]) && empty($unparsed_uri_segments[0])) {
            array_shift($unparsed_uri_segments);
        }
        
        $parsed_uri_segments = array();
        
        $module_segment_index = array_search('modules', $unparsed_uri_segments);

        if($module_segment_index !== false) {
            //Remove all elements before module
            $unparsed_uri_segments = array_slice($unparsed_uri_segments, ($module_segment_index));
            
            //Reset the module segment index to the first element
            $module_segment_index = 0;
        
            $module_value_index = $module_segment_index + 1;
        
            $parsed_uri_segments['module'] = $unparsed_uri_segments[$module_value_index];

            unset($unparsed_uri_segments[$module_segment_index]);
            unset($unparsed_uri_segments[$module_value_index]);
        
            $theme_segment_index = array_search('styles', $unparsed_uri_segments);
            
            if(!empty($theme_segment_index)) {
                $theme_value_index = $theme_segment_index + 1;
                                    
                $parsed_uri_segments['theme'] = $unparsed_uri_segments[$theme_value_index];
                
                unset($unparsed_uri_segments[$theme_segment_index]);
                unset($unparsed_uri_segments[$theme_value_index]);
            }
        }

        $parsed_uri_segments['file'] = urldecode(implode('/', $unparsed_uri_segments));

        return $parsed_uri_segments;
    }
    
    /**
     * Sends the initial headers for the response.
     *
     * @return void
     */
    protected function sendInitialHeaders() {
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");
        header("Accept-Ranges: bytes");
    }

    /**
     * Retrieves a value from the request and strips it of any potentially dangerous characters.
     *
     * @param string $parameter_name The name of the variable to retrieve from the request.     
     * @return string
     */
    protected function sanitizePathParameter($parameter_name) {
        if(!empty($this->parsed_uri_segments[$parameter_name])) {
            return File::sanitizePathSegment($this->parsed_uri_segments[$parameter_name]);
        }
        
        return '';
    }
    
    /**
     * Indicates if the requested file has a valid extension.
     *
     * @return void
     */
    protected function validateExtension() {
        $extension = strtolower($this->extension);
    
        switch($extension) {
            case 'php':
                throw new \Exception("Requested file '{$extension}' is forbidden.");
                break;
            default:
                header("Content-Type: application/{$extension}");

                break;
        }
    }
    
    /**
     * Compiles the full path to the requested file.
     *
     * @return void
     */
    protected function constructFilePath() {
        $file_path = $this->installation_path;
        
        if(!empty($this->module_name)) {
            $file_path .= "/modules/{$this->module_name}/assets";
            
            if(!empty($this->theme_name)) {
                $file_path .= "/styles/{$this->theme_name}";
            }
        }
        else {
            $file_path .= "/public/assets";
        }
        
        $file_path .= "/{$this->assets_folder}/{$this->full_name}";

        if(is_file($file_path)) {
            $this->full_path = $file_path;
            
            //Sent the file's size
            header("Content-Length: " . filesize($file_path));
        }
        else {
            $this->initializeNotFound(true);
        }
    }
    
    /**
     * Indicates to the client that the requested file could not be found.
     *      
     * @param boolean $exit Indicates if code execution should be terminated immediately after sending 404 headers. Defaults to false.
     * @return void
     */
    public function initializeNotFound($exit = false) {
        header("HTTP/1.0 404 Not Found");

        if($exit) {
            exit;
        }
    }
}
