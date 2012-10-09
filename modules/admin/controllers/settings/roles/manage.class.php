<?php
/**
* The management page for roles.
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

namespace Modules\Admin\Controllers\Settings\Roles;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Roles";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array('module_id' => $this->managed_module->getId()));
    }
    
    protected function constructRightContent() {
        $roles_table = new EditTable(
            'roles',
            'cms_roles',
            'add',
            'role_id',
            'sort_order',
            array('module_id')
        );
        
        $roles_table->disableDeleteRecord();
        
        $roles_table->setNumberOfColumns(2);
    
        $roles_table->addHeader(array(
            'display_name' => 'Role Name',
            'Permissions'
        ));
        
        $roles_table->useQuery("
            SELECT
                display_name,
                NULL AS permissions,
                role_id,
                module_id
            FROM cms_roles
            ORDER BY sort_order
        ", array(), function($results_data) {
            if(!empty($results_data)) {
                foreach($results_data as &$results_row) {                
                    $results_row['permissions'] = '
                        <a href="' . 
                            Http::getCurrentLevelPageUrl('permissions', array(
                                'module_id' => $results_row['module_id'],
                                'role_id' => $results_row['role_id']
                            )) . 
                        '">
                            Manage Permissions
                        </a>
                    ';
                }
            }
            
            return $results_data;
        });
        
        $this->page->body->addChild($roles_table, 'current_menu_content');
    }
}