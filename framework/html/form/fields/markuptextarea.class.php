<?php
/**
* Allows the rendering of a form textarea field with advanced text formatting and performs validation on its submitted data.
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

namespace Framework\Html\Form\Fields;

class MarkupTextarea
extends Textarea {
    /**
     * Initializes a new instance of MarkupTextArea.
     *      
     * @param string $textarea_name The name of the textarea.
     * @param string $textarea_level (optional) The label for the textarea.
     * @param string $textara_value (optional) The value of the textarea.        
     * @return void
     */
    public function __construct($markup_name, $markup_label = "", $markup_value = NULL) {
        parent::__construct($markup_name, $markup_label, $markup_value, array('markup_editor'));
    }

    /**
     * Adds the textarea's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFiles(array(
            'framework/MarkupTextArea/skin_style.css',
            'framework/MarkupTextArea/setting_style.css'
        ));
    
        page()->addJavascriptFiles(array(
            'jquery.min.js',
            'jquery.markitup.js',
            'framework/MarkupTextArea/jquery.markitup_settings.js',
            'framework/MarkupTextArea/MarkupTextArea.js'
        ));
    }
}