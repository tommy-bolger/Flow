<?php
/**
* The management page for the user work history of the Online Resume module.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class OnlineResumeWorkHistoryEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Work History Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Work History Edit</h2><br />');
        
        $work_history_table = new EditTableForm(
            'work_history',
            'online_resume.work_history',
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
        
        if($work_history_table->getFormVisibility()) {
            $work_history_table->addTextbox('organization_name', 'Organization Name');
            $work_history_table->addTextbox('job_title', 'Job Title');
            $work_history_table->addSubmit('save', 'Save');
            
            $work_history_table->processForm();
        }
        
        $work_history_data = db()->getAll("
            SELECT
                wh.work_history_id,
                wh.organization_name,
                wh.job_title,
                array_to_string(array(
                    SELECT 
                        (
                            to_char(start_date, 'MM/YYYY') || ' - ' || 
                            CASE
                                WHEN end_date IS NOT NULL THEN to_char(end_date, 'MM/YYYY')
                                ELSE 'Present'
                            END
                        ) AS duration
                    FROM online_resume.work_history_durations
                    WHERE work_history_id = wh.work_history_id
                    ORDER BY sort_order ASC
                ), ',<br />') AS durations
            FROM online_resume.work_history wh
            ORDER BY wh.sort_order
        ");
        
        if(!empty($work_history_data)) {
            $durations_page_url = Http::getPageBaseUrl() . 'OnlineResumeWorkHistoryDurationsEdit';
            $tasks_page_url = Http::getPageBaseUrl() . 'OnlineResumeWorkHistoryTasksEdit';
        
            foreach($work_history_data as $work_history_row) {
                $durations_edit_url = "<a href=\"{$durations_page_url}&work_history_id={$work_history_row['work_history_id']}\">Edit Durations</a>";
            
                if(!empty($work_history_row['durations'])) {
                    $work_history_row['durations'] .= "<br /><br />{$durations_edit_url}";
                }
                else {
                    $work_history_row['durations'] = $durations_edit_url;
                }
                
                $tasks_edit_url = "<a href=\"{$tasks_page_url}&work_history_id={$work_history_row['work_history_id']}\">Edit Tasks</a>";
                $work_history_row['tasks'] = $tasks_edit_url;
                
                $work_history_table->addRow($work_history_row);
            }
        }
        
        $content->addChild($work_history_table);
        
        $this->body->addChild($content);
	}
}