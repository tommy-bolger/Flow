<?php
/**
* The management page for the skills used in a user code example of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\CodeExamples\Skills;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Add
extends Home {
    protected $title = "Add/Edit a Code Example Skill";
    
    protected $active_sub_nav_link = "Add/Edit";
    
    protected $code_examples;

    public function __construct() {
        parent::__construct();
        
        $this->code_examples = db()->getMappedColumn("
            SELECT
                code_example_id,
                code_example_name
            FROM resume_code_examples
            ORDER BY sort_order
        ");
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links["Add/Edit"] = Http::getCurrentLevelPageUrl('add', array(), 'resume');
    }
    
    protected function constructRightContent() {
        if(!empty($this->code_examples)) {
            $this->page->body->addChild($this->getForm(), 'current_menu_content');
        }
        else {
            $code_example_url = Http::getHigherLevelPageUrl('manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Code Examples', 'prerequisite');
            $required_template->addChild('Skills', 'context');
            $required_template->addChild($code_example_url, 'prerequisite_url');
            
            $this->page->body->addChild($required_template, 'current_menu_content');
        }
    }
    
    protected function getForm() {
        $code_example_skills_form = new EditTableForm(
            'code_example_skills',
            'resume_code_example_skills',
            'code_example_skill_id',
            'sort_order',
            array('code_example_id')
        );
        
        $code_example_skills_form->setTitle("Add a Code Example Skill");
        
        $available_skills = db()->getAll("
            SELECT 
                sc.skill_category_name,
                s.skill_id,
                s.skill_name
            FROM resume_skill_categories sc
            JOIN resume_skills s USING (skill_category_id)
            ORDER BY sc.sort_order, s.sort_order
        ");
        
        $skills = array();
    
        foreach($available_skills as $available_skill) {
            $skills[$available_skill['skill_category_name']][$available_skill['skill_id']] = $available_skill['skill_name'];
        }
    
        $code_example_skills_form->addDropdown('code_example_id', 'Code Example', $this->code_examples)->addBlankOption();
        $code_example_skills_form->addDropdown('skill_id', 'Skill', $skills)->addBlankOption();
        $code_example_skills_form->addSubmit('save', 'Save');
        
        $code_example_skills_form->setRequiredFields(array(
            'code_example_id',
            'skill_id'
        ));
        
        $code_example_skills_form->processForm();
        
        return $code_example_skills_form;
    }
}