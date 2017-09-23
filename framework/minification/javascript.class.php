<?php
/**
* Cleans up and compresses css.
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

class Javascript
extends Minifier {    
    /**
    * Strips the unminified html of any unnecessary characters.
    * 
    * @return void
    */
    public function clean() {
        $this->loadUnminifiedData();
        
        $javascript_minifier = $this->framework->getConfiguration()->javascript_minifier;
        
        if($javascript_minifier != 'simple') {
            $unminified_temp_file = tempnam('flow_scratch', 'js_unminified');
            $minified_temp_file = tempnam('flow_scratch', 'js_minified');
            
            //Write the unminified data to a temp file
            file_put_contents($unminified_temp_file, $this->unminified_data);
        
            switch($javascript_minifier) {
                case 'closure':
                    $compiler_path = escapeshellcmd($this->framework->getConfiguration()->closure_compiler_path);

                    if(!empty($compiler_path)) {
                        exec("java -jar {$compiler_path} --js {$unminified_temp_file} --js_output_file {$minified_temp_file}");
                    }
                    break;
                case 'uglify-js':
                    exec("uglifyjs -nc -o {$minified_temp_file} {$unminified_temp_file}");
                    break;
            }
            
            if(is_file($minified_temp_file)) {
                $this->minified_data = file_get_contents($minified_temp_file);
                
                unlink($minified_temp_file);
            }
            
            unlink($unminified_temp_file);
        }
        
        if(empty($this->minified_data)) {
            $this->minified_data = $this->unminified_data;
        }
    }
}