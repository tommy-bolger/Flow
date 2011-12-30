<?php
/**
* The management page for the skills used in a user code example of the Online Resume module.
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
class OnlineResumeCodeExampleSkillsEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Code Example Skills Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $available_skills = db()->getAll("
            SELECT 
                sc.skill_category_name,
                s.skill_id,
                s.skill_name
            FROM online_resume.skill_categories sc
            JOIN online_resume.skills s USING (skill_category_id)
            ORDER BY sc.sort_order, s.sort_order
        ");

        if(!empty($available_skills)) {
            $code_example_skills_table = new EditTableForm(
                'code_example_skills',
                'online_resume.code_example_skills',
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
                FROM online_resume.code_example_skills ces
                JOIN online_resume.skills s USING (skill_id)
                ORDER BY ces.sort_order ASC
            ");
            
            //Get the project name these skills are linked to.
            $code_example_id = request()->get->code_example_id;
            
            $code_example_name = db()->getOne("
                SELECT code_example_name
                FROM online_resume.code_examples
                WHERE code_example_id = ?
            ", array($code_example_id));
            
            $code_example_edit_page_url = Http::getPageBaseUrl() . "OnlineResumeCodeExamplesEdit";
            
            $content = new Div(array('id' => 'current_menu_content'), "
                <h2>Editing Skills for Code Example {$code_example_name}</h2><br />
                <a href=\"{$code_example_edit_page_url}\"><- Return to Code Examples</a><br /><br />
            ");
            
            $content->addChild($code_example_skills_table);
        }
        else {
            $skills_edit_page_url = Http::getPageBaseUrl() . 'OnlineResumeSkillsEdit';
        
            $content->addParagraph("
                Skills need to be added to your resume before selecting for this code examples. Go <a href=\"{$skills_edit_page_url}\">here</a> to manage resume skills.
            ");
        }
        
        $this->body->addChild($content);
    }
}