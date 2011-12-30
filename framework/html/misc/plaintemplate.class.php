<?php
/**
* Loads a plantext template with no placeholders into memory as an element.
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

namespace Framework\Html\Misc;

class PlainTemplate {
    /**
    * @var string The file path to the template file.
    */
    private $template_path;

    /**
     * Initializes a new instance of PlainTemplate.
     *      
     * @param string $template_path The path to the template file.
     * @return void
     */
    public function __construct($template_path) {
        $this->template_path = $template_path;
    }
    
    /**
     * Renders and retrieves the element's html.
     *      
     * @return string
     */
    public function toHtml() {
        $template_html = '';
            
        if(\Framework\Core\Framework::$enable_cache) {
            $template_html = cache()->get($this->template_path);
        }
        else {
            assert('is_readable($this->template_path)');
            
            $template_html = file_get_contents($this->template_path);
            
            if(\Framework\Core\Framework::$enable_cache) {
                cache()->set($this->template_path, $template_html);
            }
        }
        
        return $template_html;
    }
}