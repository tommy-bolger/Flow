<?php
/**
* The conductor class for the ajax mode of the framework.
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

require_once(__DIR__ . '/page.class.php');

class Ajax
extends Page {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\AjaxError';
    
    /**
    * @var string The framework error not found class name.
    */
    protected $not_found_class = '';
    
    /**
    * @var string The ajax method to perform.
    */
    protected $method;
    
    /**
     * Initializes an instance of the framework in web mode and processes a page.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('ajax');
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
        
        if(empty($page_class_name)) {
            $this->initializeNotFound('class');
        }
        
        $method = $this->getMethod();

        //Instantiate the page class
        $current_page_class = new $page_class_name();
        
        if(!is_callable(array($current_page_class, $method))) {
            $this->initializeNotFound('method');
        }
        else {
            //Render the page
            $response = $current_page_class->$method();
            
            echo json_encode($response);
        }
    }
    
    /**
     * Executes functionality for when a page or method is not found.
     *
     * @return void
     */
    protected function initializeNotFound($type) {
        header('HTTP/1.0 404 Not Found');
        
        if($type == 'class') {
            $this->error_handler->logMessage("Ajax class '{$this->qualified_page_path}' could not be found.");
        }
        else {
            $this->error_handler->logMessage("Method '{$this->qualified_page_path}->{$this->method}()' could not be found.");
        }
        
        echo "The method you requested could not be performed at this time. Please try again later.";
        
        exit;
    }
    
    /**
     * Retrieves the method from the request.
     *
     * @return string
     */
    protected function getMethod() {
        $this->method = request()->method;
        
        return $this->method; 
    }
}