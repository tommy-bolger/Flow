<?php
/**
* The management page for the user skill categories of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\Skills\Categories;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Skill Categories";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {            
        //The skill_category table
        $skill_category_edit_table = new EditTable(
            'skill_categories', 
            'resume_skill_categories',
            'add',
            'skill_category_id',
            'sort_order'
        );
        
        $skill_category_edit_table->setNumberOfColumns(1);
        
        $skill_category_edit_table->addHeader(array(
            'skill_category_name' => 'Skill Category Name'
        ));

        $skill_category_edit_table->useQuery("
            SELECT
                skill_category_id,
                skill_category_name
            FROM resume_skill_categories
        ");
        
        $this->page->body->addChild($skill_category_edit_table, 'current_menu_content');
    }
}