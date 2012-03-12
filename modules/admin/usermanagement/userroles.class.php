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
use \Framework\Html\Form\Fields\Dropdown;
use \Framework\Utilities\Http;

class UserRoles
extends Home {
    protected $title = "User Roles";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['User Roles'] = Http::getCurrentLevelPageUrl('module-roles');
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), "<h2>{$this->title}</h2><br />");

        //The education history table
        $user_roles_table = new EditTableForm(
            'user_role_affiliation',
            'cms_user_role_affiliation',
            'user_role_affiliation_id',
            '',
            array('user_id')
        );
        
        $user_roles_table->setNumberOfColumns(2);
    
        $user_roles_table->addHeader(array(
            'User Email',
            'role_id' => 'Role Name'
        ));
        
        if($user_roles_table->getFormVisibility()) {
            $roles = db()->getAll("
                SELECT
                    m.display_name AS module_name,
                    r.role_id,
                    r.display_name AS role_name
                FROM cms_modules m
                JOIN cms_roles r USING (module_id)
                WHERE m.enabled = 1
            ");
            
            $role_options = array();
            
            foreach($roles as $role) {
                $role_options[$role['module_name']][$role['role_id']] = $role['role_name'];
            }
            
            $roles_dropdown = new Dropdown('role_id', 'Role', $role_options);
            $roles_dropdown->addBlankOption();
        
            $user_roles_table->addField($roles_dropdown);
            
            $user_roles_table->addSubmit('save', 'Save');
            
            $user_roles_table->setRequiredFields(array('role_id'));
            
            $user_roles_table->processForm();
        }
        
        $user_roles_table->useQuery("
            SELECT
                u.email_address,
                r.display_name AS role_name,
                r.role_id,
                ura.user_role_affiliation_id
            FROM cms_users u
            JOIN cms_user_role_affiliation ura USING (user_id)
            JOIN cms_roles r USING (role_id)
        ");
        
        $content->addChild($user_roles_table);
        
        $this->body->addChild($content);
    }
}