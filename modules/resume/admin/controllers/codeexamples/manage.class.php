<?php
/**
* The management page for a code example of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\CodeExamples;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Code Examples";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {    
        $code_examples_table = new EditTable(
            'code_examples',
            'resume_code_examples',
            'add',
            'code_example_id',
            'sort_order'
        );
        
        $code_examples_table->setNumberOfColumns(6);
        
        $code_examples_table->addHeader(array(
            'code_example_name' => 'Name',
            'Source File',
            'work_history_id' => 'Organization',
            'portfolio_project_id' => 'Portfolio Project',
            'Skills Used',
            'purpose' => 'Purpose',
            'description' => 'Description',
        ));
        
        $code_examples = db()->getAll("
            SELECT
                ce.code_example_id,
                ce.code_example_name,
                NULL AS source_file,
                wh.organization_name,
                pp.project_name,
                NULL AS skills_used,
                ce.purpose,
                ce.description
            FROM resume_code_examples ce
            LEFT JOIN resume_work_history wh ON wh.work_history_id = ce.work_history_id
            LEFT JOIN resume_portfolio_projects pp ON pp.portfolio_project_id = ce.portfolio_project_id
            ORDER BY ce.sort_order ASC
        ");
        
        $code_example_skills = db()->getGroupedColumn("
            SELECT
                ces.code_example_id, 
                s.skill_name
            FROM resume_code_example_skills ces
            JOIN resume_skills s USING (skill_id)
            ORDER BY ces.sort_order ASC
        ");
        
        if(!empty($code_examples)) {
            $source_file_edit_url = Http::getCurrentLevelPageUrl('change-source-code');
            $skills_used_edit_url = Http::getLowerLevelPageUrl(array('skills'), 'manage');
        
            foreach($code_examples as $code_example) {
                $code_example_id = $code_example['code_example_id'];
            
                $source_file_edit_link = "<a href=\"{$source_file_edit_url}&code_example_id={$code_example_id}\">Edit Source File</a>";
            
                $code_example['source_file'] = $source_file_edit_link;
                
                $skills_used_edit_link = "<a href=\"{$skills_used_edit_url}&code_example_id={$code_example_id}\">Edit Skills Used</a>";
            
                if(!empty($code_example_skills[$code_example_id])) {
                    $code_example['skills_used'] = implode('<br />', $code_example_skills[$code_example_id]) . "<br /><br />{$skills_used_edit_link}";
                }
                else {
                    $code_example['skills_used'] = $skills_used_edit_link;
                }
                
                $code_example['purpose'] = substr($code_example['purpose'], 0, 100) . '...';
                
                $code_example['description'] = substr($code_example['description'], 0, 100) . '...';
                
                $code_examples_table->addRow($code_example);
            }
        }
        
        $this->body->addChild($code_examples_table, 'current_menu_content');
    }
}