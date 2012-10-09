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

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Add
extends Home {
    protected $title = "Add/Edit an Organization Task";
    
    protected $active_sub_nav_link = "Add/Edit";
    
    protected $organizations;

    public function __construct() {
        parent::__construct();
        
        $this->organizations = db()->getConcatMappedColumn("
            SELECT
                work_history_id,
                job_title,
                organization_name
            FROM resume_work_history
            ORDER BY sort_order
        ", ', ');
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links["Add/Edit"] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }
    
    protected function constructRightContent() {
        if(!empty($this->organizations)) {        
            $this->page->body->addChild($this->getForm(), 'current_menu_content');
        }
        else {
            $organization_manage_url = Http::getHigherLevelPageUrl('manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Organizations', 'prerequisite');
            $required_template->addChild('Tasks', 'context');
            $required_template->addChild($organization_manage_url, 'prerequisite_url');
            
            $this->page->body->addChild($required_template, 'current_menu_content');
        }
    }
    
    protected function getForm() {
        $work_history_tasks_form = new EditTableForm(
            'work_history_tasks',
            'resume_work_history_tasks',
            'work_history_task_id',
            'sort_order',
            array('work_history_id')
        );
        
        $work_history_tasks_form->setTitle('Add a New Task');

        $work_history_tasks_form->addDropdown('work_history_id', 'Organization', $this->organizations)->addBlankOption();
        $work_history_tasks_form->addTextArea('description', 'Task Description');
        $work_history_tasks_form->addSubmit('save', 'Save');
        
        $work_history_tasks_form->setRequiredFields(array(
            'work_history_id', 
            'description'
        ));
        
        $work_history_tasks_form->processForm();
        
        return $work_history_tasks_form;
    }
}