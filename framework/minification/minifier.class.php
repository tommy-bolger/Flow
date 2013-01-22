<?php
/**
* Base class for objects that clean up and compress files.
* Copyright (c) 2012, Tommy Bolger
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
namespace Framework\Minification;

use \Framework\Core\Framework;

class Minifier {
    /**
    * @var object The instance of the framework.
    */ 
    protected $framework;

    /**
    * @var array The list of all unminified files to be converted.
    */ 
    protected $unminified_files;
    
    /**
    * @var string The data that has been minified.
    */ 
    protected $minified_data;

    /**
    * Initializes a new instance of Minify. 
    * 
    * @param array|string $unminified_files A list of file paths to all files that need to be loaded or the raw data to be minified.
    * @return void
    */
    public function __construct($unminified_files) {    
        $this->framework = Framework::getInstance();
        
        $this->unminified_files = $unminified_files;
    }

    /**
    * Loads and returns all unminified data.
    * 
    * @return string The loaded unminified data.
    */
    protected function getUnminifiedData() {
        $all_unminified_data = '';

        if(is_array($this->unminified_files)) {
            foreach($this->unminified_files as $unminified_file) {
                $all_unminified_data .= file_get_contents($unminified_file) . "\n";
            }
        }
        else {
            $all_unminified_data = $this->unminified_files;
        }
        
        return $all_unminified_data;
    }
    
    /**
    * Strips the unminified string of any unnecessary characters.
    * 
    * @return void
    */
    public function clean() {}
    
    /**
    * Compresses the cleaned data.
    * 
    * @return void
    */
    public function compress() {
        if(empty($this->minified_data)) {
            throw new \Exception('There is no minified data to compress.');
        }
        
        $this->minified_data = gzencode($this->minified_data, 9);
    }
    
    /**
    * Retrieves the minified data.
    * 
    * @return string
    */
    public function getMinifiedData() {
        return $this->minified_data;
    }
}