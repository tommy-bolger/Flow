<?php
/**
* The management page for a code example of the Online Resume module.
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
class OnlineResumeCodeExamplesEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Code Samples Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Code Examples Edit</h2><br />');

        $code_examples_table = new EditTableForm(
            'code_examples',
            'online_resume.code_examples',
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
                FROM online_resume.work_history
                ORDER BY sort_order ASC
            ");
        
            //Retrieve all portfolio projects
            $portfolio_projects = db()->getMappedColumn("
                SELECT
                    portfolio_project_id,
                    project_name
                FROM online_resume.portfolio_projects
                ORDER BY sort_order ASC
            ");
        
            $code_examples_table->addTextbox('code_example_name', 'Name');
            $code_examples_table->addDropdown('work_history_id', 'Organization', $work_history)->addBlankOption();
            $code_examples_table->addDropdown('portfolio_project_id', 'Portfolio Project', $portfolio_projects)->addBlankOption();
            $code_examples_table->addTextArea('purpose', 'Purpose');
            $code_examples_table->addTextArea('description', 'Description');
            $code_examples_table->addSubmit('save', 'Save');
            
            $code_examples_table->processForm();
        }
        
        $code_examples = db()->getAll("
            SELECT
                ce.code_example_id,
                ce.code_example_name,
                NULL AS source_file,
                wh.organization_name,
                pp.project_name,
                array_to_string(array(
                    SELECT s1.skill_name
                    FROM online_resume.code_example_skills ces1
                    JOIN online_resume.skills s1 USING (skill_id)
                    WHERE ces1.code_example_id = ce.code_example_id
                    ORDER BY ces1.sort_order ASC
                ), ',<br />') AS skills_used,
                ce.purpose,
                ce.description
            FROM online_resume.code_examples ce
            LEFT JOIN online_resume.work_history wh ON wh.work_history_id = ce.work_history_id
            LEFT JOIN online_resume.portfolio_projects pp ON pp.portfolio_project_id = ce.portfolio_project_id
            ORDER BY ce.sort_order ASC
        ");
        
        if(!empty($code_examples)) {
            $source_file_edit_url = Http::getPageBaseUrl() . 'OnlineResumeCodeExampleFileEdit';
            $skills_used_edit_url = Http::getPageBaseUrl() . 'OnlineResumeCodeExampleSkillsEdit';
        
            foreach($code_examples as $code_example) {            
                $source_file_edit_link = "<a href=\"{$source_file_edit_url}&code_example_id={$code_example['code_example_id']}\">Edit Source File</a>";
            
                $code_example['source_file'] = $source_file_edit_link;
                
                $skills_used_edit_link = "<a href=\"{$skills_used_edit_url}&code_example_id={$code_example['code_example_id']}\">Edit Skills Used</a>";
            
                if(!empty($code_example['skills_used'])) {
                    $code_example['skills_used'] .= "<br /><br />{$skills_used_edit_link}";
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