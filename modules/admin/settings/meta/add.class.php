<?php
/**
* The add/edit page of the meta settings section of the Admin module.
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

namespace Modules\Admin\Settings\Meta;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Meta Setting";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getInternalUrl('', array(
            'settings',
            'meta'
        ), 'add');
    }
    
    protected function constructRightContent() {    
        $meta_settings_form = new EditTableForm('meta_settings', 'cms_meta_settings', 'meta_setting_id', 'sort_order', array('module_id'));

        $meta_settings_form->addHidden('module_id', $this->managed_module->getId());
        
        $meta_settings_form->setTitle('Add a New Meta Setting');

        $meta_settings_form->addDropdown('tag_name', 'Name', array(
            'Recommended' => array(
                'description' => 'Description',
                'Language' => 'Language'
            ),
            'Optional' => array(
                'Abstract' => 'Abstract',
                'Author' => 'Author',
                'Copyright' => 'copyright',
                'Designer' => 'Designer',
                'googlebot' => 'Googlebot',
                'keywords' => 'Keywords',
                'msnbot' => 'MSNBot',
                'Title' => 'Title'
            ),
            'Not Recommended' => array(
                'Distribution' => 'Distribution',
                'Generator' => 'Generator',
                'MSSmartTagsPreventParsing' => 'MS Smart Tags',
                'Publisher' => 'Publisher',
                'reply-to' => 'Reply-To',
                'resource-type' => 'Resource-Type',
                'Revisit-After' => 'Revisit-After',
                'ROBOTS' => 'Robots',
                'Subject' => 'Subject'
            )
        ))->addBlankOption();
        
        $meta_settings_form->addDropdown('http_equiv', 'Http-Equiv', array(
            'Recommended' => array(
                'Content-Language' => 'Content-Language',
                'Content-Type' => 'Content-Type',
            ),
            'Not Recommended' => array(
                'Content-Script-Type' => 'Content-Script-Type',
                'Content-Style-Type' => 'Content-Style-Type',
                'expires' => 'Expires',
                'Pragma' => 'Pragma',
                'Refresh' => 'Refresh',
                'Set-Cookie' => 'Set-Cookie'
            )
        ))->addBlankOption();
               
        $meta_settings_form->addTextArea('content', 'Content');        
        $meta_settings_form->addBooleanCheckbox('is_active', 'Active'); 
        $meta_settings_form->addSubmit('save', 'Save');
        
        $meta_settings_form->setRequiredFields(array('content'));
        
        if($meta_settings_form->wasSubmitted() && $meta_settings_form->isValid()) {
            $form_data = $meta_settings_form->getData();
            
            $tag_name = $form_data['tag_name'];
            $http_equiv = $form_data['http_equiv'];
            
            if(empty($tag_name) && empty($http_equiv)) {
                $meta_settings_form->addError('You must select an option for either Name or Http-Equiv.');
            }
            elseif(!empty($tag_name) && !empty($http_equiv)) {
                $meta_settings_form->addError('Name and Http-Equiv cannot both have a selected option.');
            }
            else {
                $meta_settings_form->processForm();
            }
        }
        else {
            $meta_settings_form->processForm();
        }
        
        $this->body->addChild($meta_settings_form, 'current_menu_content');
    }
}