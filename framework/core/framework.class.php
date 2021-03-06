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

use \Framework\Utilities\Encryption;
use \Framework\Caching\Cache;

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
    * @var object An instance of the framework's caching module.
    */
    protected $cache;
    
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
    * @var object The legacy framework error handler class name for PHP versions less than 7.
    */
    protected $legacy_error_handler_class = '\\Framework\\Core\\ErrorLegacy';
    
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
        
        $error_handler_class = NULL;
        
        if(PHP_MAJOR_VERSION < 7) {
            spl_autoload_register();
            
            $error_handler_class = $this->legacy_error_handler_class;
        }
        else {
            /*
                In PHP 7 the default spl_autoload_register throws the ParseError throwable that circumvents any exception handler
                regardless of if one has been set. This in turn causes a fatal error. In order to prevent this any classes loaded
                via spl_autoload now need to be wrapped in a try/catch block and sent over to the handleThrowable() function of the
                error handler object manually.
            */
            spl_autoload_register(function($class_name) {
                try {
                    spl_autoload($class_name);
                }
                catch(Throwable $throwable) {
                    $this->error_handler->handleThrowable($throwable);
                }
            });
            
            $error_handler_class = $this->error_handler_class;
        }
        
        //Initialize error handling
        $this->error_handler = new $error_handler_class();
        
        //Add the root external directory to the loader
        Loader::addBasePath("{$this->installation_path}/external");
        Loader::addBasePath("{$this->installation_path}/vendor");
        Loader::load('autoload.php', true, false);
        
        $this->cache = Cache::getInstance('framework');
        
        $this->configuration = new Configuration('framework');
        
        $this->configuration->loadFrameworkBaseFile();
        
        //When not in safe mode initialize the rest of the framework.
        if($mode != 'safe') {            
            //Add the base salt to the encryption object
            Encryption::setBaseSalt($this->configuration->site_key);
            
            //Load the database config
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
        }
    }
    
    /**
     * Indicates if a class exists at the fully qualified namespace.
     *
     * @param string $page_class_path The fully qualified namespace path to the class.     
     * @return boolean
     */
    protected function classExists($page_class_path) {
        /*
         * Need to call class_exists() in a try/catch block because of PHP bug #52339 found at: 
         * https://bugs.php.net/bug.php?id=52339&edit=1
         */
        $class_exists = false; 
        
        try {
            $class_exists = class_exists($page_class_path);
        }
        catch(\Exception $e) {
            $class_exists = false;
        }
        
        return $class_exists;
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
    
    /**
     * Retrieves the installation path of the framework.
     *
     * @return string
     */
    public function getInstallationPath() {
        return $this->installation_path;
    }
    
    /**
     * Retrieves the framework configuration.
     *
     * @return object
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * Retrieves the framework environment.
     *
     * @return string
     */
    public function getEnvironment() {
        return $this->environment;
    }
    
    /**
     * Retrieves the framework mode.
     *
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }
    
    /**
     * Retrieves the framework cache object.
     *
     * @return object
     */
    public function getCache() {
        return $this->cache;
    }
    
    /**
     * Retrieves the framework error handler object.
     *
     * @return object
     */
    public function getErrorHandler() {
        return $this->error_handler;
    }
}