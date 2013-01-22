<?php
/**
* Allows the rendering of a form dropdown field automatically populated with U.S. states and performing validation on its submitted data dynamically.
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

use \Framework\Core\Framework;

class StateSelect
extends Dropdown {
    /**
     * Initializes a new instance of StateSelect.
     *      
     * @param string $select_name The state select name.
     * @param string $select_label (optional) The state select label
     * @param array $css_classes (optional) A list of css classes for this field.   
     * @return void
     */
    public function __construct($select_name, $select_label = "", $css_classes = array()) {
        $options = NULL;
        
        $framework = Framework::getInstance();
    
        if($framework->enable_cache) {
            $options = cache()->get('options', 'state_select');
        }
    
        if(empty($options)) {
            $options = db()->getConcatMappedColumn("
                SELECT 
                    state_id, 
                    abbreviation,
                    state_name
                FROM cms_us_states
                ORDER BY abbreviation
            ", ' - ');
            
            if($framework->enable_cache) {
                cache()->set('options', $options, 'state_select');
            }
        }
    
        parent::__construct($select_name, $select_label, $options, $css_classes);
    }
}