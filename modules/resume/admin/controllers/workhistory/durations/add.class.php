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

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Add
extends Home {
    protected $title = 'Add/Edit an Organization Duration';
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }
    
    protected function constructRightContent() {
        $organizations = db()->getConcatMappedColumn("
            SELECT
                work_history_id,
                job_title,
                organization_name
            FROM resume_work_history
            ORDER BY sort_order
        ", ', ');
        
        if(!empty($organizations)) {        
            $work_history_durations_form = new EditTableForm(
                'work_history_durations',
                'resume_work_history_durations',
                'work_history_duration_id',
                'sort_order',
                array('work_history_id')
            );
            
            $work_history_durations_form->setTitle('Add Durations for this Organization');
            
            $work_history_durations_form->setNumberOfColumns(2);
            
            $work_history_durations_form->addHeader(array(
                'start_date' => 'Start Date',
                'end_date' => 'End Date'
            ));
            
            $work_history_durations_form->addDropdown('work_history_id', 'Organization', $organizations)->addBlankOption();
            $work_history_durations_form->addDate('start_date', 'Start Date');
            $work_history_durations_form->addDate('end_date', 'End Date');
            $work_history_durations_form->addSubmit('save', 'Save');
            
            $work_history_durations_form->setRequiredFields(array(
                'work_history_id',
                'start_date'
            ));
            
            $work_history_durations_form->setDefaultValues(array(
                'work_history_id' => request()->get->work_history_id
            ));
            
            $work_history_durations_form->processForm();
            
            $this->body->addChild($work_history_durations_form, 'current_menu_content');
        }
        else {
            $organization_manage_url = Http::getHigherLevelPageUrl('manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Organizations', 'prerequisite');
            $required_template->addChild('Durations', 'context');
            $required_template->addChild($organization_manage_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}