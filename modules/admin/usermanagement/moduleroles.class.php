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

class ModuleRoles
extends Home {
    protected $title = "Module Roles";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Module Roles'] = Http::getCurrentLevelPageUrl('module-roles');
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), "<h2>{$this->title}</h2><br />");
        
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
        $roles_table = new EditTableForm(
            'roles',
            'cms_roles',
            'role_id',
            'sort_order',
            $page_filter,
            $module_filters
        );
        
        $roles_table->disableDeleteRecord();
        
        $roles_table->setNumberOfColumns(3);
    
        $roles_table->addHeader(array(
            'display_name' => 'Role Name',
            'Module',
            'Role Permissions'
        ));
        
        if($roles_table->getFormVisibility()) {
            $roles_table->addTextbox('display_name', 'Role Name');
            $roles_table->addSubmit('save', 'Save');
            
            $roles_table->processForm();
        }
        
        $roles_table->useQuery("
            SELECT
                r.display_name,
                m.display_name AS module_name,
                NULL AS role_permissions,
                r.role_id
            FROM cms_roles r
            JOIN cms_modules m USING (module_id)
            ORDER BY r.sort_order
        ", array(), function($results_data) {
            if(!empty($results_data)) {
                foreach($results_data as $index => $results_row) {                
                    $results_row['role_permissions'] = '
                        <a href="' . Http::getCurrentLevelPageUrl('role-permissions', array('role_id' => $results_row['role_id'])) . '">
                            Manage Role Permissions
                        </a>
                    ';
                    
                    $results_data[$index] = $results_row;
                }
            }
            
            return $results_data;
        });
        
        $content->addChild($roles_table);
        
        $this->body->addChild($content);
    }
}