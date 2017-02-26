<?php
/**
* Loads, manages, and allows access to framework, module, and application configuration files.
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
namespace Framework\Core;

use \Exception;
use \Framework\Core\Framework;

final class Configuration {
    /**
    * @var string The default configuration name when calling getInstance with a blank parameter.
    */
    protected static $default_configuration_name;

    /**
    * @var array A static list of all application configurations.
    */
    protected static $instances = array();
    
    /**
    * @var object The instance of the framework.
    */
    protected $framework;
    
    /**
    * @var string The name of the configuration.
    */
    protected $name;
    
    /**
    * @var boolean A flag to determine if the current configuration is loaded.
    */
    protected $loaded = false;
    
    /**
    * @var array The combined configuration for read-only purposes.
    */
    protected $full_configuration = array();
    
    /**
     * Retrieves an instantiated configuration object of the specified configuration.
     *
     * @param string $configuration_name The name of the configuration.
     * @return object The configuration object.
     */
    public static function getInstance($configuration_name) {        
        if(empty($configuration_name)) {
            $configuration_name = self::$default_configuration_name;
        }
        
        if(!isset(self::$instances[$configuration_name])) {
            self::$instances[$configuration_name] = new configuration($configuration_name);
        }
        
        return self::$instances[$configuration_name];
    }
    
    /**
     * Sets the name of the default returned configuration.
     *
     * @param string $configuration_name The name of the default configuration.
     * @return void
     */
    public static function setDefault($configuration_name) {
        self::$default_configuration_name = $configuration_name;
    }
    
    /**
     * Initializes a new configuration instance.
     *
     * @param string $configuration_name The name of the configuration.
     * @return void
     */
    public function __construct($configuration_name) {
        if(!empty(self::$instances[$configuration_name])) {
            throw new Exception("Configuration '{$configuration_name}' has already been instantiated. Use Configuration::getInstance(configuration_name) to retrieve it.");
        }
        else {
            self::$instances[$configuration_name] = $this;
        }
    
        $this->name = $configuration_name;
        
        $this->framework = Framework::getInstance();
    }
    
    /**
     * Retrieves the loaded state of the configuration.
     *
     * @return boolean
     */
    public function isloaded() {
        return $this->loaded;
    }
    
    /**
     * Sets the configuration with the specified parameters.
     *
     * @param array $parameters The configuration parameters.
     * @return void     
     */
    public function set($parameters) {
        if(!is_array($parameters)) {
            throw new Exception('$parameters is not a valid array.');
        }
        
        $this->full_configuration = $parameters;
    }
    
    /**
     * Sets a configuration parameter.
     *
     * @param string $parameter_name The configuration parameter name.
     * @param string $parameter_value The configuration parameter value.
     * @return mixed
     */
    public function __set($parameter_name, $parameter_value) {
        $this->full_configuration[$parameter_name] = $parameter_value;
    }
    
    /**
     * Writes the base configuration settings to the framework's protected folder.
     *
     * @return void
     */
    public function writeBaseFile() {
        $new_configuration = 
            "site_key = \"{$this->site_key}\"\n" . 
            "database_dsn = \"{$this->database_dsn}\"\n" . 
            "database_user = \"{$this->database_user}\"\n" . 
            "database_password = \"{$this->database_password}\""
        ;
        
        file_put_contents("{$this->framework->getInstallationPath()}/protected/configuration.ini", $new_configuration);
    }
    
    /**
     * Loads configuration settings from the framework configuration file in the protected directory.
     *
     * @return void
     */
    public function loadFrameworkBaseFile() {
        $config_full_path = $this->framework->installation_path . "/protected/configuration.ini";
        
        $config_is_readable = is_readable($config_full_path);
        
        $base_configuration = array();
        
        if($this->framework->mode != 'safe') {
            if(!$config_is_readable) {
                throw new Exception("Configuration file '{$config_full_path}' is missing or not readable.");
            }
            
            $base_configuration = parse_ini_file($config_full_path);
            
            if(empty($base_configuration)) {
                throw new Exception("The base configuration file '{$config_full_path}' could not be loaded or is empty.");
            }
        }
        else {
            if($config_is_readable) {
                $base_configuration = parse_ini_file($config_full_path);
                
                if(empty($base_configuration)) {
                    $base_configuration = array();
                }
            }
        }
        
        $this->full_configuration = $base_configuration;
    }
    
    /**
     * Casts configuration parameters into their appropriate data type.
     *
     * @param array $parameters The configuration parameters to cast. Each row must contain the parameter name, parameter value, and data type.
     * @return array The casted configuration parameters.
     */
    protected function castConfigurationParameters($parameters) {
        assert('is_array($parameters)');
        
        $casted_parameters = array();
        
        if(!empty($parameters)) {            
            foreach($parameters as $parameter) {
                $data_type = $parameter['data_type'];
                $parameter_name = $parameter['parameter_name'];
                $uncasted_value = $parameter['parameter_value'];
                $casted_value = NULL;
    
                switch($data_type) {
                    case 'integer':
                        $casted_value = (integer)$uncasted_value;
                        break;
                    case 'float':
                        $casted_value = (float)$uncasted_value;
                        break;
                    case 'boolean':
                        $casted_value = (boolean)$uncasted_value;
                        break;
                    case 'array':
                        $casted_value = explode(',', $uncasted_value);
                        
                        if(!empty($casted_value) && !is_array($casted_value)) {
                            throw new Exception("Configuration parameter '{$parameter_name}' is not a valid array.");
                        }
                        break;
                    default:
                        $casted_value = $uncasted_value;
                        break;
                }
                
                $casted_parameters[$parameter_name] = $casted_value;
            }
        }
        
        return $casted_parameters;
    }
    
    /**
     * Loads a configuration.
     *  
     * @return void
     */
    public function load() {    
        $full_configuration = array();
    
        if($this->framework->enable_cache) {
            $full_configuration = cache()->get($this->name, 'configurations');
        }
        
        if(empty($full_configuration)) {        
            $where_clause = '';
            $placeholder_values = array();
        
            if($this->name != 'framework') {
                $where_clause = 'm.module_name = ?';
                $placeholder_values[] = $this->name;
            }
            else {            
                $where_clause = 'module_id IS NULL';
            }
        
            $database_configuration = db()->getAll("
                SELECT 
                    cp.parameter_name,
                    COALESCE(cp.value, cp.default_value) AS parameter_value,
                    pdt.data_type
                FROM cms_configuration_parameters cp
                LEFT JOIN cms_modules m USING (module_id)
                LEFT JOIN cms_parameter_data_types pdt USING (parameter_data_type_id)
                WHERE {$where_clause}
            ", $placeholder_values);

            $database_configuration = $this->castConfigurationParameters($database_configuration);

            if(!empty($database_configuration)) {
                //Merge the two configurations into one
                $this->full_configuration = array_merge($this->full_configuration, $database_configuration);
            
                if($this->framework->enable_cache) {
                    cache()->set($this->name, serialize($this->full_configuration), 'configurations');
                }
                
                $this->loaded = true;
            }
        }
        else {
            if(!is_array($full_configuration)) {
                $this->full_configuration = unserialize($full_configuration);
            }
        }
        
    }
    
    /**
     * Retrieves the configuration.
     *
     * @return mixed Returns either the configuration or false if the configuration is not set correctly
     */
    public function getAll() {
        return $this->full_configuration;
    }
    
    /**
     * Magic function that checks to see if a configuration parameter exists.
     *
     * @param string $parameter_name The configuration parameter name.
     * @return boolean
     */
    public function __isset($parameter_name) {
        return isset($this->full_configuration[$parameter_name]);
    }
    
    /**
     * Retrieves a configuration parameter by name.
     *
     * @param string $parameter_name The configuration parameter name.
     * @return mixed
     */
    public function __get($parameter_name) {
        if(isset($this->full_configuration[$parameter_name])) {
            return $this->full_configuration[$parameter_name];
        }
        else {
            throw new Exception("Item '{$parameter_name}' was not found in the configuration.");
        }
    }
    
    /**
     * Retrieves several parameter values by name.
     *
     * @param array $parameter_names The names of the parameters to retrieve in this format: array('parameter_1', 'parameter_2', etc...).
     * @return array
     */
    public function getParameters($parameter_names) {
        assert('is_array($parameter_names) && !empty($parameter_names)');
        
        $parameter_values = array();
        
        foreach($parameter_names as $parameter_name) {
            if(isset($this->full_configuration[$parameter_name])) {
                $parameter_values[$parameter_name] = $this->full_configuration[$parameter_name];
            }
        }
        
        if(count($parameter_values) < count($parameter_names)) {
            $missing_parameters = array_diff_key($parameter_names_as_keys, $parameter_values);
            
            $missing_parameters_list = implode(', ', array_keys($missing_parameters));
            
            throw new Exception("Parameters '{$missing_parameters_list}' could not be found in the configuration.");
        }
        
        return $parameter_values;
    }
}
