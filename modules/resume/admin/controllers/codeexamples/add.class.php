<?php
/**
* The management page for a code example of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\CodeExamples;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit a Code Example";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }

    protected function constructRightContent() {    
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        $code_examples_form = new EditTableForm(
            'code_examples',
            'resume_code_examples',
            'code_example_id',
            'sort_order'
        );
        
        $code_examples_form->setTitle('Add a new Code Example');

        //Retrieve all work history information
        $work_history = db()->getMappedColumn("
            SELECT 
                work_history_id,
                organization_name
            FROM resume_work_history
            ORDER BY sort_order ASC
        ");
    
        //Retrieve all portfolio projects
        $portfolio_projects = db()->getMappedColumn("
            SELECT
                portfolio_project_id,
                project_name
            FROM resume_portfolio_projects
            ORDER BY sort_order ASC
        ");
    
        $code_examples_form->addTextbox('code_example_name', 'Name');
        $code_examples_form->addDropdown('work_history_id', 'Organization', $work_history)->addBlankOption();
        $code_examples_form->addDropdown('portfolio_project_id', 'Portfolio Project', $portfolio_projects)->addBlankOption();
        $code_examples_form->addTextArea('purpose', 'Purpose');
        $code_examples_form->addTextArea('description', 'Description');
        $code_examples_form->addSubmit('save', 'Save');
        
        $code_examples_form->setRequiredFields(array('code_example_name', 'purpose'));
        
        $code_examples_form->processForm();
        
        return $code_examples_form;
    }
}