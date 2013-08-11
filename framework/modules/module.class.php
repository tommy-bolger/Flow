<?php
/**
* Base class of a module that loads a module configuration and enforces it being enabled or disabled.
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
use \Framework\Core\Configuration;
use \Framework\Core\Loader;

class Module {
    /**
    * @var object The instance of the framework.
    */
    protected $framework;

    /**
    * @var integer The ID of the module.
    */
    protected $id;
    
    /**
    * @var string The name of the module.
    */
    protected $name;
    
    /**
    * @var string The file path to the module.
    */
    protected $file_path;
    
    /**
    * @var array All descriptive data about the module.
    */
    protected $data = array();
    
    /**
    * @var array The module's configuration.
    */
    public $configuration;

    /**
     * Initializes the current module.
     *
     * @param string $module_name The name of the module.     
     * @return void
     */
    public function __construct($module_name) {
        $this->framework = Framework::getInstance();
    
        $this->name = $module_name;
        
        $this->file_path = "{$this->framework->installation_path}/modules/{$module_name}/external";
        
        Loader::addBasePath($this->file_path);
        
        $this->loadData($module_name);
        
        $this->loadConfiguration();
        
        $this->framework->error_handler->setModuleId($this->id);
    }
    
    /**
     * Retrieves the module's data.
     *
     * @return void
     */
    private function loadData() {
        if($this->framework->enable_cache) {
            $this->data = cache()->get($this->name, 'modules');
        }
        
        if(empty($this->data)) {
            $this->data = db()->getRow("
                SELECT
                    module_id,
                    module_name,
                    enabled,
                    display_name
                FROM cms_modules
                WHERE module_name = ?
            ", array($this->name));

            if(empty($this->data)) {
                throw new \Exception("Module {$this->name} does not exist.");
            }
            
            if($this->framework->enable_cache) {
                cache()->set($this->name, $this->data, 'modules');
            }
        }
        
        if(empty($this->data['enabled'])) {
            throw new \Exception("Module '{$this->name}' is not enabled.");
        }
        
        $this->id = $this->data['module_id'];
    }
    
    /**
     * Loads the module's configuration into memory.
     *   
     * @return void
     */
    private function loadConfiguration() {
        $this->configuration = new Configuration($this->name);
        
        $this->configuration->load();
    }
    
    /**
     * Retrieves the module's ID.
     *   
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Retrieves the module's name.
     *   
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Retrieves a list of all modules installed on the filesystem. 
     *   
     * @return array
     */
    public static function getInstalledModules() {
        $modules = array();
        
        $framework = Framework::getInstance();
        
        if($framework->enable_cache) {
            $modules = cache()->get('installed_modules');
        }
    
        if(empty($modules)) {
            $modules_directory = opendir($framework->installation_path . "/modules");
    
            while($module_entry = readdir($modules_directory)) {
                switch($module_entry) {
                    case '.':
                    case '..':
                        break;
                    default:
                        $modules[$module_entry] = $module_entry;
                        break;
                }
            }
            
            closedir($modules_directory);
            
            if($framework->enable_cache) {
                cache()->set('installed_modules', $modules);
            }
        }
        
        return $modules;
    }
}