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

use \Exception;
use \Framework\Core\Framework;
use \Framework\Core\Configuration;
use \Framework\Core\Loader;

class Module {
    /**
    * @var array All initialized module instances
    */
    protected static $instances = array();

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
    * @var string The base file path to this module.
    */
    protected $installation_path;
    
    /**
    * @var string The file path to the module.
    */
    protected $external_path;
    
    /**
    * @var string The vendor path of the module.
    */
    protected $vendor_path;
    
    /**
    * @var array All descriptive data about the module.
    */
    protected $data = array();
    
    /**
    * @var string The base file path to this module.
    */
    
    protected $script_file_path;
    
    /**
    * @var array The module's configuration.
    */
    protected $configuration;
    
    /**
     * Retrieves an instance of the module by its name.
     *
     * @param string $module_name The name of the module.     
     * @return Module The instance of the specified module.
     */
    public static function getInstance($module_name) {
        if(!isset(self::$instances[$module_name])) {
            self::$instances[$module_name] = new self($module_name);
        }
        
        return self::$instances[$module_name];
    }

    /**
     * Initializes the current module.
     *
     * @param string $module_name The name of the module.     
     * @return void
     */
    public function __construct($module_name) {
        $this->framework = Framework::getInstance();
    
        $this->name = $module_name;
        
        $this->installation_path = "{$this->framework->installation_path}/modules/{$module_name}";
        $this->external_path = "{$this->installation_path}/external";
        $this->vendor_path = "{$this->installation_path}/vendor";
        
        $this->script_file_path = "{$this->installation_path}/scripts";
        
        Loader::addBasePath($this->external_path);
        Loader::addBasePath($this->vendor_path);
        
        if($this->framework->mode != 'safe') {
            $this->loadData($module_name);
            
            $this->loadConfiguration();
            
            $this->framework->error_handler->setModuleId($this->id);
        }
        
        if(empty(self::$instances[$module_name])) {
            self::$instances[$module_name] = $this;
        }
    }
    
    public function __get($variable_name) {
        if($variable_name == 'configuration') {
            return $this->configuration;
        }
        
        throw new Exception("Property '{$variable_name}' is not valid.");
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
                cache()->set($this->name, serialize($this->data), 'modules');
            }
        }
        else {
            $this->data = unserialize($this->data);
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
        $this->configuration = Configuration::getInstance($this->name);
        
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
     * Retrieves the module's installation path.
     *   
     * @return string
     */
    public function getInstallationPath() {
        return $this->installation_path;
    }
    
    /**
     * Retrieves the module's script file path.
     *   
     * @return string
     */
    public function getScriptFilePath() {
        return $this->script_file_path;
    }
    
    /**
     * Retrieves the module's external file path.
     *   
     * @return string
     */
    public function getExternalPath() {
        return $this->external_path;
    }
    
    /**
     * Retrieves the module's external file path.
     *   
     * @return string
     */
    public function getVendorPath() {
        return $this->vendor_path;
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
                cache()->set('installed_modules', serialize($modules));
            }
        }
        else {
            $modules = unserialize($modules);
        }
        
        return $modules;
    }
}