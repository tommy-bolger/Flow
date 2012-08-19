<?php
/**
* The management page for the user portfolio of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\Portfolio;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit a Portfolio Project";
    
    protected $active_sub_nav_link = "Add/Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }
    
    protected function constructRightContent() {    
        $portfolio_table = new EditTableForm(
            'portfolio',
            'resume_portfolio_projects',
            'portfolio_project_id',
            'sort_order'
        );
        
        $portfolio_table->setTitle('Add a New Portfolio Item');
        
        //Retrieve all work history records
        $work_history = db()->getMappedColumn("
            SELECT 
                work_history_id,
                organization_name
            FROM resume_work_history
        ");
    
        $portfolio_table->addTextbox('project_name', 'Project Name');
        $portfolio_table->addDropdown('work_history_id', 'Organization', $work_history)->addBlankOption();
        $portfolio_table->addTextbox('site_url', 'Site URL');
        $portfolio_table->addTextArea('description', 'Description');
        $portfolio_table->addTextArea('involvement_description', 'Involvement');
        $portfolio_table->addSubmit('save', 'Save');
        
        $portfolio_table->setRequiredFields(array(
            'project_name', 
            'description'
        ));
        
        $portfolio_table->processForm();
        
        $this->body->addChild($portfolio_table, 'current_menu_content');
    }
}