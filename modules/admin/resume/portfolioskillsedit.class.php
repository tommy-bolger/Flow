<?php
/**
* The management page for the skills used in a user portfolio project of the Online Resume module.
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

class PortfolioSkillsEdit
extends PortfolioEdit {
    protected $title = "Resume Portfolio Project Skills Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Portfolio Project Skills Edit'] = Http::getCurrentBaseUrl() . 'portfolio-skills-edit';
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
            $portfolio_skills_table = new EditTableForm(
                'portfolio_skills',
                'resume_portfolio_project_skills',
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
                FROM resume_portfolio_project_skills pps
                JOIN resume_skills s USING (skill_id)
                ORDER BY pps.sort_order ASC
            ");
            
            //Get the project name these skills are linked to.
            $portfolio_project_id = request()->get->portfolio_project_id;
            
            $project_name = db()->getOne("
                SELECT project_name
                FROM resume_portfolio_projects
                WHERE portfolio_project_id = ?
            ", array($portfolio_project_id));
            
            $portfolio_edit_page_url = Http::getCurrentBaseUrl() . "portfolio-edit";
            
            $content = new Div(array('id' => 'current_menu_content'), "
                <h2>Editing Skills for Portfolio Project {$project_name}</h2><br />
                <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Projects</a><br /><br />
            ");
            
            $content->addChild($portfolio_skills_table);
        }
        else {
            $skills_edit_page_url = Http::getCurrentBaseUrl() . 'skills-edit';
        
            $content->addChild("
                <p>
                    Skills need to be added to your resume before selecting for this portfolio project. Go <a href=\"{$skills_edit_page_url}\">here</a> to manage resume skills.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}