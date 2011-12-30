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

namespace Modules\Admin\Resume;

use \Framework\Html\Misc\Div;
use \Framework\Html\Table\EditTableForm;
use \Framework\Utilities\Http;

class CodeExamplesEdit
extends Home {
    protected $title = "Resume Code Samples Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Code Samples Edit'] = Http::getCurrentBaseUrl() . 'code-examples-edit';
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Code Examples Edit</h2><br />');

        $code_examples_table = new EditTableForm(
            'code_examples',
            'resume_code_examples',
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
        
        if($code_examples_table->getFormVisibility()) {
            //Retrieve all work history information
            $work_history = db()->getMappedColumn("
                SELECT 
                    work_history_id,
                    organization_name
                FROM resume_work_history
                ORDER BY sort_order ASC
            ");
        
            //Retrieve all portfolio projects
            $portfolio_projects = db()->getMappedColumn("
                SELECT
                    portfolio_project_id,
                    project_name
                FROM resume_portfolio_projects
                ORDER BY sort_order ASC
            ");
        
            $code_examples_table->addTextbox('code_example_name', 'Name');
            $code_examples_table->addDropdown('work_history_id', 'Organization', $work_history)->addBlankOption();
            $code_examples_table->addDropdown('portfolio_project_id', 'Portfolio Project', $portfolio_projects)->addBlankOption();
            $code_examples_table->addTextArea('purpose', 'Purpose');
            $code_examples_table->addTextArea('description', 'Description');
            $code_examples_table->addSubmit('save', 'Save');
            
            $code_examples_table->setRequiredFields(array('code_example_name', 'purpose'));
            
            $code_examples_table->processForm();
        }
        
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
            $source_file_edit_url = Http::getCurrentBaseUrl() . 'code-example-file-edit';
            $skills_used_edit_url = Http::getCurrentBaseUrl() . 'code-example-skills-edit';
        
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
        
        $content->addChild($code_examples_table);
        
        $this->body->addChild($content);
    }
}