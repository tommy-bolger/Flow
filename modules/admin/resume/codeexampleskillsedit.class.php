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

namespace Modules\Admin\Resume;

use \Framework\Html\Misc\Div;
use \Framework\Html\Table\EditTableForm;
use \Framework\Utilities\Http;

class CodeExampleSkillsEdit
extends CodeExamplesEdit {
    protected $title = "Resume Code Example Skills Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Code Example Skills Edit'] = Http::getCurrentBaseUrl() . 'code-example-skills-edit';
    }
    
    protected function constructRightContent() {
        $available_skills = db()->getAll("
            SELECT 
                sc.skill_category_name,
                s.skill_id,
                s.skill_name
            FROM resume_skill_categories sc
            JOIN resume_skills s USING (skill_category_id)
            ORDER BY sc.sort_order, s.sort_order
        ");

        if(!empty($available_skills)) {
            $code_example_skills_table = new EditTableForm(
                'code_example_skills',
                'resume_code_example_skills',
                'code_example_skill_id',
                'sort_order',
                array('code_example_id')
            );
            
            $code_example_skills_table->setNumberOfColumns(1);
            
            $code_example_skills_table->addHeader(array(
                'skill_id' => 'Skills Used'
            ));
            
            if($code_example_skills_table->getFormVisibility()) {
                $skills = array();
            
                foreach($available_skills as $available_skill) {
                    $skills[$available_skill['skill_category_name']][$available_skill['skill_id']] = $available_skill['skill_name'];
                }
            
                $code_example_skills_table->addDropdown('skill_id', 'Skill', $skills)->addBlankOption();
                $code_example_skills_table->addSubmit('save', 'Save');
                
                $code_example_skills_table->setRequiredFields(array('skill_id'));
                
                $code_example_skills_table->processForm();
            }
            
            $code_example_skills_table->useQuery("
                SELECT
                    ces.code_example_skill_id,
                    s.skill_name
                FROM resume_code_example_skills ces
                JOIN resume_skills s USING (skill_id)
                ORDER BY ces.sort_order ASC
            ");
            
            //Get the project name these skills are linked to.
            $code_example_id = request()->get->code_example_id;
            
            $code_example_name = db()->getOne("
                SELECT code_example_name
                FROM resume_code_examples
                WHERE code_example_id = ?
            ", array($code_example_id));
            
            $code_example_edit_page_url = Http::getCurrentBaseUrl() . "code-examples-edit";
            
            $content = new Div(array('id' => 'current_menu_content'), "
                <h2>Editing Skills for Code Example {$code_example_name}</h2><br />
                <a href=\"{$code_example_edit_page_url}\"><- Return to Code Examples</a><br /><br />
            ");
            
            $content->addChild($code_example_skills_table);
        }
        else {
            $skills_edit_page_url = Http::getCurrentBaseUrl() . 'skills-edit';
        
            $content->addChild("
                <p>
                    Skills need to be added to your resume before selecting for this code examples. Go <a href=\"{$skills_edit_page_url}\">here</a> to manage resume skills.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}