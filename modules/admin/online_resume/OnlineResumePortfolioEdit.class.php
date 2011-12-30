<?php
/**
* The management page for the user portfolio of the Online Resume module.
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
class OnlineResumePortfolioEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Portfolio Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Portfolio Edit</h2><br />');

        $portfolio_table = new EditTableForm(
            'portfolio',
            'online_resume.portfolio_projects',
            'portfolio_project_id',
            'sort_order'
        );
        
        $portfolio_table->setNumberOfColumns(7);
        
        $portfolio_table->addHeader(array(
            'project_name' => 'Project Name',
            'work_history_id' => 'Organization',
            'Images',
            'Skills Used',
            'site_url' => 'Site URL',
            'description' => 'Description',
            'involvement_description' => 'Involvement'
        ));
        
        if($portfolio_table->getFormVisibility()) {
            //Retrieve all work history records
            $work_history = db()->getMappedColumn("
                SELECT 
                    work_history_id,
                    organization_name
                FROM online_resume.work_history
            ");
        
            $portfolio_table->addTextbox('project_name', 'Project Name');
            $portfolio_table->addDropdown('work_history_id', 'Organization', $work_history)->addBlankOption();
            $portfolio_table->addTextbox('site_url', 'Site URL');
            $portfolio_table->addTextArea('description', 'Description');
            $portfolio_table->addTextArea('involvement_description', 'Involvement');
            $portfolio_table->addSubmit('save', 'Save');
            
            $portfolio_table->setRequiredFields(array('project_name', 'description'));
            
            $portfolio_table->processForm();
        }
        
        $portfolio_projects = db()->getAll("
            SELECT
                pp.portfolio_project_id,
                pp.project_name,
                wh.organization_name,
                NULL AS images,
                array_to_string(array(
                    SELECT s1.skill_name
                    FROM online_resume.portfolio_project_skills pps1
                    JOIN online_resume.skills s1 USING (skill_id)
                    WHERE pps1.portfolio_project_id = pp.portfolio_project_id
                    ORDER BY pps1.sort_order ASC
                ), ',<br />') AS skills_used,
                pp.site_url,
                pp.description,
                pp.involvement_description
            FROM online_resume.portfolio_projects pp
            LEFT JOIN online_resume.work_history wh USING (work_history_id)
            ORDER BY pp.sort_order ASC
        ");
        
        if(!empty($portfolio_projects)) {
            $screenshot_edit_url = Http::getPageBaseUrl() . 'OnlineResumePortfolioImagesEdit';
            $skills_used_edit_url = Http::getPageBaseUrl() . 'OnlineResumePortfolioSkillsEdit';
        
            foreach($portfolio_projects as $portfolio_project) {            
                $screenshot_edit_link = "<a href=\"{$screenshot_edit_url}&portfolio_project_id={$portfolio_project['portfolio_project_id']}\">Edit Images</a>";
            
                $portfolio_project['images'] = $screenshot_edit_link;
                
                $skills_used_edit_link = "<a href=\"{$skills_used_edit_url}&portfolio_project_id={$portfolio_project['portfolio_project_id']}\">Edit Skills Used</a>";
            
                if(!empty($portfolio_project['skills_used'])) {
                    $portfolio_project['skills_used'] .= "<br /><br />{$skills_used_edit_link}";
                }
                else {
                    $portfolio_project['skills_used'] = $skills_used_edit_link;
                }
                
                $portfolio_project['description'] = substr($portfolio_project['description'], 0, 100) . '...';
                
                $portfolio_project['involvement_description'] = substr($portfolio_project['involvement_description'], 0, 100) . '...';
                
                $portfolio_table->addRow($portfolio_project);
            }
        }
        
        $content->addChild($portfolio_table);
        
        $this->body->addChild($content);
    }
}