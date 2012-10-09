<?php
/**
* The management page of the meta settings section for the Admin module.
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

namespace Modules\Admin\Controllers\Settings\Meta;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Meta Settings";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getInternalUrl('', array(
            'settings',
            'meta'
        ), 'manage');
    }
    
    protected function constructRightContent() {            
        $meta_settings_table = new EditTable(
            'meta_settings',
            'cms_meta_settings',
            'add',
            'meta_setting_id',
            'sort_order',
            array('module_id')
        );

        $meta_settings_table->addHeader(array(
            'tag_name' => 'Name',
            'http_equiv' => 'Http-Equiv',
            'content' => 'Content',
            'is_active' => 'Active'
        ));
        
        $meta_settings_table->setNumberOfColumns(4);
        
        $meta_settings_table->useQuery("
            SELECT
                tag_name,
                http_equiv,
                content,
                is_active,
                meta_setting_id
            FROM cms_meta_settings
        ", array(), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as &$query_row) {
                    if(!empty($query_row['is_active'])) {
                        $query_row['is_active'] = 'Yes';
                    }
                    else {
                        $query_row['is_active'] = 'No';
                    }
                }
            }

            return $query_rows;
        });
        
        $this->page->body->addChild($meta_settings_table, 'current_menu_content');
    }
}