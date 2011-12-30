<?php
/**
* The management page for configurations.
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

namespace Modules\Admin;

use \Framework\Html\Misc\Div;
use \Framework\Html\Table\EditTableForm;

class ToggleModules
extends Home {
    protected $title = "Toggle Modules";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Toggle Modules</h2><br />');

        //The education history table
        $configurations_table = new EditTableForm(
            'modules',
            'modules',
            'module_id',
            'sort_order'
        );
        
        $configurations_table->setNumberOfColumns(4);
    
        $configurations_table->addHeader(array(
            'Module',
            'enabled' => 'Enabled',
        ));
        
        if($configurations_table->getFormVisibility()) {
            $configurations_table->addCheckbox('enabled', 'Enabled');
            $configurations_table->addSubmit('save', 'Save');
            
            $configurations_table->processForm();
        }
        
        $configurations_table->useQuery("
            SELECT
                display_name,
                enabled,
                module_id
            FROM cms_modules
            ORDER BY sort_order
        ");
        
        $content->addChild($configurations_table);
        
        $this->body->addChild($content);
    }
}