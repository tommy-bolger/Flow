<?php
/**
* Allows the rendering of an <img> tag dynamically.
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

class Image
extends \Framework\Html\Element {
    /**
     * Initializes a new instance of ImageElement.
     *      
     * @param string $image_file_path The file path to the image relative to the document root.
     * @param array $element_attributes (optional) The html attributes of the image element.
     * @return void
     */
    public function __construct($image_file_path, $element_attributes = array()) {    
        parent::__construct("img", $element_attributes, NULL);
        
        $this->setAttribute('src', $image_file_path);
    }
    
    /**
     * Catches calls to functions not in this class and throws an exception to avoid a fatal error.
     *      
     * @param string $function_name The called function name.
     * @param array $arguments The function arguments.
     * @return void
     */
    public function __call($function_name, $arguments) {
        throw new \Exception("Function name '{$function_name}' does not exist in this class.");
    }
    
    /**
     * Renders and retrieves the image element's html.
     *
     * @return string
     */
    public function toHtml() {
        return "<{$this->tag}{$this->renderAttributes()} />";
    }
}