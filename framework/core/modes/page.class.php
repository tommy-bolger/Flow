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
namespace Framework\Core\Modes;

use \Framework\Debug\NotFound;

require_once(__DIR__ . '/web.class.php');

class Page
extends Web {
    /**
    * @var string The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\PageError';
    
    /**
    * @var string The framework error not found class name.
    */
    protected $not_found_class = '\\Framework\\Debug\\NotFound';
    
    /**
    * @var string The name of the current module.
    */
    protected $module_name;
    
    /**
    * @var array The subdirectory path to the page class.
    */
    protected $subdirectory_path = array();
    
    /**
    * @var array The subdirectory path to the page class as it appears in the url.
    */
    protected $subdirectory_http_path;

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
        session()->start();
        
        $this->getModule();
        
        $page_class_name = $this->getPageClass();
        
        $current_page_class = new $page_class_name();
        
        $current_page_class->setup();
        
        echo $current_page_class->render();
    }
    
    /**
     * Gets the name of the current page class.
     *
     * @return string The name of the page class.
     */
    protected function getPageClass() {
        $page_class_name = request()->page;
        
        if(empty($page_class_name)) {
            $page_class_name = "Home";
        }

        $sub_path = '';
        $is_admin_sub_path = false;
        
        if(isset(request()->get->subd)) {
            $request_subdirectories = request()->get->subd;

            $this->subdirectory_http_path = explode('/', $request_subdirectories);
            
            $this->subdirectory_path = $this->subdirectory_http_path;
            
            if(!empty($this->subdirectory_path[0])) {
                if(self::$environment == 'production' && $this->subdirectory_path[0] == 'admin' && !empty($this->subdirectory_path[1])) {
                    $this->subdirectory_path[0] = "{$this->subdirectory_path[0]}\\ controllers";
    
                    $is_admin_sub_path = true;
                }
                elseif($this->module_name != 'admin' && $this->subdirectory_path[0] == 'admin') {
                    $this->subdirectory_path[0] = "{$this->subdirectory_path[0]}\\ controllers";
    
                    $is_admin_sub_path = true;
                }
            }

            $sub_path = implode('\\ ', $this->subdirectory_path) . '\\ ';
        }

        $this->page_class_name = $page_class_name;
        
        $module_namespace = '';
        
        if($is_admin_sub_path) {
            $module_namespace = "{$this->module_name}\\ {$sub_path}";
        }
        else {
            $module_namespace = "{$this->module_name}\\ controllers\\ {$sub_path}";
        }

        if(self::$environment == 'production') {
            if($this->module_name == 'admin' && !empty($this->subdirectory_path[1]) && $this->subdirectory_path[1] == 'admin') {            
                $module_namespace = $sub_path;
            }
        }

        $page_class_path = "\\Modules\\ {$module_namespace}{$page_class_name}";

        $page_class_path = $this->formatNamespace($page_class_path);

        $class_exists = $this->classExists($page_class_path);
        
        //Check to see if the page class exists
        if(empty($class_exists)) {
            //If the current page class doesn't exist get the not found page and check its availability        
            $page_class_path = $this->not_found_class;
            
            $this->page_class_name = $page_class_path;
        }
        
        $this->qualified_page_path = $page_class_path;

        return $page_class_path;
    }
    
    /**
     * Gets the name of the current module from the request and loads its configuration.
     *
     * @return void
     */
    protected function getModule() {    
        $this->module_name = request()->module;

        if(empty($this->module_name) || $this->module_name == 'framework') {
            $this->module_name = config('framework')->default_module;
        }
        
        if(empty($this->module_name)) {
            throw new \Exception("Unable to retrieve the module to load.");
        }
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
}