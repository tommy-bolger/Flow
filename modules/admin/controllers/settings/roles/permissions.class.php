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

namespace Modules\Admin\Controllers\Settings\Roles;

use \Framework\Html\Form\TableForm;
use \Framework\Html\Form\Fields\Dropdown;
use \Framework\Utilities\Http;

class Permissions
extends Manage {
    protected $title = "Edit Role Permissions";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();

        $this->page_links['Permissions'] = Http::getCurrentLevelPageUrl('permissions');
    }
    
    protected function constructRightContent() {                
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        request()->get->setRequired(array('role_id'));
        
        $role_id = request()->get->role_id;
        
        $role_permissions = db()->getGroupedRows("
            SELECT
                p.permission_name,
                p.permission_id,
                r.role_id,
                rpa.role_permission_affiliation_id,
                p.display_name,
                p.description,
                CASE 
                    WHEN rpa.can IS NOT NULL THEN rpa.can
                    ELSE 0
                END AS can
            FROM cms_roles r
            JOIN cms_permissions p USING (module_id)
            LEFT JOIN cms_role_permission_affiliation rpa USING (role_id, permission_id)
            WHERE r.role_id = ?
        ", array($role_id));

        $role_permissions_form = new TableForm('role_permissions');
        
        $role_permissions_form->setTitle('Edit the Permissions for this Role');
        
        foreach($role_permissions as $permission_name => $role_permission) {
            $toggle_dropdown = new Dropdown($permission_name, $role_permission['display_name'], array(
                1 => 'Yes',
                0 => 'No'
            ));
            $toggle_dropdown->setDescription($role_permission['description']);
            $toggle_dropdown->addBlankOption();
            $toggle_dropdown->setDefaultValue($role_permission['can']);
            
            $role_permissions_form->addField($toggle_dropdown);
        }
        
        $role_permissions_form->addSubmit('save', 'Save');
        
        if($role_permissions_form->wasSubmitted() && $role_permissions_form->isValid()) {
            $submitted_permissions = array_intersect_key($role_permissions_form->getData(), $role_permissions);

            foreach($submitted_permissions as $permission_name => $can) {            
                if(!empty($role_permissions[$permission_name]['role_permission_affiliation_id'])) {
                    db()->update('cms_role_permission_affiliation', array('can' => $can), array(
                        'role_permission_affiliation_id' => $role_permissions[$permission_name]['role_permission_affiliation_id']
                    ));
                }
                else {
                    db()->insert('cms_role_permission_affiliation', array(
                        'role_id' => $role_permissions[$permission_name]['role_id'],
                        'permission_id' => $role_permissions[$permission_name]['permission_id'],
                        'can' => $can
                    ));
                }
            }
            
            $role_permissions_form->addConfirmation('Permissions successfully updated for this role.');
        }
        
        return $role_permissions_form;
    }
}