<?php
/**
* The conductor class for the web page mode of the framework.
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
namespace Framework\Core\Modes\Page;

use \Framework\Core\Modes\Web;
use \Framework\Modules\Module;

require_once(dirname(__DIR__) . '/web.class.php');

class Framework
extends Web {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Core\\Modes\\Page\\Error';

    /**
    * @var array The subdirectory path to the page class.
    */
    protected $subdirectory_path = array();
    
    /**
    * @var array The subdirectory path to the page class as it appears in the url.
    */
    protected $subdirectory_http_path;
    
    /**
    * @var string The name of the requested page as specified in the url.
    */    
    protected $page_http_name;        

    /**
    * @var string The name of the current page class.
    */
    protected $page_class_name;
    
    /**
    * @var string The fully qualified path of the class.
    */
    protected $qualified_page_path;
    
    /**
     * Initializes an instance of the framework in web mode and processes a page.
     *
     * @return void
     */
    public function __construct($mode = 'page') {
        parent::__construct($mode);
    }
    
    /**
     * Executes the runtime after initialization.
     *
     * @return void
     */
    public function run() {            
        $page_class_name = $this->getPageClass();
        
        $current_page_class = new $page_class_name();
        
        $current_page_class->init();
        
        $current_page_class->setup();
        
        $current_page_class->action();
        
        $current_page_class->render();
    }
    
    /**
     * Executes the maintenance mode for the current mode. 
     *
     * @return void
     */
    protected function runMaintenance() {    
        require_once("{$this->installation_path}/protected/maintenance_mode.php");
    }
    
    /**
     * Retrieves the parsed request uri.
     *
     * @return string
     */
    public function getParsedUri() {
        $unparsed_uri = parent::getParsedUri();

        $last_uri_index = strlen($unparsed_uri) - 1;

        if($unparsed_uri{$last_uri_index} == '/') {
            $unparsed_uri .= 'home';
        }
        
        $parsed_uri = explode('/', $unparsed_uri);

        if(empty($parsed_uri[0])) {
            array_shift($parsed_uri);
        }

        return $parsed_uri;
    }
    
    /**
     * Gets the name of the current page class.
     *
     * @return string The name of the page class.
     */
    protected function getPageClass() {
        $uri_segments = $this->getParsedUri();
    
        $this->page_http_name = array_pop($uri_segments);
        
        $page_class_name = $this->page_http_name;                
        
        if($page_class_name == 'home') {
            $page_class_name = "Home";
        }
        
        if(!empty($uri_segments)) {
            $this->subdirectory_http_path = $uri_segments;
            
            $this->subdirectory_path = $this->subdirectory_http_path;
        }
        
        $first_subdirectory = '';
        
        if(!empty($this->subdirectory_path[0])) {
            $first_subdirectory = $this->subdirectory_path[0];
        }
        
        $installed_modules = Module::getInstalledModules();
        
        //If the first subdirectory is not an installed module name then use the default module
        if(!isset($installed_modules[$first_subdirectory])) {
            $first_subdirectory = $this->configuration->default_module;
        
            array_unshift($this->subdirectory_path, $first_subdirectory);
        }
        
        $second_subdirectory = '';
        
        if(!empty($this->subdirectory_path[1])) {
            $second_subdirectory = $this->subdirectory_path[1];
        }
        
        //If the second subdirectory is admin then append the controllers namespace to it
        if($second_subdirectory == 'admin') {
            $this->subdirectory_path[1] = "{$second_subdirectory}\\ controllers\\ {$this->mode}";
        }
        //If the module name is the admin append the controllers namespace to the first subdirectory
        else {
            $this->subdirectory_path[0] = "{$first_subdirectory}\\ controllers\\ {$this->mode}";
        }

        $sub_path = implode('\\ ', $this->subdirectory_path) . '\\ ';

        $this->page_class_name = $page_class_name;

        $page_class_path = "\\Modules\\{$sub_path}{$page_class_name}";

        $page_class_path = $this->formatNamespace($page_class_path);

        $class_exists = $this->classExists($page_class_path);
        
        //Check to see if the page class exists
        if(empty($class_exists)) {
            $this->qualified_page_path = $page_class_path;  
        
            //If the current page class doesn't exist get the not found page   
            $page_class_path = '\\Framework\\Core\\Modes\\Page\\NotFound';
            
            $this->page_class_name = $page_class_path;
        }
        else {
            $this->qualified_page_path = $page_class_path;        
        }

        return $page_class_path;
    }
    
    /**
     * Transforms an unformatted fully qualified namespace into one that is syntactically correct for PHP. 
     *
     * @param string $unformatted_namespace The unformatted namespace.     
     * @return string The formatted fully qualified namespace.
     */
    protected function formatNamespace($unformatted_namespace) {
        //Transform the fully qualified namespace into the camel case naming convention
        $formatted_namespace = str_replace(array('_', '-'), ' ', $unformatted_namespace);
        $formatted_namespace = ucwords($formatted_namespace);
        $formatted_namespace = str_replace(' ', '', $formatted_namespace);
        
        return $formatted_namespace;
    }
    
    /**
     * Retrieves the subdirectories of the page class path.
     *
     * @return array
     */
    public function getSubdirectories() {
        return $this->subdirectory_path;
    }
    
    /**
     * Retrieves the subdirectories of the page class path as shown in the url.
     *
     * @return array
     */
    public function getHttpSubPath() {
        return $this->subdirectory_http_path;
    }
    
    /**
     * Retrieves the page name form the requested url.
     *
     * @return string
     */
    public function getPageHttpName() {
        return $this->page_http_name;
    }
            
    /**
     * Retrieves the current page class name.
     *
     * @return string
     */
    public function getPageClassName() {
        return $this->page_class_name;
    }
    
    /**
     * Retrieves the fully qualified path to the class.
     *
     * @return string
     */
    public function getQualifiedPagePath() {
        return $this->qualified_page_path;
    }
    
    /**
     * Retrieves the output of the data dump.
     *
     * @param mixed $data The data to retrieve a dump of.     
     * @return string
     */
    protected function getDebugOutput($data) {
        return "<pre class=\"normal_size_text\">\n" . htmlentities(var_export($data, true), ENT_QUOTES, 'UTF-8') . "\n</pre>";
    }
}