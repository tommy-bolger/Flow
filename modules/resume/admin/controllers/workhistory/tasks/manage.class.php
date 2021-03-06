<?php
/**
* The management page for the tasks of a user work history item of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\WorkHistory\Tasks;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;

class Manage
extends Home {
    protected $title = 'Manage Organization Tasks';
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function getDataTable() {
        $page_filter = array();
        $organization_options = array();
        
        if(!empty(request()->get->work_history_id)) {
            $page_filter = array('work_history_id');
        }
        else {
            $organizations = db()->getAll("
                SELECT
                    work_history_id,
                    organization_name,
                    job_title
                FROM resume_work_history
                ORDER BY sort_order
            ");

            if(!empty($organizations)) {
                foreach($organizations as $organization) {
                    $organization_options["{$organization['job_title']}, {$organization['organization_name']}"] = "work_history_id = {$organization['work_history_id']}"; 
                }
            }
        }
        
        $resultset = new SQL('work_history_tasks');
        
        $resultset->setBaseQuery("
            SELECT
                description,
                work_history_task_id
            FROM resume_work_history_tasks
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->setSortCriteria('sort_order', 'ASC');  
    
        $work_history_tasks_table = new EditTable(
            'work_history_tasks',
            'resume_work_history_tasks',
            'add',
            'work_history_task_id',
            'sort_order',
            $page_filter
        );
        
        $work_history_tasks_table->setNumberOfColumns(1);
        
        $work_history_tasks_table->setHeader(array(
            'description' => 'Task Description'
        ));
        
        if(!empty($organization_options)) {
            $work_history_tasks_table->addFilterDropdown('organization', $organization_options, 'Select an Organization');
        
            $work_history_tasks_table->setPrimaryDropdown('organization');
        }
        
        $work_history_tasks_table->process($resultset);
        
        return $work_history_tasks_table;
    }
    
    protected function constructRightContent() {        
        $this->page->body->addChild($this->getDataTable(), 'current_menu_content');
    }
}