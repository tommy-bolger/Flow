<?php
/**
* The edit page for permissions.
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

namespace Modules\Admin\Settings\Permissions;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Edit
extends Manage {
    protected $title = "Edit Permission";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Edit'] = Http::getCurrentLevelPageUrl('edit');
    }
    
    protected function constructRightContent() {
        //The education history table
        $permissions_form = new EditTableForm(
            'permissions',
            'cms_permissions',
            'permission_id',
            '',
            array('module_id')
        );
        
        $permissions_form->setTitle("Edit this Permission");

        $permissions_form->addHidden('module_id', $this->managed_module->getId());
        $permissions_form->addTextbox('display_name', 'Permission Name');
        $permissions_form->addTextarea('description', 'Description');
        $permissions_form->addSubmit('save', 'Save');
        
        $permissions_form->processForm();
        
        $this->body->addChild($permissions_form, 'current_menu_content');
    }
}