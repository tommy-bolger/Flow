<?php
/**
* The add/edit page of the Ads section for the Admin module.
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

namespace Modules\Admin\Controllers\Ads;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Ad";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getInternalUrl('', array('ads'), 'manage');
    }
    
    protected function constructRightContent() {    
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        //The education history table
        $ad_form = new EditTableForm('ads', 'cms_ads', 'ad_id');
        
        $ad_form->addConstantFields(array('module_id' => $this->managed_module->getId()));
        
        $ad_form->setTitle('Add a New Ad');

        $ad_form->addTextbox('description', 'Description')->setMaxLength(255);     
        $ad_form->addTextArea('code', 'Code');        
        $ad_form->addBooleanCheckbox('is_active', 'Active'); 
        $ad_form->addSubmit('save', 'Save');
        
        $ad_form->setRequiredFields(array(
            'description',
            'code'
        ));
        
        $ad_form->processForm();
        
        return $ad_form;
    }
}