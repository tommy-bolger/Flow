<?php
/**
* Allows the rendering of a form select field as a listbox and performing validation on its submitted data dynamically.
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

use \Framework\Html\Form\FieldObjects\Select;

class Listbox
extends Select {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'Listbox';

    /**
     * Instantiates a new instance of Listbox.
     *      
     * @param string $listbox_name The listbox name.
     * @param string $listbox_label (optional) The listbox label.
     * @param array $options (optional) The options for the listbox field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name).
     * @param array $css_classes A list of css classes.
     * @return void
     */
    public function __construct($listbox_name, $listbox_label = "", $options = array(), $css_classes = array()) {
        parent::__construct($listbox_name, $listbox_label, $options, $css_classes);
        
        $this->setMultiSelect();
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();

        $this->addJavascriptFile('form/fields/Listbox.js');
    }
    
    /**
     * Prepends a blank option to the select field.
     *      
     * @param string $blank_option_text (optional) The blank option's display text. Defaults to a blank string.
     * @return void
     */
    public function addBlankOption($blank_option_text = "") {}
}