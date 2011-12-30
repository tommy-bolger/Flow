<?php
/**
* The home page of the Admin module.
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

use \Framework\Html\Table\DataTable;
use \Framework\Html\Misc\Div;
use \Framework\Utilities\Http;

class SiteErrors
extends Home {
    protected $title = "Site Errors";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Site Errors'] = Http::getCurrentBaseUrl() . 'site-errors';
    }

    protected function constructRightContent() {        
        $site_errors_table = new DataTable('cms_errors', array(), array(
            'time' => 'Time',
            'message' => 'Message',
            'module' => 'Module'
        ));
        
        $site_errors_table->setNumberofColumns(3);
        
        $site_errors_table->setDefaultSortOrder('e.created_time', 'DESC');
        
        $site_errors_table->setSortColumnOptions(array(
            'time' => 'e.created_time',
            'message' => 'e.error_message',
            'module' => 'module_name' 
        ));
        
        $site_errors_table->setColumnsAsLink(array(2), Http::getPageBaseUrl() . 'site-error', array(
            'error_id' => 'error_id'
        ));
        
        $site_errors_table->useQuery("
            SELECT
                NULL AS error_time,
                e.error_message,
                CASE
                    WHEN m.module_id IS NOT NULL THEN m.module_name
                    ELSE 'Framework'
                END AS module_name,
                e.error_id,
                e.created_time
            FROM cms_errors e
            LEFT JOIN cms_modules m USING (module_id)
        ", array(), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as $row_index => $query_row) {
                    $query_row['error_time'] = date('m/d/Y H:i', strtotime($query_row['created_time']));
                    
                    $query_rows[$row_index] = $query_row;
                }
            }
            
            return $query_rows;
        });
        
        $content_div = new Div(array('id' => 'current_menu_content'));
        
        $content_div->addChild($site_errors_table);
        
        $this->body->addChild($content_div);
    }
}