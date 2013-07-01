<?php
/**
* The management page for the skills used in a user code example of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\CodeExamples\Skills;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;

class Manage
extends Home {
    protected $title = "Manage Code Example Skills";
    
    protected $active_sub_nav_link = "Manage";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links["Manage"] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function getDataTable() {
        $resultset = new SQL('code_example_skills');
        
        $resultset->setBaseQuery("
            SELECT
                ces.code_example_skill_id,
                s.skill_name
            FROM resume_code_example_skills ces
            JOIN resume_skills s USING (skill_id)
            {{WHERE_CRITERIA}}
        ");
        
        $resultset->setSortCriteria('ces.sort_order', 'ASC');
    
        $page_filter = array();
        $code_example_options = array();
    
        if(!empty(request()->get->code_example_id)) {
            $page_filter = array('code_example_id');
        }
        else {
            $code_examples = db()->getAll("
                SELECT
                    code_example_id,
                    code_example_name
                FROM resume_code_examples
                ORDER BY sort_order
            ");
            
            if(!empty($code_examples)) {
                foreach($code_examples as $code_example) {                    
                    $code_example_options[$code_example['code_example_name']] = "code_example_id = {$code_example['code_example_id']}";
                }
            }
        }
    
        $code_example_skills_table = new EditTable(
            'code_example_skills',
            'resume_code_example_skills',
            'add',
            'code_example_skill_id',
            'sort_order',
            $page_filter
        );
        
        $code_example_skills_table->setNumberOfColumns(1);
        
        $code_example_skills_table->setHeader(array(
            'skill_id' => 'Skills Used'
        ));
        
        if(!empty($code_example_options)) {
            $code_example_skills_table->addFilterDropdown('code_examples', $code_example_options, 'Select a Code Example');
        
            $code_example_skills_table->setPrimaryDropdown('code_examples');
        }
        
        $code_example_skills_table->process($resultset);
        
        return $code_example_skills_table;
    }
    
    protected function constructRightContent() {        
        $this->page->body->addChild($this->getDataTable(), 'current_menu_content');
    }
}