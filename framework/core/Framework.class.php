<?php
/**
* Initializes and directs the flow of the framework.
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
final class Framework {
    /**
    * @var boolean A flag indicating that the framework has been initialized.
    */
    private static $initialized = false;

    /**
    * @var string The context this framework is running in such as web, command-line, ajax, etc.
    */
    private static $mode;
    
    /**
    * @var string The operating environment such as development, production, etc.
    */
    private static $environment;

    /**
    * @var string The framework version.
    */
    private static $version;
    
    /**
    * @var string The name of the current page class.
    */
    private static $current_page_class;

    /**
    * @var array A list of all classes available to the framework and their file path.
    */
    private static $available_classes;

    /**
    * @var boolean A flag telling the framework to enable caching.
    */
    public static $enable_cache = false;

    /**
     * Run the framework in web mode.
     *
     * @param string $config_ini_file The file path to the configuration ini file.
     * @return void
     */
    public static function runWeb($config_ini_file) {
        //Set the framework to web mode
        self::$mode = "web";

        //Initialize the framework
        self::initialize($config_ini_file);

        //Initialize the session
        self::initializeSession();
        
        self::getPageClass();

        //Instantiate the page class
        $current_page_class = new self::$current_page_class();

        //Display the page
        $current_page_class->display();
    }

    /**
     * Run the framework without a GUI (usually for command-line operations).
     *
     * @param string $config_ini_file The file path to the configuration ini file.
     * @return void
     */
    public static function runWithoutGUI($config_ini_file) {
        //Set the framework to no gui mode
        self::$mode = 'no_gui';

        //Initialize the framework
        self::initialize($config_ini_file);
    }
    
    /**
     * Run the framework as a SOAP API.
     *
     * @return void
     */
    public static function runSoap() {
        //Initialize the application
        self::initialize();

        //Retrieve the name of the soap class
        self::getActionClass();
        
        //Instantiate a new SOAP server object
        self::$_soap_server = new SoapServer('wsdl/' . self::$_current_action . '.wsdl');
        
        //Set the action class as the SOAP class
        self::$_soap_server->setClass(self::$_current_action);
        
        //Process the request
        self::$_soap_server->handle();
    }
    
    /**
     * Loads the Cache class.
     *
     * @return void
     */
    public static function loadCache() {
        require_once(__DIR__ . "/../data/cache/Cache.class.php");
    }
    
    /**
     * Loads the specified configuration file and runs code common to all modes.
     *
     * @param string $config_ini_file The file path to the configuration ini file.
     * @return void
     */
    private static function initialize($config_ini_file) {
        //Check to see if the framework was already initialized
        if (self::$initialized) {
            return;
        }
        
        //Initialize error handling
        self::initializeErrorHandler();
        
        //Load the cache class if caching is enabled
        if(self::$enable_cache) {
            self::loadCache();
        }
        
        //Load the configuration class
        require_once('Configuration.class.php');
        
        //Load the global framework functions
        require_once("framework_functions.php");

        //Load the framework configuration file
        config('framework')->load($config_ini_file);
        
        //Retrieve the current environment
        self::$environment = config('framework')->getParameter('environment');
        
        //Throw an initialize error if it doesn't exist
        if(empty(self::$environment)) {
            trigger_error("The environment configuration variable could not be retrieved.");
        }
        
        //Retrieve the version of this application
        self::$version = config('framework')->getParameter('version');
        
        //Set the framework autoloader
        spl_autoload_register('Framework::loadClass');
        
        if(self::$environment == 'production') {
            Assert::disable();
        }
        else {
            //Enable assertions
            Assert::enable();
        
            //Initialize debugging
            self::initializeDebugging();

            //Register the framework shutdown function
            register_shutdown_function('Framework::shutdown');
        }
        
        //Set the framework as initialized
        self::$initialized = true;
    }
    
    /**
     * Initializes error and exception handling.
     *
     * @return void
     */
    private static function initializeErrorHandler() {
        require_once(__DIR__ . "/../debug/Error.class.php");
        
        //Set error reporting to all if not in production mode
        if(self::$environment != 'production') {
            error_reporting(-1);
        }
        
        //Register the framework error handler with PHP
        set_error_handler(array('Error', "display"));
        
        //Register an exception handler that routes to the error handler
        set_exception_handler(array('Error', 'handleException'));
    }
    
    /**
     * Initializes debugging.
     *
     * @return void
     */
    private static function initializeDebugging() {
        //Load the debugging class
        self::loadClass('Debug');
    }
    
    /**
     * Starts or resumes a session.
     *
     * @return void
     */
    private static function initializeSession() {        
        //Load the class
        self::loadClass('Session');
    
        session()->start();
    }
    
    /**
     * Gets the name of the current page class.
     *
     * @return void
     */
    private static function getPageClass() {    
        if(!isset(self::$current_page_class)) {
            self::$current_page_class = request()->page;
                
            if(empty(self::$current_page_class)) {
                self::$current_page_class = config('framework')->getParameter("default_page");
            }
        }
        
        //Check to see if the page class exists
        if(!class_exists(self::$current_page_class)) {
            //If the current page class doesn't exist get the not found page and check its availability        
            self::$current_page_class = config('framework')->getParameter("not_found_page");
            
            if(!class_exists(self::$current_page_class)) {
                throw new Exception("Page class " . self::$current_page_class . " does not exist.");
            }
        }
    }

    /**
     * Loads the specified class into the application.
     *
     * @param string $class_name The name of the class to be included.
     * @return void
     */
    public static function loadClass($class_name) {
        //If caching is enabled pull this class location from the cache
        if(self::$enable_cache && empty(self::$available_classes)) {
            self::$available_classes = cache()->get('available_classes');
        }
    
        //If the list of available classes is empty retrieve it from disk
        if(empty(self::$available_classes)) {
            $classes_file_location = rtrim(config('framework')->getParameter("cache_base_directory"), '/') . '/pages.ini';
            
            if(is_readable($classes_file_location)) {
                self::loadClassesFile($classes_file_location);
            }
            else {
                require_once('ClassScanner.class.php');
            
                self::$available_classes = ClassScanner::refreshClassesFile($classes_file_location);
            }
            
            //Save the classes that were loaded into cache if enabled
            if(Framework::$enable_cache) {
                cache()->set('available_classes', self::$available_classes);
            }
        }
        
        //If the page class exists in the list, include it
        if(isset(self::$available_classes[$class_name])) {
            include_once(self::$available_classes[$class_name]);
        }
    }
    
    /**
     * Loads the ini file containing the list of class file locations.
     *
     * @param string $classes_file_location The file path to the classes ini file.
     * @return void
     */
    private static function loadClassesFile($classes_file_location) {
        self::$available_classes = parse_ini_file($classes_file_location);
    }
    
    /**
     * Wraps up execution of the framework.
     *
     * @return void
     */
    public static function shutdown() {}

    /**
     * Retrieves the current mode.
     *
     * @return string
     */
    public static function getMode() {
        return self::$mode;
    }
    
    /**
     * Retrieves the current environment.
     *
     * @return string
     */
    public static function getEnvironment() {
        return self::$environment;
    }
    
    /**
     * Retrieves the current framework version.
     *
     * @return string
     */
    public static function getVersion() {
        return self::$version;
    }
}