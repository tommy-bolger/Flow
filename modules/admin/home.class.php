<?php
/**
* The home page of the Admin module.
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
namespace Modules\Admin;

use \Framework\Core\Framework;
use \Framework\Modules\ModulePage;
use \Framework\Modules\WebModule;
use \Framework\Utilities\Auth;
use \Framework\Utilities\Http;
use \Framework\Html\Lists\LinkList;
use \Framework\Html\Misc\Div;
use \Framework\Html\Custom\PagePath;

class Home
extends ModulePage {
    protected $title = "Administration Control Panel";
    
    protected $cache_page = true;
    
    protected $managed_module;
    
    protected $page_links = array();

    public function __construct() {
        parent::__construct('admin');

        if(!Auth::userLoggedIn()) {
            Http::redirect(Http::getTopLevelPageUrl('login'));
        }
        
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructLeftContent();
        
        $this->constructPagePath();
        
        $this->constructRightContent();
    }
    
    protected function loadManagedModule($module_name) {
        $this->managed_module = new WebModule($module_name);
    }
    
    private function constructHeader() {
        $this->setTemplate('layout.php');
        
        //Setup the css style        
        $this->addCssFiles(array(
            'reset.css',
            'main.css'
        ));
        
        //Setup the javascript        
        $this->addJavascriptFile("jquery.min.js");
    }
    
    protected function constructContentHeader() {
        $this->body->addChild(Http::getTopLevelPageUrl(), 'home_link');
    }
    
    private function constructLeftContent() {
        $this->constructLoginInfo();
        
        $this->constructSideMenu();
    }
    
    protected function constructSideMenu() {
        $this->constructModuleMenu();
        
        $this->constructUserManagementMenu();
        
        $this->constructSettingsMenu();
    }
    
    private function constructLoginInfo() {
        $user_name = session()->user_name;
        
        $this->body->addChild($user_name, 'user_name');
            
        $this->body->addChild(Http::getTopLevelPageUrl("login", array('logout' => 1)), 'logout_link');
    }
    
    protected function constructModuleMenu() {    
        $admin_pages = array();
        
        if(Framework::$enable_cache) {
            $admin_pages = cache()->get('header_nav', 'admin');
        }
        
        if(empty($admin_pages)) {
            $admin_pages = db()->getMappedColumn("
                SELECT 
                    display_name,
                    module_name
                FROM cms_modules
                WHERE enabled = 1
                    AND module_name != 'admin'
                ORDER BY sort_order
            ");
            
            if(Framework::$enable_cache) {
                cache()->set('header_nav', $admin_pages, 'admin');
            }
            
            foreach($admin_pages as $page_name => $module_name) {
                $admin_pages[$page_name] = Http::getInternalUrl('', array('subd_1' => $module_name));
            }
        }
        
        $modules_list = new LinkList($admin_pages, array('id' => 'modules_list'));
        
        $this->body->addChild($modules_list);
    }
    
    protected function getSettingsMenuLinks() {    
        return array(
            'Module Management' => Http::getTopLevelPageUrl("toggle-modules"),
            'Errors' => Http::getTopLevelPageUrl("site-errors")
        );
    }
    
    protected function constructSettingsMenu() {            
        $settings_links = array();
        
        if(isset($this->managed_module)) {
            $settings_links['Configuration'] = Http::getTopLevelPageUrl("configuration-edit", array('module_id' => $this->managed_module->getId()));
        }
        else {
            $settings_links['Configuration'] = Http::getTopLevelPageUrl('configuration-edit');
        }
        
        $settings_links = array_merge($settings_links, $this->getSettingsMenuLinks());
        
        $settings_list = new LinkList($settings_links, array('id' => 'settings_list'));
        
        $this->body->addChild($settings_list);
    }
    
    protected function constructUserManagementMenu() {        
        $link_query_string = array();
        
        if(isset($this->managed_module)) {
            $link_query_string = array('module_id' => $this->managed_module->getId());
        }
        
        $subdirectory_path = array('d_1' => 'user-management');
        
        $user_management_list = new LinkList(array(
            'Module Roles' => Http::getInternalUrl('', $subdirectory_path, 'module-roles', $link_query_string),
            'Module Permissions' => Http::getInternalUrl('', $subdirectory_path, 'module-permissions', $link_query_string)
        ), array('id' => 'user_management_list'));
        
        $this->body->addChild($user_management_list);
    }
    
    protected function setPageLinks() {        
        $this->page_links['Home'] = Http::getTopLevelPageUrl();
    
        session()->module_path = $this->page_links;
    }
    
    private function constructPagePath() {
        $this->setPageLinks();
    
        $page_path = new PagePath('page_path');
        $page_path->addPages($this->page_links);
        
        $this->body->addChild($page_path);
    }
    
    protected function constructRightContent() {
        $right_content = new Div(array('id' => 'current_menu_content'), '
            <h1>Welcome</h1>
            <br />
            <p>
                Welcome to the Administration Control Panel. Configuration for individual modules can be found via one of the tabs up top. Sub-pages for the current configuration can be found in the sidebar on the left.
                <br />
                <br />
            </p>
        ');
    
        $this->body->addChild($right_content);
    }
}