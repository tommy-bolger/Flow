<?php
/**
* The conductor class for the web mode of the framework.
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

use \Framework\Debug\NotFound;

require_once(__DIR__ . '/framework.class.php');

class Web
extends Framework {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\WebError';
    
    /**
    * @var string The name of the current module.
    */
    protected $module_name;
    
    /**
    * @var array The subdirectory path to the page class.
    */
    protected $subdirectory_path;

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
        
        $sub_path = '';
        
        if(empty($page_class_name)) {
            $page_class_name = "Home";
        }
        else {        
            if(isset(request()->get->subd_1)) {
                $request_namespaces = request()->get->getByNameContaining('subd_');
                
                $this->subdirectory_path = $request_namespaces;

                $sub_path = implode('\\ ', $request_namespaces) . '\\ ';
            }
        }
        
        $this->page_class_name = $page_class_name;

        $page_class_path = "\\Modules\\ {$this->module_name}\\ {$sub_path}{$page_class_name}";
        
        //Transform the fully qualified namespace into the camel case naming convention
        $page_class_path = str_replace(array('_', '-'), ' ', $page_class_path);
        $page_class_path = ucwords($page_class_path);
        $page_class_path = str_replace(' ', '', $page_class_path);

        //Check to see if the page class exists
        if(!class_exists($page_class_path)) {
            //If the current page class doesn't exist get the not found page and check its availability        
            $page_class_path = 'NotFound';
            
            if(!class_exists($page_class_path)) {
                throw new \Exception("Page class {$page_class_path} does not exist.");
            }
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
     * Retrieves the current module name.
     *
     * @return string
     */
    public function getModuleName() {
        return $this->module_name;
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