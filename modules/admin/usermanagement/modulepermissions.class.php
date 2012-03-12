<?php
/**
* The management page for configurations.
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

namespace Modules\Admin\UserManagement;

use \Modules\Admin\Home;
use \Framework\Html\Misc\Div;
use \Framework\Html\Table\EditTableForm;
use \Framework\Utilities\Http;

class ModulePermissions
extends Home {
    protected $title = "Module Permissions";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Module Permissions'] = Http::getCurrentLevelPageUrl('module-permissions');
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Module Permissions</h2><br />');
        
        $page_filter = array();
        $module_filters = array();
        
        if(isset(request()->get->module_id)) {
            $page_filter[] = 'module_id';
        }
        else {
            $modules = db()->getGroupedRows("
                SELECT
                    display_name,
                    module_id
                FROM cms_modules
            ");
            
            $module_filters = array('Modules' => $modules);
        }

        //The education history table
        $permissions_table = new EditTableForm(
            'permissions',
            'cms_permissions',
            'permission_id',
            'sort_order',
            $page_filter,
            $module_filters
        );
        
        $permissions_table->disableDeleteRecord();
        $permissions_table->disableAddRecord();
        
        $permissions_table->setNumberOfColumns(2);
    
        $permissions_table->addHeader(array(
            'display_name' => 'Permission Name',
            'description' => 'Description',
            'Module' 
        ));
        
        if($permissions_table->getFormVisibility()) {
            $permissions_table->addTextbox('display_name', 'Permission Name');
            $permissions_table->addTextarea('description', 'Description');
            $permissions_table->addSubmit('save', 'Save');
            
            $permissions_table->processForm();
        }
        
        $permissions_table->useQuery("
            SELECT
                p.display_name,
                p.description,
                m.display_name AS module_name,
                p.permission_id
            FROM cms_permissions p
            JOIN cms_modules m USING (module_id)
            ORDER BY p.sort_order
        ", array(), function($results_data) {
            if(!empty($results_data)) {
                foreach($results_data as $index => $results_row) {
                    $description = $results_row['description'];
                
                    if(strlen($description) > 50) {
                        $results_row['description'] = substr($description, 0, 50) . '...';
                    }
                    
                    $results_data[$index] = $results_row;
                }
            }
            
            return $results_data;
        });
        
        $content->addChild($permissions_table);
        
        $this->body->addChild($content);
    }
}