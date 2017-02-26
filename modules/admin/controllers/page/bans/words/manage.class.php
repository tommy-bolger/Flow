<?php
/**
* The management page of the censored words section section for the Admin module.
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

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;

class Manage
extends Home {
    protected $title = "Manage Censored Words";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getInternalUrl('', array(
            'bans',
            'words'
        ), 'manage');
    }
    
    protected function getDataTable() {
        $resultset = new SQL('word_bans');
        
        $resultset->setBaseQuery("
            SELECT
                original_word,
                translated_to,
                censored_word_id
            FROM cms_censored_words
        ");
    
        $words_table = new EditTable(
            'words',
            'cms_censored_words',
            'add',
            'censored_word_id'
        );

        $words_table->setHeader(array(
            'original_word' => 'Original Word',
            'translated_to' => 'Translated To'
        ));
        
        $words_table->setNumberOfColumns(2);
        
        $words_table->process($resultset);
        
        return $words_table;
    }
    
    protected function constructRightContent() {                    
        $this->page->body->addChild($this->getDataTable(), 'current_menu_content');
    }
}