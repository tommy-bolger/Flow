<?php
/**
* Base class of all pages that are a part of a module.
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
namespace Framework\Modules;

use \Framework\Core\Framework;
use \Framework\Html\Page;
use \Framework\Core\Configuration;
use \Framework\Display\Template;
use \Framework\Caching\File;

class ModulePage
extends Page {
    protected $module;
    
    public function __construct($module_name, $page_name = '', $cache_page = false) {
        File::setDefaultModuleName($module_name);
    
        parent::__construct($page_name, $cache_page);
        
        $this->module = new WebModule($module_name);
        
        //Set the default configuration name to this module's name
        Configuration::setDefault($module_name);
        
        $module_theme_path = $this->module->getThemePath();
        
        $this->setThemeDirectory($module_theme_path);
        
        $this->assets_http_path = $this->module->getAssetsHttpPath();
        
        //Set the theme css path
        $theme_css_path =  "{$module_theme_path}/css";
        
        //Set the root css path
        $root_css_path = "{$this->assets_path}/css";
        
        $this->setCssDirectories(array(
            $theme_css_path,
            $root_css_path
        ));
        
        //Set the module's javascript theme path
        $theme_javascript_path = "{$module_theme_path}/javascript";
        
        //Set the module's javascript path
        $module_javascript_path = "{$this->module->getAssetsPath()}/javascript";
        
        //Set the root javascript path
        $root_javascript_path = "{$this->assets_path}/javascript";
        
        $this->setJavascriptDirectories(array(
            $theme_javascript_path,
            $module_javascript_path,
            $root_javascript_path
        ));
        
        $module_templates_path = $this->module->getTemplatesPath();
        
        Template::addBasePath($module_templates_path);
        
        //Add the current module style's error page template to display for errors if it exists.
        if($module_name != 'admin') {
            Framework::$instance->error_handler->setTemplatePath("{$module_templates_path}/error_template.php");
        }
    }
    
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->module, $function_name), $arguments);
    }
}