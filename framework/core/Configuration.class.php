<?php
/**
* Loads, manages, and allows access to framework, module, and application configuration files.
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
final class Configuration {
    /**
    * @var string The default configuration name when calling config() with a blank parameter.
    */
    private static $default_configuration_name;

	/**
    * @var array A static list of all application configurations.
    */
	private static $configurations = array();
	
	/**
    * @var boolean A flag to determine if the current configuration is loaded.
    */
	private $loaded = false;

	/**
    * @var array The current configuration.
    */
	private $current_configuration = array();
	
	/**
    * @var array The default values of the current configuration.
    */
	private $dist_configuration = array();
	
	/**
    * @var array The combined configuration for read-only purposes.
    */
	private $full_configuration = array();
	
	/**
	 * Retrieves an instantiated configuration object of the specified configuration.
	 *
	 * @param string $configuration_name The name of the configuration.
	 * @return object The configuration object.
	 */
	public static function getConfiguration($configuration_name) {		
		if(empty($configuration_name)) {
			$configuration_name = self::$default_configuration_name;
		}
		
		if(!isset(self::$configurations[$configuration_name])) {
			self::$configurations[$configuration_name] = new Configuration();
		}
		
		return self::$configurations[$configuration_name];
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
	 * Loads configuration settings from a configuration file.
	 *
	 * @param string $config_ini_file The path to a configuration ini file.
	 * @return boolean Returns true on success and false on error.
	 */
	public function load($config_ini_file) {
        if($this->loaded) {
            return;
        }
        
        if(Framework::$enable_cache) {
            $this->full_configuration = cache()->get($config_ini_file);
            
            if(!empty($this->full_configuration)) {
                $this->loaded = true;

                return;
            }
        }
	
		//Return false if the configuration file is not available
		if(!is_readable($config_ini_file)) {
			throw new Exception("Configuration file '{$config_ini_file}' is missing or not readable.");
		}

		//Load the configuration into memory
		$this->current_configuration = parse_ini_file($config_ini_file);
		
		//Open the dist configuration file
		$dist_configuration_file = "{$config_ini_file}-DIST";
		
		if($this->loadDist($dist_configuration_file)) {
			$this->full_configuration = array_merge($this->dist_configuration, $this->current_configuration);
		}
		else {
			$this->full_configuration = &$this->current_configuration;
		}
		
		if(!empty($this->full_configuration)) {
            $this->loaded = true;
            
            if(Framework::$enable_cache) {
                cache()->set($config_ini_file, $this->full_configuration);
            }
		}
		else {
            throw new Exception("The configuration could not be loaded or is empty.");
		}
	}
	
	/**
	 * Loads dist configuration settings from a file.
	 *
	 * @param string $dist_configuration_file_path The path to the dist configuration ini file.
	 * @return boolean Returns true if successful or false if not.
	 */
	private function loadDist($dist_configuration_file_path) {
        if(is_readable($dist_configuration_file_path)) {
            //Load the config INI file into memory
            $this->dist_configuration = parse_ini_file($dist_configuration_file_path);
        }

		return !empty($this->dist_configuration);
	}
	
	/**
	 * Sets the configuration with the specified parameters.
	 *
	 * @param array $parameters The configuration parameters.
	 * @return void	 
	 */
	public function set($parameters) {
        if(!is_array($parameters)) {
            trigger_error('$parameters is not a valid array.');
        }
        
        $this->full_configuration = $parameters;
	}
	
	/**
	 * Retrieves the configuration.
	 *
	 * @return mixed Returns either the configuration or false if the configuration is not set correctly
	 */
    public function getFullConfiguration() {
        return $this->full_configuration;
    }
	
	/**
	 * Check to see if a configuration parameter exists.
	 *
	 * @param string $parameter_name The configuration parameter.
	 * @return boolean
	 */
	public function parameterExists($parameter_name) {
		return isset($this->full_configuration[$parameter_name]);
	}

	/**
	 * Retrieves a configuration value by name.
	 *
	 * @param string $parameter_name The name parameter to retrieve.
	 * @return mixed
	 */
	public function getParameter($parameter_name) {
		if(isset($this->full_configuration[$parameter_name])) {
            return $this->full_configuration[$parameter_name];
		}
		else {
            trigger_error("Item '{$parameter_name}' was not found in the configuration.");
		}
	}
	
	/**
	 * Retrieves a configuration value as a boolean by name.
	 *
	 * @param string $parameter_name The name of the parameter to retrieve.
	 * @return boolean
	 */
	public function getBooleanParameter($parameter_name) {        
        return filter_var($this->getParameter($parameter_name), FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * Retrieves a configuration value as an array by name.
	 *
	 * @param string $parameter_name The name of the parameter to retrieve.
	 * @return array
	 */
	public function getArrayParameter($parameter_name) {
		return explode(",", $this->getParameter($parameter_name));
	}
}