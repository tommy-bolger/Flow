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

namespace Modules\Resume\Admin\Portfolio\Skills;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Project Skills";
    
    protected $active_sub_nav_link = "Manage";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {
        $page_filter = array();
        $filter_dropdown = array();
    
        if(!empty(request()->get->portfolio_project_id)) {
            $page_filter = array('portfolio_project_id');
        }
        else {
            $portfolio_projects = db()->getAll("
                SELECT
                    portfolio_project_id,
                    project_name
                FROM resume_portfolio_projects
                ORDER BY sort_order
            ");
            
            if(!empty($portfolio_projects)) {
                $dropdown_options = array();
            
                foreach($portfolio_projects as $portfolio_project) {
                    $dropdown_options[$portfolio_project['project_name']] = array(
                        'portfolio_project_id' => $portfolio_project['portfolio_project_id']
                    ); 
                }
                
                $filter_dropdown = array('Select a Portfolio Project' => $dropdown_options);
            }
        }
    
        $portfolio_skills_table = new EditTable(
            'portfolio_skills',
            'resume_portfolio_project_skills',
            'add',
            'portfolio_project_skill_id',
            'sort_order',
            $page_filter,
            $filter_dropdown
        );
        
        $portfolio_skills_table->setNumberOfColumns(1);
        
        $portfolio_skills_table->addHeader(array(
            'skill_id' => 'Used Skills'
        ));
        
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
        
        $this->body->addChild($portfolio_skills_table, 'current_menu_content');
    }
}