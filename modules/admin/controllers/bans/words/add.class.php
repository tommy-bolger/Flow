<?php
/**
* The add/edit page of the censored words section section of the Admin module.
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

namespace Modules\Admin\Controllers\Bans\Words;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Censored Words";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getInternalUrl('', array(
            'bans',
            'words'
        ), 'add');
    }
    
    protected function constructRightContent() {            
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        $words_form = new EditTableForm('words', 'cms_censored_words', 'censored_word_id');
        
        $words_form->setTitle('Add a New Censored Word');
        
        $words_form->addTextbox('original_word', 'Original Word');
        $words_form->addTextbox('translated_to', 'Translated To')->setMaxLength(255);        
        $words_form->addSubmit('save', 'Save');
        
        $words_form->setDefaultValues(array('translated_to' => '[CENSORED]'));
        
        $words_form->setRequiredFields(array(
            'original_word',
            'translated_to'
        ));
        
        $words_form->processForm();
        
        return $words_form;
    }
}