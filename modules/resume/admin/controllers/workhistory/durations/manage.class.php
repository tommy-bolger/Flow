<?php
/**
* The management page for the durations of a user work history item of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\WorkHistory\Durations;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Organization Durations";
    
    protected $active_sub_nav_link = "Manage";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {
        $page_filter = array();
        $filter_dropdown = array();
        
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
                $dropdown_options = array();
            
                foreach($organizations as $organization) {
                    $dropdown_options["{$organization['job_title']}, {$organization['organization_name']}"] = array(
                        'work_history_id' => $organization['work_history_id']
                    ); 
                }
                
                $filter_dropdown = array('Select an Organization' => $dropdown_options);
            }
        }
    
        $work_history_durations_table = new EditTable(
            'work_history_durations',
            'resume_work_history_durations',
            'add',
            'work_history_duration_id',
            'sort_order',
            $page_filter,
            $filter_dropdown
        );
        
        $work_history_durations_table->setNumberOfColumns(2);
        
        $work_history_durations_table->addHeader(array(
            'start_date' => 'Start Date',
            'end_date' => 'End Date'
        ));
        
        $work_history_durations_table->useQuery("
            SELECT
                start_date,
                end_date,
                work_history_duration_id
            FROM resume_work_history_durations
            ORDER BY sort_order ASC
        ", array(), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as $row_index => $query_row) {
                    $query_row['start_date'] = date('m/Y', strtotime($query_row['start_date']));
                
                    $end_date = $query_row['end_date'];
                    
                    if(!empty($end_date)) {
                        $query_row['end_date'] = date('m/Y', strtotime($end_date));
                    }
                    else {
                        $query_row['end_date'] = 'Present';
                    }
                    
                    $query_rows[$row_index] = $query_row;
                }
                
                return $query_rows;
            }
        });
        
        $this->page->body->addChild($work_history_durations_table, 'current_menu_content');
    }
}