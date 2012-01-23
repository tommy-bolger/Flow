<?php
/**
* The management page for the user portfolio of the Online Resume module.
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

class PortfolioEdit
extends Home {
    protected $title = "Resume Portfolio Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Portfolio Edit'] = Http::getCurrentLevelPageUrl('portfolio-edit');
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Portfolio Edit</h2><br />');

        $portfolio_table = new EditTableForm(
            'portfolio',
            'resume_portfolio_projects',
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
                FROM resume_work_history
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
                NULL AS skills_used,
                pp.site_url,
                pp.description,
                pp.involvement_description
            FROM resume_portfolio_projects pp
            LEFT JOIN resume_work_history wh USING (work_history_id)
            ORDER BY pp.sort_order ASC
        ");
        
        $portfolio_project_skills = db()->getGroupedColumn("
            SELECT
                pps.portfolio_project_id, 
                s.skill_name
            FROM resume_portfolio_project_skills pps
            JOIN resume_skills s USING (skill_id)
            ORDER BY pps.sort_order ASC
        ");
        
        if(!empty($portfolio_projects)) {
            $screenshot_edit_url = Http::getCurrentLevelPageUrl('portfolio-images-edit');
            $skills_used_edit_url = Http::getCurrentLevelPageUrl('portfolio-skills-edit');
        
            foreach($portfolio_projects as $portfolio_project) {
                $portfolio_project_id = $portfolio_project['portfolio_project_id'];
            
                $screenshot_edit_link = "<a href=\"{$screenshot_edit_url}&portfolio_project_id={$portfolio_project_id}\">Edit Images</a>";
            
                $portfolio_project['images'] = $screenshot_edit_link;
                
                $skills_used_edit_link = "<a href=\"{$skills_used_edit_url}&portfolio_project_id={$portfolio_project_id}\">Edit Skills Used</a>";
            
                if(!empty($portfolio_project_skills[$portfolio_project_id])) {
                    $portfolio_project['skills_used'] = implode('<br />', $portfolio_project_skills[$portfolio_project_id]) . "<br /><br />{$skills_used_edit_link}";
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