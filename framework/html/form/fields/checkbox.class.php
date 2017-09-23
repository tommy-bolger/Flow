<?php
/**
* Allows the rendering of a form checkbox field and performing validation on its submitted data dynamically.
* Copyright (c) 2017, Tommy Bolger
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

use \Framework\Html\Form\FieldObjects\Toggle;

class Checkbox
extends Toggle {
    /**
     * Instantiates a new instance of a checkbox.
     *      
     * @param string $checkbox_name The checkbox name.
     * @param string $checkbox_label (optional) The checkbox label.
     * @param boolean $is_checked (optional) Field checked flag, defaults to false.
     * @param string $checkbox_value (optional) The checkbox value, defaults to 'yes'.         
     * @param array $css_classes (optional) An array of classes.
     * @return void
     */
    public function __construct($checkbox_name, $checkbox_label = NULL, $is_checked = false, $checkbox_value = 'yes', $css_classes = array()) {
        parent::__construct("checkbox", $checkbox_name, $checkbox_label, $css_classes, $checkbox_value, $is_checked);
    }
}