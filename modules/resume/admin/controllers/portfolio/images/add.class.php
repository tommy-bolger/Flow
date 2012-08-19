<?php
/**
* The management page for the images of a portfolio project in the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\Portfolio\Images;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Add
extends Home {
    protected $title = "Add/Edit a Project Image";
    
    protected $active_sub_nav_link = "Add/Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links["Add/Edit"] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }
    
    protected function constructRightContent() {
        $portfolio_projects = db()->getMappedColumn("
            SELECT
                portfolio_project_id,
                project_name
            FROM resume_portfolio_projects
            ORDER BY sort_order
        ");

        if(!empty($portfolio_projects)) {
            $portfolio_images_form = new EditTableForm(
                'portfolio_images',
                'resume_portfolio_project_images',
                'portfolio_project_image_id',
                'sort_order',
                array('portfolio_project_id')
            );
            
            $portfolio_images_form->setTitle("Add a New Project Image");
    
            $portfolio_images_form->addDropdown('portfolio_project_id', 'Portfolio Project', $portfolio_projects)->addBlankOption();
            $portfolio_images_form->addTextbox('title', 'Title');
            $portfolio_images_form->addTextArea('description', 'Description');
            $portfolio_images_form->addSubmit('save', 'Save');
    
            $portfolio_images_form->setRequiredFields(array(
                'portfolio_project_id',
                'title'
            ));
            
            $portfolio_images_form->processForm();
            
            $this->body->addChild($portfolio_images_form, 'current_menu_content');
        }
        else {
            $project_edit_page_url = Http::getHigherLevelPageUrl('manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Projects', 'prerequisite');
            $required_template->addChild('Project Images', 'context');
            $required_template->addChild($project_edit_page_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}