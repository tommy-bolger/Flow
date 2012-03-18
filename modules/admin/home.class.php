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
    
    protected $module_links = array();
    
    protected $page_links = array();
    
    protected $active_nav;
    
    protected $active_sub_nav_section;
    
    protected $active_sub_nav_link;

    public function __construct() {
        parent::__construct('admin');

        if(!Auth::userLoggedIn()) {
            Http::redirect(Http::getTopLevelPageUrl('login'));
        }
        
        $this->initializeModuleLinks();
        
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructLeftContent();
        
        $this->constructRightContent();
        
        $this->constructFooter();
    }
    
    protected function initializeModuleLinks() {
        $modules = cache()->get('modules', 'module_links');
    
        if(empty($modules)) {
            $modules = db()->getAll("
                SELECT 
                    display_name,
                    module_name
                FROM cms_modules
                WHERE enabled = 1
                    AND module_name != 'admin'
                ORDER BY sort_order
            ");
            
            cache()->set('modules', $modules, 'module_links');
        }
        
        foreach($modules as $module) {
            $this->module_links[$module['module_name']]['top_nav'] = array(
                $module['display_name'] => Http::getInternalUrl('', array('subd_1' => $module['module_name']))
            );
        }
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
        
        if(config('framework')->enable_javascript) {
            $this->addCssFile('top_nav.css');
        }
        
        //Setup the javascript        
        $this->addJavascriptFiles(array(
            "jquery.min.js",
            'nav.js'
        ));
        
        if(!empty($this->module_links)) {
            $module_sub_nav_links = array();
        
            foreach($this->module_links as $link_name => $module_link) {
                if(isset($module_link['sub_nav'])) {
                    $module_sub_nav_links[$link_name] = $module_link['sub_nav'];
                }
            }

            if(!empty($module_sub_nav_links)) {
                $this->addInlineJavascript("top_nav_links = " . json_encode($module_sub_nav_links) . ";");
            }
        }
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
        $active_nav = '';
    
        //Use either the active nav property when in a module
        if(!empty($this->active_nav)) {
            $active_nav = $this->active_nav;
        }
        //Use the module name when on the admin home page
        elseif(!empty($this->managed_module)) {
            $active_nav = $this->managed_module->getName();
        }
        
        $modules_list = new LinkList(array('Home' => Http::getTopLevelPageUrl()), array('id' => 'modules_list'));
        
        $active_link_name = 'Home';
        
        if(!empty($this->module_links)) {
            foreach($this->module_links as $link_name => $module_link) {
                $top_nav = $module_link['top_nav'];
                $link_display_name = key($top_nav);
                
                $modules_list->addListItem(current($top_nav), $link_display_name, array(
                    'id' => $link_name,
                    'class' => array('top_nav_hover')
                ));
                
                if($link_name == $active_nav) {
                    $active_link_name = $link_display_name;
                }
            }
        }
        
        $modules_list->setActiveItem($active_link_name);
        
        $this->body->addChild($modules_list);
    }
    
    private function constructLeftContent() {        
        $this->constructSubNav();
    }
    
    private function constructSubNav() {
        $active_nav = '';
    
        //Use either the active nav property when in a module
        if(!empty($this->active_nav)) {
            $active_nav = $this->active_nav;
        }
        //Use the module name when on the admin home page
        elseif(!empty($this->managed_module)) {
            $active_nav = $this->managed_module->getName();
        }
    
        if(isset($this->managed_module) && isset($this->module_links[$active_nav]['sub_nav'])) {
            $sub_nav_links = $this->module_links[$active_nav]['sub_nav'];
            
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