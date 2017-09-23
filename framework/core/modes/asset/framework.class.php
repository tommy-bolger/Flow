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
namespace Framework\Core\Modes\Asset;

use \Framework\Core\Modes\File\Framework as BaseFramework;

require_once(dirname(__DIR__) . '/file/framework.class.php');

class Framework
extends BaseFramework {
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
        $output = '';
    
        if($this->cache->initialized()) { 
            $output = $this->cache->get($this->full_path, $this->type);

            if(empty($output)) {
                $output = file_get_contents($this->full_path);
                
                $this->cache->set($this->full_path, $output, $this->type);
            }

            echo $output;
        }
        else {
            readfile($this->full_path);
        }
    }
    
    /**
     * Retrieves the parsed request uri.
     *
     * @return string
     */
    public function getParsedUri() {
        $parsed_uri_segments = parent::getParsedUri();

        return $parsed_uri_segments;
    }
    
    /**
     * Sends the initial headers for the response.
     *
     * @return void
     */
    protected function sendInitialHeaders() {
        $type = $this->type;
                
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
    protected function validateExtension() {}
    
    /**
     * Compiles the full path to the requested file.
     *
     * @return void
     */
    protected function constructFilePath() {
        $base_file_path = '';
        $file_path = '';
          
        if(empty($this->module_name)) {
            $this->initializeNotFound(true);
        }

        $base_file_path = "{$this->installation_path}/modules/{$this->module_name}/cache/{$this->type}/{$this->full_name}";
        $file_path = "{$base_file_path}.gz";

        if(is_file($file_path)) {
            header('Content-Encoding: gzip');
        
            $this->full_path = $file_path;
        }
        else {
            $file_temp_path = "{$base_file_path}.tmp";
            $file_lock_path = "{$base_file_path}.lock";
            
            if(is_file($file_temp_path)) {                
                ini_set('zlib.output_compression', 1);

                ini_set('zlib.output_compression_level', 9);
                
                ob_start();
                
                $this->full_path = $file_temp_path;
                
                if(!is_file($file_lock_path)) {   
                    exec(
                        PHP_BINDIR . "/php {$this->installation_path}/flow_cli.php admin assets Minify --module_name=" . escapeshellarg($this->module_name) . " --asset_type=" . escapeshellarg($this->type) . " --file_name=" . escapeshellarg($this->full_name) . " --no_output >> {$this->installation_path}/logs/minification.log 2>&1 &"
                    );                     
                }
            }
            else {
                $this->initializeNotFound(true);
            }
        }
        
        //Send the file's size
        header("Content-Length: " . filesize($this->full_path));
    }
}
