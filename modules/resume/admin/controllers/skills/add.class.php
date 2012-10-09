<?php
/**
* The management page for the user skills of the Online Resume module.
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
namespace Modules\Resume\Admin\Controllers\Skills;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Add
extends Home {
    protected $title = "Add/Edit a Skill";
    
    protected $active_sub_nav_link = 'Add/Edit';
    
    protected $skill_categories;

    public function __construct() {
        parent::__construct();
        
        $this->skill_categories = db()->getMappedColumn("
            SELECT 
                skill_category_id, 
                skill_category_name
            FROM resume_skill_categories
            ORDER BY sort_order ASC
        ");
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('skill-add', array(), 'resume');
    }
    
    protected function constructRightContent() {
        if(!empty($this->skill_categories)) {       
            $this->page->body->addChild($this->getForm(), 'current_menu_content');
        }
        else {
            $skill_category_edit_url = Http::getLowerLevelPageUrl(array('categories'), 'manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Categories', 'prerequisite');
            $required_template->addChild('Skills', 'context');
            $required_template->addChild($skill_category_edit_url, 'prerequisite_url');
            
            $this->page->body->addChild($required_template, 'current_menu_content');
        }
    }
    
    protected function getForm() {
        $skills_form = new EditTableForm('skills', 'resume_skills', 'skill_id', 'sort_order', array('skill_category_id'));
            
        $skills_form->setTitle('Add a New Skill');
        
        $proficiency_levels = db()->getMappedColumn("
            SELECT
                proficiency_level_id,
                proficiency_level_name
            FROM resume_proficiency_levels
            ORDER BY proficiency_level_id
        ");
        
        $skills_form->addTextbox('skill_name', 'Skill Name');
        $skills_form->addDropdown('skill_category_id', 'Skill Category', $this->skill_categories)->addBlankOption();
        $skills_form->addIntField('years_proficient', 'Years Proficient');
        $skills_form->addDropdown('proficiency_level_id', 'Proficiency Level', $proficiency_levels)->addBlankOption();
        $skills_form->addSubmit('save', 'Save');
        
        $skills_form->setRequiredFields(array(
            'skill_name',
            'skill_category_id',
            'years_proficient',
            'proficiency_level_id'
        ));
        
        $skills_form->processForm();
        
        return $skills_form;
    }
}