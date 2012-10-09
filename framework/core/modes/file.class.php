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
namespace Framework\Core\Modes;

require_once(__DIR__ . '/web.class.php');

class File
extends Web {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\FileError';
    
    /**
    * @var string The name of the assets folder to use in the full path.
    */
    protected $assets_folder = 'files';
    
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
        parent::__construct($mode);
        
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");
        header("Accept-Ranges: bytes");
        
        request()->get->setRequired(array('file'));
        
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
     * Retrieves a value from the request and strips it of any potentially dangerous characters.
     *
     * @param string $parameter_name The name of the variable to retrieve from the request.     
     * @return string
     */
    protected function sanitizePathParameter($parameter_name) {
        return str_replace(array(
            '~',
            '\\',
            '..'
        ), '', request()->get->$parameter_name);
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
        $file_path = self::$installation_path;
        
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
            $this->initializeNotFound();
        }
    }
    
    /**
     * Indicates to the client that the requested file could not be found.
     *
     * @return void
     */
    public function initializeNotFound() {
        header("HTTP/1.0 404 Not Found");
    }
}