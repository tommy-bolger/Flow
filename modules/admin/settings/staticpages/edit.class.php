<?php
/**
* The edit page of the static page section of the Admin module.
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

namespace Modules\Admin\Settings\StaticPages;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Edit
extends Manage {
    protected $title = "Add/Edit Static Pages";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Edit'] = Http::getInternalUrl('', array(
            'settings',
            'static-pages'
        ), 'edit');
    }
    
    protected function constructRightContent() {
        $static_page_id = request()->get->static_page_id;
    
        $static_page_form = new EditTableForm('static_pages', 'cms_static_pages', 'static_page_id', 'sort_order', array('module_id'));
        
        $static_page_form->setTitle('Edit this Static Page');        

        $static_page_form->addHidden('module_id', $this->module_id);
        
        $display_name = db()->getOne("
            SELECT display_name
            FROM cms_static_pages
            WHERE static_page_id = ?
        ", array($static_page_id));
        
        $static_page_form->addReadOnly('display_name', 'Page', $display_name);
        $static_page_form->addTextbox('title', 'Title')->setMaxLength(255);
        $static_page_form->addTextArea('content', 'Content');
        $static_page_form->addBooleanCheckbox('is_active', 'Active');
        $static_page_form->addSubmit('save', 'Save');
        
        $static_page_form->setRequiredFields(array(
            'title',
            'content'
        ));
        
        $static_page_form->processForm();
        
        $this->body->addChild($static_page_form, 'current_menu_content');
    }
}