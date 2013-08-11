<?php
/**
* Initializes the framework.
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

class Framework {
    /**
    * @var object The instance of this object for retrieval via getInstance.
    */
    private static $instance;
    
    /**
    * @var string The path to the directory where the site is installed at.
    */
    protected $installation_path;
    
    /**
    * @var boolean A flag telling the framework to enable caching.
    */
    protected $enable_cache = false;
    
    /**
    * @var object The framework configuration.
    */
    protected $configuration;
    
    /**
    * @var string The context this framework is running in such as web, command-line, ajax, etc.
    */
    protected $mode;
    
    /**
    * @var string The operating environment such as development, production, etc.
    */
    protected $environment;
    
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Core\\Error';
    
    /**
    * @var object The framework error handler.
    */
    protected $error_handler;
    
    /**
    * @var array A list of all classes available to the framework and their file path.
    */
    protected $available_classes;
    
    /**
     * Retrieves the current instance of the framework.
     *
     * @return Framework
     */
    public static function getInstance() {
        return self::$instance;
    }
    
    /**
     * Initializes a new instance of the framework.
     *
     * @return void
     */
    public function __construct($mode = 'safe') {
        if(!empty(self::$instance)) {
            trigger_error("The framework has already been initialized.");
        }
        
        self::$instance = $this;
    
        $this->mode = $mode;
        
        $this->installation_path = dirname(dirname(__DIR__));
        
        //Load the global framework functions
        require_once("{$this->installation_path}/framework/core/framework_functions.php");
        
        //Set the framework autoloader.
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->installation_path);
        spl_autoload_extensions('.class.php');
        spl_autoload_register();
        
        //Initialize error handling
        $error_handler_class = $this->error_handler_class;

        $this->error_handler = new $error_handler_class();
        
        //When not in safe mode initialize the rest of the framework.
        if($mode != 'safe') {
            $this->configuration = new Configuration('framework');
            $this->configuration->load();

            //Retrieve the current environment
            $this->environment = $this->configuration->environment;

            //Initialize additional error handling
            switch($this->environment) {
                case 'development':
                    $this->error_handler->initializeDevelopment();
                    break;
                case 'production':
                default:
                    $this->error_handler->initializeProduction();
                    break;
            }
            
            //Add the root external directory to the loader
            Loader::addBasePath($this->installation_path . '/external');
        }
    }
    
    /**
     * Retrieves a specified property of the framework instance.
     *
     * @param string $property_name The name of the property to retrieve.     
     * @return mixed
     */
    public function __get($property_name) {
        return $this->$property_name;
    }
    
    /**
     * Indicates if a specified property is set.
     *
     * @param string $property_name The name of the property.     
     * @return boolean
     */
    public function __isset($property_name) {
        return isset($this->$property_name);
    }
    
    /**
     * Executes the runtime after initialization.
     *
     * @return void
     */
    public function run() {}
    
    /**
     * Retrieves the output of the data dump.
     *
     * @param mixed $data The data to retrieve a dump of.     
     * @return string
     */
    protected function getDebugOutput($data) {
        return var_export($data, true);
    }
    
    /**
     * Outputs a dump of the specified data.
     *
     * @param mixed $data The data to output a dump of.     
     * @return void
     */
    public function dump($data) {
        if($this->environment != 'production') {
            echo $this->getDebugOutput($data);
        }
    }
}