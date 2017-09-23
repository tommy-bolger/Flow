<?php
/**
* The home page of the Admin module.
* Copyright (c) 2017, Tommy Bolger
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
namespace Modules\Admin\Controllers\Page\Errors;

use \Framework\Html\Table\DataTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;
use \Modules\Admin\Controllers\Page\Home as AdminHome;

class Home
extends AdminHome {
    protected $name = 'errors';
    
    protected $sub_name = '';

    protected $datatable;

    public function init() {
        parent::init();
        
        $this->title = "View All Errors";
        $this->name = 'errors';
        
        $this->initializeDataTable();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $query_string_parameters = array();
        
        if(!empty($this->managed_module)) {
            $query_string_parameters['module_id'] = $this->managed_module->getId();
        }

        $this->page_links['All'] = Http::getCurrentLevelPageUrl('all', $query_string_parameters);
    }
    
    protected function initializeDataTable() {
        $resultset = new SQL('cms_errors');
        
        $resultset->enableTotalRecordCount();
        
        $query_string_parameters = array('error_id' => 'error_id');
        
        $resultset->setBaseQuery("
            SELECT
                e.incident_number,
                NULL AS error_time,
                e.error_message,
                CASE
                    WHEN m.module_id IS NOT NULL THEN m.module_name
                    ELSE 'Framework'
                END AS module_name,
                e.error_id,
                e.module_id,
                e.created_time
            FROM cms_errors e
            LEFT JOIN cms_modules m USING (module_id)
            {{WHERE_CRITERIA}}
        ");
        
        //Set default sort criteria
        $resultset->setSortCriteria('e.created_time', 'DESC');
        
        //Set default rows per page
        $resultset->setRowsPerPage(25);
        
        $count_resultset = clone $resultset;
        
        $count_resultset->clearLeftJoinCriteria();
        $count_resultset->clearSortCriteria();
        
        $resultset->setCountResultset($count_resultset);
        
        $module_id = NULL;
        $module_name = '';
        
        //Set module context for the resultset, datatable, and table links if a module_id is specified
        if(!empty($this->managed_module)) {
            $query_string_parameters['module_id'] = 'module_id';
            
            $module_id = $this->managed_module->getId();
            $module_name = $this->managed_module->getName();

            $resultset->addFilterCriteria("module_id = ?", array(
                $module_id
            ));
        }
        
        $site_errors_table = new DataTable("cms_errors_{$module_name}", true);
        
        if(!empty($module_id)) {
            $site_errors_table->addRequestVariable('module_id', $module_id);
        }
        
        $site_errors_table->setRowsPerPageOptions(array(10, 25, 50, 100));
        
        $site_errors_table->setNumberofColumns(4);
        
        $site_errors_table->setHeader(array(
            'incident_number' => 'Incident number',
            'time' => 'Time',
            'message' => 'Message',
            'module' => 'Module'
        ));
        
        $site_errors_table->setSortColumnOptions(array(
            'incident_number' => 'e.incident_number',
            'time' => 'e.created_time',
            'message' => 'e.error_message',
            'module' => 'module_name' 
        ));
        
        $site_errors_table->setColumnsAsLink(array(1), '/admin/errors/view/one', $query_string_parameters);
        
        $site_errors_table->addFilterTextbox('incident_number', 'e.incident_number = ?', 'Matches', 'incident_number');
        
        $site_errors_table->process($resultset, function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as $row_index => $query_row) {
                    $query_row['error_time'] = date('m/d/Y H:i', strtotime($query_row['created_time']));
                    
                    $query_rows[$row_index] = $query_row;
                }
            }
            
            return $query_rows;
        });
        
        return $site_errors_table;
    }

    public function actionGet() {        
        $this->page->body()->addChild($this->datatable, 'content');
    }
    
    public function actionPost() {
        $this->page->body()->addChild($this->datatable, 'content');
    }
}