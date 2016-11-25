<?php
/**
* The base class that all command line controllers extend from.
* Copyright (c) 2016, Tommy Bolger
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
namespace Framework\Core\Controllers;

use \Exception;
use \ReflectionClass;
use \Framework\Core\Framework;
use \Framework\Modules\Module;

abstract class CliController {
    /**
    * @var object The instance of the framework.
    */
    protected $framework;
    
    /**
    * @var object The instance of the current module.
    */
    protected $module;
    
    /**
    * @var array The arguments to pass to this controller.
    */
    protected $arguments = array();
    
    /**
     * Initializes a new instance of Controller.
     *
     * @param string $module_name The name of the module this instance works within.
     * @return void
     */
    public function __construct($module_name) {
        $this->framework = Framework::getInstance();
        $this->module = Module::getInstance($module_name);
    }
    
    /**
     * Sets arguments for the current instance of this class.
     *
     * @param array $arguments The arguments to pass into this instance.
     * @return void
     */
    public function setArguments(array $arguments = array()) {
        $this->arguments = $arguments;
    }
    
    /**
     * Validates arguments
     *
     * @return void
     */
    public function validateArguments() {}
    
    /**
     * Executes code before any other code is executed.
     *
     * @return void
     */
    public function init() {}
    
    /**
     * Executes the specified action for this command controller.
     *
     * @param string $method_name The name of the method to execute.
     * @param array $arguments The arguments for this method.
     * @return void
     */
    public function action($method_name, array $arguments) {    
        if(!is_callable(array($this, $method_name))) {
            throw new Exception("Method name '{$method_name}' does not exist in this class.");
            
            $this->framework->outputHelp(
                "Option '{$method_name}' is not valid.\n", 
                $this->module->getName(),
                get_called_class()
            );
        }
        
        //Target our class
        $reflector = new ReflectionClass($this);
        
        $method = $reflector->getMethod($method_name);

        //Get the parameters of a method
        $parameters = $method->getParameters();
        
        $method_arguments = array();
        
        if(!empty($parameters)) {
            $parameter_names = array();
            
            foreach($parameters as $parameter) {
                $parameter_names[] = $parameter->getName();
            }        

            //Loop through each parameter and get the type
            foreach($parameters as $parameter) {
                $parameter_name = $parameter->getName();
                
                if(isset($arguments[$parameter_name])) {
                    $argument = $arguments[$parameter_name];
                
                    if($parameter->isArray()) {
                        if(is_array($argument)) {
                            $method_arguments[] = $argument;
                        }
                        else {
                            $method_arguments[] = array($argument);
                        }
                    }
                    elseif(!is_array($argument)) {
                        $method_arguments[] = $argument;
                    }
                    else {
                        $this->framework->outputHelp(
                            "Option --{$parameter_name} requires a scalar value. An array was given instead.\n",
                            $this->module->getName(),
                            get_called_class(),
                            $method_name,
                            $parameter_names
                        );
                    }
                }
                elseif($parameter->isDefaultValueAvailable()) {
                    $method_arguments[] = $parameter->getDefaultValue();
                }
                else {
                    $this->framework->outputHelp(
                        "Option --{$parameter_name} does not have a value for it.\n", 
                        $this->module->getName(),
                        get_called_class(),
                        $method_name,
                        $parameter_names
                    );
                }
            }
        }
        
        call_user_func_array(array(
            $this,
            $method_name
        ), $method_arguments);
    }
}