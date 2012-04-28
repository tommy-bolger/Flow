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
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\PageError';
    
    /**
    * @var string The name of the current module.
    */
    protected $module_name;
    
    /**
    * @var array The subdirectory path to the page class.
    */
    protected $subdirectory_path = array();

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
    public function __construct() {
        parent::__construct('web');

        session()->start();
        
        $this->getModule();
        
        $page_class_name = $this->getPageClass();

        //Instantiate the page class
        $current_page_class = new $page_class_name();

        //Display the page
        $current_page_class->display();
    }
    
    /**
     * Gets the name of the current page class.
     *
     * @return string The name of the page class.
     */
    private function getPageClass() {
        $page_class_name = request()->page;
        
        if(empty($page_class_name)) {
            $page_class_name = "Home";
        }

        $sub_path = '';
        
        if(isset(request()->get->subd)) {
            $request_subdirectories = request()->get->subd;
            
            $this->subdirectory_path = explode('/', $request_subdirectories);

            $sub_path = implode('\\ ', $this->subdirectory_path) . '\\ ';
        }

        $this->page_class_name = $page_class_name;

        $page_class_path = "\\Modules\\ {$this->module_name}\\ {$sub_path}{$page_class_name}";

        //Transform the fully qualified namespace into the camel case naming convention
        $page_class_path = str_replace(array('_', '-'), ' ', $page_class_path);
        $page_class_path = ucwords($page_class_path);
        $page_class_path = str_replace(' ', '', $page_class_path);
        
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
        
        //Check to see if the page class exists
        if(empty($class_exists)) {
            //If the current page class doesn't exist get the not found page and check its availability        
            $page_class_path = '\\Framework\\Debug\\NotFound';
            
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
     * Retrieves the subdirectories of the page class path.
     *
     * @return array
     */
    public function getSubdirectories() {
        return $this->subdirectory_path;
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