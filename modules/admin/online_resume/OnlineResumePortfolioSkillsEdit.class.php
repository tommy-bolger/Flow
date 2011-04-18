<?php
/**
* The management page for the skills used in a user portfolio project of the Online Resume module.
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
class OnlineResumePortfolioSkillsEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Portfolio Project Skills Edit";

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
            $portfolio_skills_table = new EditTableForm(
                'portfolio_skills',
                'online_resume.portfolio_project_skills',
                'portfolio_project_skill_id',
                'sort_order',
                array('portfolio_project_id')
            );
            
            $portfolio_skills_table->setNumberOfColumns(1);
            
            $portfolio_skills_table->addHeader(array(
                'skill_id' => 'Used Skills'
            ));
            
            if($portfolio_skills_table->getFormVisibility()) {
                $skills = array();
            
                foreach($available_skills as $available_skill) {
                    $skills[$available_skill['skill_category_name']][$available_skill['skill_id']] = $available_skill['skill_name'];
                }
            
                $portfolio_skills_table->addDropdown('skill_id', 'Skill', $skills)->addBlankOption();
                $portfolio_skills_table->addSubmit('save', 'Save');
                
                $portfolio_skills_table->setRequiredFields(array('skill_id'));
                
                $portfolio_skills_table->processForm();
            }
            
            $portfolio_skills_table->useQuery("
                SELECT
                    pps.portfolio_project_skill_id,
                    s.skill_name
                FROM online_resume.portfolio_project_skills pps
                JOIN online_resume.skills s USING (skill_id)
                ORDER BY pps.sort_order ASC
            ");
            
            //Get the project name these skills are linked to.
            $portfolio_project_id = request()->get->portfolio_project_id;
            
            $project_name = db()->getOne("
                SELECT project_name
                FROM online_resume.portfolio_projects
                WHERE portfolio_project_id = ?
            ", array($portfolio_project_id));
            
            $portfolio_edit_page_url = Http::getPageBaseUrl() . "OnlineResumePortfolioEdit";
            
            $content = new Div(array('id' => 'current_menu_content'), "
                <h2>Editing Skills for Portfolio Project {$project_name}</h2><br />
                <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Projects</a><br /><br />
            ");
            
            $content->addChild($portfolio_skills_table);
        }
        else {
            $skills_edit_page_url = Http::getPageBaseUrl() . 'OnlineResumeSkillsEdit';
        
            $content->addParagraph("
                Skills need to be added to your resume before selecting for this portfolio project. Go <a href=\"{$skills_edit_page_url}\">here</a> to manage resume skills.
            ");
        }
        
        $this->body->addChild($content);
	}
}