<?php
/**
* The base page controller Admin module.
* Copyright (c) 2017, Tommy Bolger
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
namespace Modules\Admin\Controllers\Page;

use \Framework\Core\Framework;
use \Framework\Core\Controllers\Page as PageController;
use \Framework\Modules\ModulePage;
use \Framework\Modules\WebModule;
use \Framework\Utilities\Auth;
use \Framework\Utilities\Http;
use \Framework\Html\Lists\LinkList;
use \Framework\Html\Misc\TemplateElement;
use \Framework\Html\Custom\Breadcrumbs;

class Admin
extends PageController {
    protected $name = 'home';
    
    protected $sub_name = '';

    protected $title = "Administration Control Panel";
    
    protected $managed_module;
    
    protected $navigation_links = array();
    
    protected $breadcrumbs;
    
    protected $page_links = array();
    
    public function __construct() {
        parent::__construct('admin');
    }
    
    public function authorize() {
        if(!Auth::userLoggedIn()) {        
            Http::redirect('/admin/logout');
        }
    }
    
    protected function loadManagedModule($module_name = '') {
        if(empty($module_name)) {
            $module_name = session()->current_module;
        }
    
        $this->managed_module = new WebModule($module_name);
        
        session()->current_module = $module_name;
    }
    
    protected function loadModulePage() {
        $this->page = new ModulePage('admin');        
    }  
    
    public function setup() {
        $this->loadModulePage();
    
        $this->page->setTitle($this->title);
    
        $this->page->setTemplate('layout.php');
        
        //Setup the css style        
        $this->page->addCssFiles(array(
            '/bootstrap/dist/css/bootstrap.css'
        ));
        
        //Setup the javascript        
        $this->page->addJavascriptFiles(array(
            "/jquery/dist/jquery.min.js",
            '/popper.js/dist/umd/popper.min.js',
            '/bootstrap/dist/js/bootstrap.min.js'
        ));
        
        $this->breadcrumbs['home'] = array(
            
        );
        
        //Content Header                
        $this->page->body()->addChild(session()->user_name, 'user_name');
        
        //Navigation
        $navigation_template = new TemplateElement('navigation.php');
        
        $navigation_template->addChild($this->name, 'page_name');
        
        $navigation_template->addChild($this->sub_name, 'sub_name');
        
        if(!empty($this->managed_module)) {
            $navigation_template->addChild($this->managed_module->getId(), 'module_id');
        }
    
        $this->page->body()->addChild($navigation_template, 'navigation');
        
        //Footer
        $this->page->body()->addChild($this->framework->getConfiguration()->version, 'version');
    }              
    
    public function action() {
        $page_content = new TemplateElement('home.php');
    
        $this->page->body()->addChild($page_content, 'content');
    }
}