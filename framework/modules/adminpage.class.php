<?php
/**
* Page class for a module's admin section.
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
use \Framework\Html\Table\EditTable;
use \Framework\Display\Template;

class AdminPage
extends ModulePage {
    /**
     * Initializes a new instance of AdminPage.
     *      
     * @param string $module_name The name of the module currently being managed.
     * @param string $page_name (optional) The name of the page. Defaults to an empty string.
     * @param string $cache_page (optional) Indicates if the page should cache its output. Defaults to false.  
     * @return void
     */
    public function __construct($module_name, $page_name = '', $cache_page = false) {
        parent::__construct('admin', $page_name, $cache_page);
    
        //Set the module name to insert into urls in EditTable        
        EditTable::setModuleName($module_name);
        
        //Add the admin assets path to the list of template paths
        $admin_templates_path = str_replace('admin', "{$module_name}/admin", $this->module->getTemplatesPath());

        Template::addBasePath($admin_templates_path);
        
        $framework = Framework::getInstance();
        
        $module_path = "{$framework->getInstllationPath()}/modules/{$module_name}";
        
        $module_javascript_path = "{$module_path}/assets/javascript";
        
        $module_admin_assets_path = "{$module_path}/admin/assets";
        
        $module_admin_javascript_path = "{$module_admin_assets_path}/javascript";
        
        $module_admin_theme_path = "{$module_admin_assets_path}/styles/{$this->module->configuration->theme}";
        
        $module_admin_css_theme_path = "{$module_admin_theme_path}/css";
        
        $module_admin_javascript_theme_path = "{$module_admin_theme_path}/javascript";
        
        $this->prependCssDirectories(array(
            $module_admin_css_theme_path
        ));

        $this->prependJavascriptDirectories(array(
            $module_javascript_path,
            $module_admin_javascript_path,
            $module_admin_javascript_theme_path,
        ));
    }
}