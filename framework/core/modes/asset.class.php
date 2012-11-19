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

require_once(__DIR__ . '/file.class.php');

class Asset
extends File {
    /**
    * @var string The file type of the asset.
    */
    protected $type;
    
    /**
     * Initializes an instance of the framework in asset mode and processes an asset request.
     *
     * @param string Type the file type of the asset.
     * @return void
     */
    public function __construct($type) {
        $this->type = $type;    
        
        parent::__construct('asset');
    }
    
    /**
     * Displays the contents of the requested file.
     *
     * @return void
     */
    public function run() {
        if(self::$environment == 'production') {
            $output = '';
        
            if(self::$enable_cache) {
                $cache = cache();
                
                $output = $cache->get($this->full_path, $this->type);

                if(empty($output)) {
                    $output = file_get_contents($this->full_path);
                    
                    $cache->set($this->full_path, $output, $this->type);
                }
            
                echo $output;
            }
            else {
                readfile($this->full_path);
            }
        }
        else {
            readfile($this->full_path);
        }
    }
    
    /**
     * Sends the initial headers for the response.
     *
     * @return void
     */
    protected function sendInitialHeaders() {
        $type = $this->type;
        
        /*
            Make a special exception for htc files to translate them to their correct content type.
            This saves the overhead of extending this class just to modify the header content type.
        */
        if($type == 'htc') {
            $type = 'x-component';                      
        }
                
        header("Content-Type: text/{$type}");
        header('X-Content-Type-Options: nosniff');
        header("Cache-Control: public");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");    
    }
    
    /**
     * Indicates if the requested file has a valid extension.
     *
     * @return void
     */
    protected function validateExtension() {
        if(self::$environment == 'production') {
            header('Content-Encoding: gzip');
        }
        else {
            parent::validateExtension();
        }
    }
    
    /**
     * Compiles the full path to the requested file.
     *
     * @return void
     */
    protected function constructFilePath() {
        if(self::$environment == 'production') {        
            if(empty($this->module_name)) {
                $this->initializeNotFound(true);
            }

            $file_path = self::$installation_path . "/modules/{$this->module_name}/cache/{$this->type}/{$this->full_name}.gz";
        }
        else {
            $file_path = $this->full_name;
        }

        if(is_file($file_path)) {
            $this->full_path = $file_path;
            if(self::$environment != 'production') {
                //Sent the file's size
                header("Content-Length: " . filesize($file_path));
            }
        }
        else {
            $this->initializeNotFound(true);
        }
    }
}
