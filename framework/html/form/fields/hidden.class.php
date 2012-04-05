<?php
/**
* Allows the rendering of a form hidden field dynamically.
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

class Hidden
extends \Framework\Html\Form\FieldObjects\Field {
    /**
    * @var boolean Flag indicating that the field's value gets included when retrieving the data of only interactive fields in the form objects.
    */
    protected $is_interactive = false;
    
    /**
    * @var boolean Flag indicating that the field can have a label.
    */
    protected $has_label = false;

    /**
     * Initializes a new instance of Hidden.
     *      
     * @param string $hidden_name The name of the hidden field.
     * @param mixed $hidden_value (optional) The hidden field's default value.   
     * @return void
     */
    public function __construct($hidden_name, $hidden_value = NULL) {
        parent::__construct("hidden", $hidden_name);
        
        $this->setDefaultValue($hidden_value);
    }

    public function toTemplate() {}
    
    /**
     * Renders and retrieves the hidden field's html.
     *      
     * @return string
     */
    public function toHtml() {
        return $this->getFieldHtml();
    }
}