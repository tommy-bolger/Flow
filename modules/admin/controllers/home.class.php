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
namespace Modules\Admin\Controllers;

use \Framework\Core\Framework;
use \Framework\Core\Controller;
use \Framework\Modules\ModulePage;
use \Framework\Modules\WebModule;
use \Framework\Utilities\Auth;
use \Framework\Utilities\Http;
use \Framework\Html\Lists\LinkList;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Custom\PagePath;

class Home
extends Controller {
    protected $title = "Administration Control Panel";
    
    protected $cache_page = true;
    
    protected $managed_module;
    
    protected $module_links = array();
    
    protected $page_links = array();
    
    protected $active_nav;
    
    protected $active_sub_nav_section;
    
    protected $active_sub_nav_link;

    public function __construct() {
        parent::__construct();
    
        if(!Auth::userLoggedIn()) {
            Http::redirect(Http::getTopLevelPageUrl('login', array(), 'admin'));
        }
        
        $module_id = request()->module_id;
        
        if(!empty($module_id)) {
            $this->loadManagedModule();
        }
    }
    
    public function setup() {
        $this->loadModulePage();
    
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructLeftContent();
        
        $this->constructRightContent();
        
        $this->constructFooter();
    }
    
    protected function loadModulePage() {
        $this->page = new ModulePage('admin');        
    }                
    
    protected function getModuleSessionLinks() {
        $current_module_name = session()->current_module;

        $module_links_session_name = "{$current_module_name}_links";
        
        //Retrieve saved nav links in the session
        if(isset(session()->$module_links_session_name)) {
            $this->module_links = session()->$module_links_session_name;
        }
    }
    
    protected function getErrorsLinks() {
        $errors_path = array(
            'errors',
            'view'
        );
        
        $query_string_parameters = array();
        
        if(!empty($this->managed_module)) {
            $query_string_parameters['module_id'] = $this->managed_module->getId();
        }
        
        return array(
            'errors' => array(
                'top_nav' => array (
                    'Errors' => Http::getInternalUrl('', $errors_path, 'all', $query_string_parameters)
                ),
                'sub_nav' => array(
                    'Errors' => array(
                        'View All' => Http::getInternalUrl('', $errors_path, 'all', $query_string_parameters)
                    )
                )
            )
        );
    }
    
    protected function getSettingsLinks() {
        $settings_path = array('settings');
        
        $meta_settings_path = $settings_path;
        $meta_settings_path[] = 'meta';
        
        $static_pages_path = $settings_path;
        $static_pages_path[] = 'static-pages';
        
        $roles_path = $settings_path;
        $roles_path[] = 'roles';
        
        $permissions_path = $settings_path;
        $permissions_path[] = 'permissions';
        
        $query_string_parameters = array();
        
        if(!empty($this->managed_module)) {
            $query_string_parameters['module_id'] = $this->managed_module->getId();
        }
        
        $settings_links = array(
            'settings' => array(
                'top_nav' => array (
                    'Settings' => Http::getInternalUrl('', $settings_path, 'general', $query_string_parameters)
                ),
                'sub_nav' => array(
                    'Settings' => array(
                        'General' => Http::getInternalUrl('', $settings_path, 'general', $query_string_parameters)
                    )
                )
            )
        );
        
        if(!empty($this->managed_module)) {
            if(!empty($this->managed_module->configuration->has_meta_settings)) {
                $settings_links['settings']['sub_nav']['Meta'] = array(
                    'Manage' => Http::getInternalUrl('', $meta_settings_path, 'manage', $query_string_parameters),
                    'Add/Edit' => Http::getInternalUrl('', $meta_settings_path, 'add', $query_string_parameters)
                );
            }
            
            if(!empty($this->managed_module->configuration->has_static_pages)) {
                $settings_links['settings']['sub_nav']['Static Pages'] = array(
                    'Manage' => Http::getInternalUrl('', $static_pages_path, 'manage', $query_string_parameters)
                );
            }
            
            if(!empty($this->managed_module->configuration->has_roles)) {
                $settings_links['settings']['sub_nav']['Roles'] = array(
                    'Manage' => Http::getInternalUrl('', $roles_path, 'manage', $query_string_parameters),
                    'Add/Edit' => Http::getInternalUrl('', $roles_path, 'add', $query_string_parameters),
                );
            }
            
            if(!empty($this->managed_module->configuration->has_permissions)) {
                $settings_links['settings']['sub_nav']['Permissions'] = array(
                    'Manage' => Http::getInternalUrl('', $permissions_path, 'manage', $query_string_parameters)
                );
            }
        }
        
        return $settings_links;
    }
    
    protected function getAdministratorsLinks() {
        $administrators_path = array('administrators');
    
        return array(
            'administrators' => array(
                'top_nav' => array(
                    'Administrators' => Http::getInternalUrl('', $administrators_path, 'manage')
                ),
                'sub_nav' => array(
                    'Administrators' => array(
                        'Manage' => Http::getInternalUrl('', $administrators_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $administrators_path, 'add')
                    )
                )
            )
        );
    }
    
    protected function getBansLinks() {
        $bans_path = array('bans');
        
        $ip_addresses_path = $bans_path;
        $ip_addresses_path[] = 'ip-addresses';
        
        $words_path = $bans_path;
        $words_path[] = 'words';
    
        return array(
            'bans' => array(
                'top_nav' => array(
                    'Bans' => Http::getInternalUrl('', $ip_addresses_path, 'manage')
                ),
                'sub_nav' => array(
                    'IP Addresses' => array(
                        'Manage' => Http::getInternalUrl('', $ip_addresses_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $ip_addresses_path, 'add')
                    ),
                    'Words' => array(
                        'Manage' => Http::getInternalUrl('', $words_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $words_path, 'add')
                    )
                )
            )
        );
    }
    
    protected function getAdsLinks() {
        $ads_path = array('ads');
        
        $ad_campaigns_path = $ads_path;
        $ad_campaigns_path[] = 'campaigns';
        
        $campaign_ads_path = $ad_campaigns_path;
        $campaign_ads_path[] = 'ads';
    
        return array(
            'ads' => array(
                'top_nav' => array(
                    'Ads' => Http::getInternalUrl('', array('ads'), 'manage')
                ),
                'sub_nav' => array(
                    'Ads' => array(
                        'Manage' => Http::getInternalUrl('', $ads_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $ads_path, 'add')
                    ),
                    'Campaigns' => array(
                        'Manage' => Http::getInternalUrl('', $ad_campaigns_path, 'manage')
                    ),
                    'Campaign Ads' => array(
                        'Manage' => Http::getInternalUrl('', $campaign_ads_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $campaign_ads_path, 'add')
                    )
                )
            )
        );
    }
    
    protected function initializeModuleLinks() {
        $modules = array();
            
        if($this->framework->enable_cache) {
            $modules = cache()->get('modules', 'module_links');
        }
    
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
            
            if($this->framework->enable_cache) {
                cache()->set('modules', $modules, 'module_links');
            }
        }
        
        $this->module_links = $this->getErrorsLinks();
        
        $this->module_links += $this->getSettingsLinks();
        
        $this->module_links += $this->getAdministratorsLinks();
        
        foreach($modules as $module) {
            $this->module_links[$module['module_name']]['top_nav'] = array(
                $module['display_name'] => Http::getInternalUrl($module['module_name'], array('admin'))
            );
        }
    }
    
    protected function loadManagedModule($module_name = '') {
        if(empty($module_name)) {
            $module_name = session()->current_module;
        }
    
        $this->managed_module = new WebModule($module_name);
        
        session()->current_module = $module_name;
    }
    
    private function constructHeader() {
        $this->page->setTitle($this->title);
    
        $this->page->setTemplate('layout.php');
        
        //Setup the css style        
        $this->page->addCssFiles(array(
            'reset.css',
            'main.css'
        ));
        
        if($this->framework->configuration->enable_javascript) {
            $this->page->addCssFile('top_nav.css');
        }
        
        //Setup the javascript        
        $this->page->addJavascriptFiles(array(
            "jquery.min.js",
            'nav.js'
        ));
    }
    
    protected function constructContentHeader() {
        $this->page->body->addChild(Http::getTopLevelPageUrl(), 'home_link');
        
        $this->constructLoginInfo();
        
        $this->constructTopNav();
        
        $this->constructPagePath();
    }
    
    private function constructLoginInfo() {
        $user_name = session()->user_name;
        
        $this->page->body->addChild($user_name, 'user_name');
            
        $this->page->body->addChild(Http::getTopLevelPageUrl("login", array('logout' => 1)), 'logout_link');
    }
    
    protected function constructTopNav() {
        $this->initializeModuleLinks();
        
        //Generate the data structure for the JS hover menus on the top nav
        if(!empty($this->module_links)) {
            $module_sub_nav_links = array();
        
            foreach($this->module_links as $link_name => $module_link) {
                if(isset($module_link['sub_nav'])) {
                    $module_sub_nav_links[$link_name] = $module_link['sub_nav'];
                }
            }

            if(!empty($module_sub_nav_links)) {
                $this->page->addInlineJavascript("top_nav_links = " . json_encode($module_sub_nav_links) . ";");
            }
        }
    
        $current_module_name = '';
        
        if(!empty($this->managed_module)) {
            $current_module_name = $this->managed_module->getName();
            
             //Aave the nav in module context into the session
            $module_links_session_name = "{$current_module_name}_links";
            
            if(!isset(session()->$module_links_session_name)) {
                session()->$module_links_session_name = $this->module_links;
            }
        }
    
        $active_nav = '';
    
        //Use the active nav property when in a module
        if(!empty($this->active_nav)) {
            $active_nav = $this->active_nav;
        }
        //Use the module name when on the admin home page
        elseif(!empty($current_module_name)) {
            $active_nav = $current_module_name;
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
        
        $this->page->body->addChild($modules_list);
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
    
        if(isset($this->module_links[$active_nav]['sub_nav'])) {
            $sub_nav_links = $this->module_links[$active_nav]['sub_nav'];
            
            foreach($sub_nav_links as $section_title => $section_links) {
                $section_template = new TemplateElement('subnav_section.php');
                
                $section_template->addChild($section_title, 'section_title');
            
                $section_list = new LinkList($section_links, array('class' => 'sub_nav'));
                
                if($section_title == $this->active_sub_nav_section) {
                    $section_list->setActiveItem($this->active_sub_nav_link);
                };
                
                $section_template->addChild($section_list, 'section_list');
                
                $this->page->body->addChild($section_template, 'sub_nav', true);
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
        
        $this->page->body->addChild($page_path);
    }
    
    protected function constructRightContent() {    
        $right_content = new TemplateElement('home.php');
    
        $this->page->body->addChild($right_content, 'current_menu_content');
    }
    
    protected function constructFooter() {
        $this->page->body->addChild($this->framework->configuration->version, 'version');
    }
    
    protected function getForm() {}
    
    public function submit() {
        $form = $this->getForm();
        
        return $form->toJsonArray();
    }
    
    protected function getDataTable() {}
    
    public function updateTableState() {
        $data_table = $this->getDataTable();
        
        return $data_table->toJsonArray();
    }
}