<?php
/**
* Base class of a module that loads a module configuration and enforces it being enabled or disabled.
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
class Module
extends Page {
    protected $module_name;
    
    protected $module_assets_path;
    
    protected $module_theme_path;
    
    protected $module_javascript_path;
    
    protected $module_images_path;
    
    protected $module_files_path;

    public function __construct($module_name) {
        $this->module_name = $module_name;
    
        if(!$this->isEnabled()) {
            throw new Exception("Module '{$module_name}' is not enabled.");
        }
    
        parent::__construct();
        
        //Set the default configuration name to this module's name
        Configuration::setDefault($module_name);
        
        $this->loadModuleConfiguration($module_name);
        
        //Set the module's assets path
        $this->module_assets_path = "{$this->assets_path}/modules/{$this->module_name}";
        
        //Set the module's style path
        if(config()->parameterExists('theme')) {
            $this->module_theme_path = "{$this->module_assets_path}/styles/" . config()->getParameter('theme');
            
            $this->setThemeDirectory($this->module_theme_path);
        }
        
        //Set the module's javascript path
        $this->module_javascript_path = "{$this->module_assets_path}/javascript";
        
        $this->setJavascriptDirectory($this->module_javascript_path);
        
        $this->module_images_path = "{$this->module_assets_path}/images";
        
        $this->module_files_path = "{$this->module_assets_path}/files";
    }
    
    public function loadModuleConfiguration($module_name) {
        $module_configuration = array();
        
        if(Framework::$enable_cache) {
            $module_configuration = cache()->get($module_name, 'module_configuration');
        }
        
        if(empty($module_configuration)) {
            $module_configuration = db()->getMappedColumn("
                SELECT 
                    mc.parameter_name,
                    COALESCE(mc.parameter_value, mc.parameter_default_value) AS parameter_value
                FROM modules m 
                JOIN module_configurations mc USING (module_id)
                WHERE m.module_name = ?
            ", array($module_name));
            
            if(!empty($module_configuration)) {
                if(Framework::$enable_cache) {
                    cache()->set($module_name, $module_configuration, 'module_configuration');
                }
            }
        }
        
        config($module_name)->set($module_configuration);
    }
    
    private function isEnabled() {
        if(Framework::$enable_cache) {
            $available_modules = cache()->get('modules');
            
            if(empty($available_modules)) {
                $available_modules = db()->getGroupedColumn("
                    SELECT module_name, enabled
                    FROM modules
                ");
                
                if(!empty($available_modules)) {                    
                    cache()->set('modules', $available_modules, '', 300);
                }
            }
            
            if(isset($available_modules[$this->module_name])) {
                return $available_modules[$this->module_name];
            }
        }
    
        return db()->getOne("
            SELECT enabled
            FROM modules
            WHERE module_name = ?
        ", array($this->module_name));
    }
}