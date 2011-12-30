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

namespace Modules\Admin\Resume;

use \Framework\Html\Misc\Div;
use \Framework\Html\Table\EditTableForm;
use \Framework\Utilities\Http;

class WorkHistoryTasksEdit
extends WorkHistoryEdit {
    protected $title = "Resume Work History Tasks Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Work History Tasks Edit'] = Http::getCurrentBaseUrl() . 'work-history-tasks-edit';
    }
    
    protected function constructRightContent() {
        $work_history_tasks_table = new EditTableForm(
            'work_history_tasks',
            'resume_work_history_tasks',
            'work_history_task_id',
            'sort_order',
            array('work_history_id')
        );
        
        $work_history_tasks_table->setNumberOfColumns(1);
        
        $work_history_tasks_table->addHeader(array(
            'description' => 'Task Description'
        ));
        
        if($work_history_tasks_table->getFormVisibility()) {
            $work_history_tasks_table->addTextArea('description', 'Task Description');
            $work_history_tasks_table->addSubmit('save', 'Save');
            
            $work_history_tasks_table->setRequiredFields(array('description'));
            
            $work_history_tasks_table->processForm();
        }
        
        $work_history_tasks_table->useQuery("
            SELECT
                description,
                work_history_task_id
            FROM resume_work_history_tasks
            ORDER BY sort_order ASC
        ");
        
        //Get the organization name of the work history record these tasks are linked to.
        $work_history_id = request()->get->work_history_id;
        
        $organization_name = db()->getOne("
            SELECT organization_name
            FROM resume_work_history
            WHERE work_history_id = ?
        ", array($work_history_id));
        
        //Set the header of the right content for this page with the organization name
        $work_history_page_url = Http::getCurrentBaseUrl() . "work-history-edit";
        
        $content = new Div(array('id' => 'current_menu_content'), "
            <h2>Editing Work History Tasks for {$organization_name}</h2><br />
            <a href=\"{$work_history_page_url}\"><- Return to Work History Page</a><br /><br />
        ");
        
        $content->addChild($work_history_tasks_table);
        
        $this->body->addChild($content);
    }
}