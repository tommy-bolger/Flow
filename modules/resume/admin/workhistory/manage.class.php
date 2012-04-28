<?php
/**
* The management page for the user work history of the Online Resume module.
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

namespace Modules\Resume\Admin\WorkHistory;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Work History";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {            
        $work_history_table = new EditTable(
            'work_history',
            'resume_work_history',
            'add',
            'work_history_id',
            'sort_order'
        );
        
        $work_history_table->setNumberOfColumns(3);
        
        $work_history_table->addHeader(array(
            'organization_name' => 'Organization Name',
            'job_title' => 'Job Title',
            'Duration',
            'Tasks'
        ));
        
        $work_history_data = db()->getAll("
            SELECT
                wh.work_history_id,
                wh.organization_name,
                wh.job_title
            FROM resume_work_history wh
            ORDER BY wh.sort_order
        ");
        
        $work_history_durations = db()->getAssoc("
            SELECT 
                work_history_id,
                start_date,
                end_date
            FROM resume_work_history_durations
            ORDER BY sort_order ASC
        ");
        
        if(!empty($work_history_data)) {                
            foreach($work_history_data as $work_history_row) {
                $work_history_id = $work_history_row['work_history_id'];
                
                $work_history_id_array = array('work_history_id' => $work_history_id);
                
                $durations_page_url = Http::getLowerLevelPageUrl(array('durations'), 'manage', $work_history_id_array, 'resume');
                $tasks_page_url = Http::getLowerLevelPageUrl(array('tasks'), 'manage', $work_history_id_array, 'resume');
            
                $durations_edit_url = "<a href=\"{$durations_page_url}\">Edit Durations</a>";
            
                if(!empty($work_history_durations[$work_history_id])) {
                    $row_durations = $work_history_durations[$work_history_id];

                    $durations = '';
                    
                    foreach($row_durations as $row_duration) {
                        $start_date = date('m/Y', strtotime($row_duration['start_date']));
                        
                        $end_date = 'Present';
                    
                        if(!empty($row_duration['end_date'])) {
                            $end_date = date(date('m/Y', strtotime($row_duration['end_date'])));
                        }
                    
                        $durations .= "{$start_date} - {$end_date}<br />";
                    }
                    
                    $work_history_row['durations'] = "{$durations}<br />{$durations_edit_url}";
                }
                else {
                    $work_history_row['durations'] = $durations_edit_url;
                }
                
                $tasks_edit_url = "<a href=\"{$tasks_page_url}\">Edit Tasks</a>";
                $work_history_row['tasks'] = $tasks_edit_url;
                
                $work_history_table->addRow($work_history_row);
            }
        }
        
        $this->body->addChild($work_history_table, 'current_menu_content');
    }
}