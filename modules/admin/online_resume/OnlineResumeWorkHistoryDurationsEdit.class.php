<?php
/**
* The management page for the durations of a user work history item of the Online Resume module.
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
class OnlineResumeWorkHistoryDurationsEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Work History Durations Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $work_history_durations_table = new EditTableForm(
            'work_history_durations',
            'online_resume.work_history_durations',
            'work_history_duration_id',
            'sort_order',
            array('work_history_id')
        );
        
        $work_history_durations_table->setNumberOfColumns(2);
        
        $work_history_durations_table->addHeader(array(
            'start_date' => 'Start Date',
            'end_date' => 'End Date'
        ));
        
        if($work_history_durations_table->getFormVisibility()) {
            $work_history_durations_table->addDateField('start_date', 'Start Date');
            $work_history_durations_table->addDateField('end_date', 'End Date');
            $work_history_durations_table->addSubmit('save', 'Save');
            
            $work_history_durations_table->setRequiredFields(array('start_date'));
            
            $work_history_durations_table->processForm();
        }
        
        $work_history_durations_table->useQuery("
            SELECT
                to_char(start_date, 'MM/YYYY'),
                CASE
                    WHEN end_date IS NOT NULL THEN to_char(end_date, 'MM/YYYY')
                    ELSE 'Present'
                END AS end_date,
                work_history_duration_id
            FROM online_resume.work_history_durations
            ORDER BY sort_order ASC
        ");
        
        //Get the organization name of the work history record these durations are linked to.
        $work_history_id = request()->get->work_history_id;
        
        $organization_name = db()->getOne("
            SELECT organization_name
            FROM online_resume.work_history
            WHERE work_history_id = ?
        ", array($work_history_id));
        
        //Set the header of the right content for this page with the organization name
        $work_history_page_url = Http::getPageBaseUrl() . "OnlineResumeWorkHistoryEdit";
        
        $content = new Div(array('id' => 'current_menu_content'), "
            <h2>Editing Work History Durations for {$organization_name}</h2><br />
            <a href=\"{$work_history_page_url}\"><- Return to Work History Page</a><br /><br />
        ");
        
        $content->addChild($work_history_durations_table);
        
        $this->body->addChild($content);
	}
}