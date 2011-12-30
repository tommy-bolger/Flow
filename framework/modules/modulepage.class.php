<?php
namespace Framework\Modules;

use \Framework\Html\Page;
use \Framework\Core\Configuration;
use \Framework\Display\Template;

class ModulePage
extends Page {
    protected $module;
    
    public function __construct($module_name) {               
        parent::__construct();
        
        $this->module = new WebModule($module_name);
        
        //Set the default configuration name to this module's name
        Configuration::setDefault($module_name);
        
        $module_theme_path = $this->module->getThemePath();
        
        $this->setThemeDirectory($module_theme_path);
        
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
        
        Template::setBaseTemplatePath($module_templates_path);
        
        //Add the current module style's error page template to display for errors if it exists.
        if($module_name != 'admin') {
            framework()->error_handler->setTemplatePath("{$module_templates_path}/error_template.php");
        }
    }
}