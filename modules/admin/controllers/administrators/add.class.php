<?php
/**
* The add/edit page of the administrators section for the Admin module.
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

namespace Modules\Admin\Controllers\Administrators;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Administrator";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getInternalUrl('', array('administrators'), 'manage');
    }
    
    protected function constructRightContent() {    
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        //The education history table
        $admin_form = new EditTableForm('administrators', 'cms_users', 'user_id');
        
        $admin_form->addConstantFields(array('is_site_admin' => 1));
        
        $admin_form->setTitle('Add a New Administrator');

        $admin_form->addTextbox('user_name', 'Username')->setMaxLength(50);     
        $admin_form->addEmail('email_address', 'Email Address')->setMaxLength(255); 
        $admin_form->addSubmit('save', 'Save');
        
        $admin_form->setRequiredFields(array(
            'user_name',
            'email_address'
        ));
        
        $admin_form->processForm();
        
        return $admin_form;
    }
}