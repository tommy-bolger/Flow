<?php
/**
* The management page for the user skill categories of the Online Resume module.
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
class OnlineResumeSkillCategoriesEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Skill Categories Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Skill Categories Edit</h2><br />');
        
        //The skill_category table
        $skill_category_edit_table = new EditTableForm(
            'skill_categories', 
            'online_resume.skill_categories',
            'skill_category_id',
            'sort_order'
        );
        
        $skill_category_edit_table->setNumberOfColumns(1);
        
        $skill_category_edit_table->addHeader(array(
            'skill_category_name' => 'Skill Category Name'
        ));
        
        if($skill_category_edit_table->getFormVisibility()) {
            $skill_category_edit_table->addTextbox('skill_category_name', 'Category Name')->setRequired();     
            $skill_category_edit_table->addSubmit('save', 'Save');
            
            $skill_category_edit_table->processForm();
        }
        
        $skill_category_edit_table->useQuery("
            SELECT
                skill_category_id,
                skill_category_name
            FROM online_resume.skill_categories
        ");
        
        $content->addChild($skill_category_edit_table);
        
        $this->body->addChild($content);
	}
}