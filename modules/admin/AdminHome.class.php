<?php
/**
* The home page of the Admin module.
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
class AdminHome
extends Module {
    protected $name = __CLASS__;

    protected $title = "Administration Control Panel";

    public function __construct() {
        parent::__construct('admin');

        if(!Auth::userLoggedIn()) {
            Http::redirect('AdminLogin');
        }
        
        $this->initialize();
        
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructLeftContent();
        
        $this->constructRightContent();
    }
    
    protected function initialize() {}
    
    private function constructHeader() {
        $this->setDocType('xhtml_1.1');
        
        //Setup the css style
        $this->setThemeDirectory("{$this->module_assets_path}/styles/default");
        
        $this->addCssFile("{$this->assets_path}/css/reset.css", false);
        
        $this->addCssFile('main.css');
        
        //Setup the javascript        
        $this->addJavascriptFile("{$this->assets_path}/javascript/jquery-1.4.4.min.js", false);
        
        $this->addJavascriptFile('AdminHome.js');
        
        //Set the template for this page
        $this->body->setTemplate("index_body.html");
    }
    
    protected function constructContentHeader() {
        $this->constructMenu();
    }
    
    private function constructLeftContent() {
        $this->constructLoginInfo();
        
        $this->constructSidebar();
    }
    
    private function constructLoginInfo() {
        $user_name = session()->user_name;
        
        $this->body->addDiv(array('id' => 'login_info', 'class' => 'normal_size_text'), "Hello, <b>{$user_name}</b>");
        
        $base_url = Http::getBaseUrl();
    
        $this->body->addHyperlink($base_url . '?page=AdminLogin&logout=1', '[Logout]', array('id' => 'LOGOUT_LINK'));
    }
    
    protected function constructSidebar() {
        $sidebar = new Div(array('id' => 'sidebar'), '<h2>Site Management</h2>');
    
        $site_management_pages = new LinkList(array(
            'Enable/Disable Modules' => 'ModuleManagement'
        ));
        
        $sidebar->addChild($site_management_pages);
        
        $this->body->addChild($sidebar);
    }
    
    private function constructMenu() {
        $menu = new Div(array('id' => 'menu'));
        
        $admin_pages = db()->getAll("
            SELECT admin_page_name, display_name
            FROM modules
            WHERE enabled = true
            ORDER BY sort_order
        ");
        
        foreach($admin_pages as $page) {        
            $menu->addDiv(array(
                 'class' => "menu-item"
            ), "<a class=\"menu-item-text\" href=\"?page={$page['admin_page_name']}\">{$page['display_name']}</a>");
        }
        
        $this->body->addChild($menu);
    }
    
    protected function constructRightContent() {
        $this->body->addDiv(array('id' => 'current_menu_content'), '
            <h1>Welcome</h1>
            <br />
            <p>
                Welcome to the Administration Control Panel. Configuration for individual modules can be found via one of the tabs up top. Sub-pages for the current configuration can be found in the sidebar on the left.
            </p>
        ');
    }
}