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
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Custom\PagePath;

class Home
extends ModulePage {
    protected $title = "Administration Control Panel";
    
    protected $cache_page = true;
    
    protected $managed_module;
    
    protected $page_links = array();
    
    protected $active_top_link = 'Home';
    
    protected $active_sub_nav_section;
    
    protected $active_sub_nav_link;

    public function __construct() {
        parent::__construct('admin');

        if(!Auth::userLoggedIn()) {
            Http::redirect(Http::getTopLevelPageUrl('login'));
        }
        
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructLeftContent();
        
        $this->constructRightContent();
        
        $this->constructFooter();
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
        
        $this->constructLoginInfo();
        
        $this->constructTopNav();
        
        $this->constructPagePath();
    }
    
    private function constructLoginInfo() {
        $user_name = session()->user_name;
        
        $this->body->addChild($user_name, 'user_name');
            
        $this->body->addChild(Http::getTopLevelPageUrl("login", array('logout' => 1)), 'logout_link');
    }
    
    protected function constructTopNav() {
        $admin_page_links = array(
            'Home' => Http::getTopLevelPageUrl()
        );
        
        $admin_page_links = array_merge($admin_page_links, $this->getModuleMenuLinks());
        
        $modules_list = new LinkList($admin_page_links, array('id' => 'modules_list'));
        
        $modules_list->setActiveItem($this->active_top_link);
        
        $this->body->addChild($modules_list);
    }
    
    protected function getModuleMenuLinks() {
        $admin_page_links = array();
        
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
                $admin_page_links[$page_name] = Http::getInternalUrl('', array('subd_1' => $module_name));
            }
        }
        
        return $admin_page_links;
    }
    
    private function constructLeftContent() {        
        $this->constructSubNav();
    }
    
    private function constructSubNav() {
        $sub_nav_links = $this->getSubNavLinks();
        
        if(!empty($sub_nav_links)) {
            foreach($sub_nav_links as $section_title => $section_links) {
                $section_template = new TemplateElement('subnav_section.php');
                
                $section_template->addChild($section_title, 'section_title');
            
                $section_list = new LinkList($section_links, array('class' => 'sub_nav'));
                
                if($section_title == $this->active_sub_nav_section) {
                    $section_list->setActiveItem($this->active_sub_nav_link);
                };
                
                $section_template->addChild($section_list, 'section_list');
                
                $this->body->addChild($section_template, 'sub_nav', true);
            }
        }
    }
    
    protected function getSubNavLinks() {    
        return array();
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
        $right_content = new TemplateElement('home.php');
    
        $this->body->addChild($right_content, 'current_menu_content');
    }
    
    protected function constructFooter() {
        $this->body->addChild(config('framework')->version, 'version');
    }
}