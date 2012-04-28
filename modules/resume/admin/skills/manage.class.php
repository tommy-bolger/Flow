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
namespace Modules\Resume\Admin\Skills;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Manage
extends Home {
    protected $title = "Manage Skills";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('skills-edit', array(), 'resume');
    }
    
    protected function constructRightContent() {            
        $skill_categories = db()->getMappedColumn("
            SELECT 
                skill_category_id, 
                skill_category_name
            FROM resume_skill_categories
            ORDER BY sort_order ASC
        ");
        
        if(!empty($skill_categories)) {
            $skill_category_filters = array();
            
            foreach($skill_categories as $skill_category_id => $skill_category_name) {
                $skill_category_filters[$skill_category_name] = array('skill_category_id' => $skill_category_id);
            }

            //The education history table
            $skills_edit_table = new EditTable(
                'skills',
                'resume_skills',
                'add',
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
            
            $skills_edit_table->useQuery("
                SELECT
                    s.skill_id,
                    s.skill_name,
                    sc.skill_category_name,
                    s.years_proficient,
                    pl.proficiency_level_name
                FROM resume_skills s
                JOIN resume_skill_categories sc USING (skill_category_id)
                JOIN resume_proficiency_levels pl USING (proficiency_level_id)
                ORDER BY s.sort_order
            ");
            
            $this->body->addChild($skills_edit_table, 'current_menu_content');
        }
        else {
            $skill_category_edit_url = Http::getLowerLevelPageUrl(array('categories'), 'manage', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Skill Categories', 'prerequisite');
            $required_template->addChild('Skills', 'context');
            $required_template->addChild($skill_category_edit_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}