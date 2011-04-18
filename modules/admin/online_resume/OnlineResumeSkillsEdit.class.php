<?php
/**
* The management page for the user skills of the Online Resume module.
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
class OnlineResumeSkillsEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Skills Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Skills Edit</h2><br />');
        
        $skill_categories = db()->getAll("
            SELECT skill_category_id, skill_category_name
            FROM online_resume.skill_categories
            ORDER BY sort_order ASC
        ");
        
        if(!empty($skill_categories)) {
            $skill_category_filters = array();
            
            foreach($skill_categories as $skill_category) {
                $skill_category_filters[$skill_category['skill_category_name']] = array('skill_category_id' => $skill_category['skill_category_id']);
            }

            //The education history table
            $skills_edit_table = new EditTableForm(
                'skills',
                'online_resume.skills',
                'skill_id',
                'sort_order',
                array(),
                array('Skill Category' => $skill_category_filters)
            );
            
            $skills_edit_table->setNumberOfColumns(4);
        
            $skills_edit_table->addHeader(array(
                'skill_name' => 'Skill Name',
                'Skill Category',
                'years_proficient' => 'Years Proficient',
                'proficiency_level_id' => 'Proficiency Level'
            ));
            
            if($skills_edit_table->getFormVisibility()) {                
                //Retrieve and format the proficiency level to something readable by the dropdown object
                $proficiency_levels = db()->getAll("
                    SELECT
                        proficiency_level_id,
                        proficiency_level_name
                    FROM online_resume.proficiency_levels
                    ORDER BY proficiency_level_id
                ");
                
                $proficiency_levels_options = array();
                
                foreach($proficiency_levels as $proficiency_level) {
                    $proficiency_levels_options[$proficiency_level['proficiency_level_id']] = $proficiency_level['proficiency_level_name'];
                }
            
                $skills_edit_table->addTextbox('skill_name', 'Skill Name');
                $skills_edit_table->addIntField('years_proficient', 'Years Proficient');
                $skills_edit_table->addDropdown('proficiency_level_id', 'Proficiency Level', $proficiency_levels_options)->addBlankOption();
                $skills_edit_table->addSubmit('save', 'Save');
                
                $skills_edit_table->setRequiredFields(array(
                    'skill_name',
                    'years_proficient',
                    'proficiency_level_id'
                ));
                
                $skills_edit_table->processForm();
             }
            
            $skills_edit_table->useQuery("
                SELECT
                    s.skill_id,
                    s.skill_name,
                    sc.skill_category_name,
                    s.years_proficient,
                    pl.proficiency_level_name
                FROM online_resume.skills s
                JOIN online_resume.skill_categories sc USING (skill_category_id)
                JOIN online_resume.proficiency_levels pl USING (proficiency_level_id)
                ORDER BY s.sort_order
            ");
            
            $content->addChild($skills_edit_table);
        }
        else {
            $skill_category_edit_url = Http::getPageBaseUrl() . 'OnlineResumeSkillCategoriesEdit';
        
            $content->addParagraph("
                Skill categories need to be added before adding skills. Go <a href=\"{$skill_category_edit_url}\">here</a> to manage skill categories.
            ");
        }
        
        $this->body->addChild($content);
	}
}