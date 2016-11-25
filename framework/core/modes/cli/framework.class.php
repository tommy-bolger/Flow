<?php
/**
* The conductor class for the cli mode of the framework to handle command line processing.
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
namespace Framework\Core\Modes\Cli;

use \Exception;
use \Framework\Core\Framework as BaseFramework;
use \Framework\Modules\Module;

require_once(dirname(dirname(__DIR__)) . '/framework.class.php');

class Framework
extends BaseFramework {
    /**
    * @var array Stores the arguments passed to the current script.
    */
    public $input_arguments;

    /**
    * @var array Stores the parsed arguments to pass to the command controller.
    */
    public $controller_arguments = array();

    /**
    * @var boolean Indicates if output should be displayed.
    */
    protected $output_enabled = true;
    
    /**
    * @var string The name of the module.
    */
    protected $module_name;
    
    /**
    * @var string The namespace of the target command class being initialized relative to the module.
    */
    protected $command_class_name;
    
    /**
    * @var string The name of the method to call.
    */
    protected $method_name;

    /**
     * Initializes an instance of the framework in cli mode.
     *
     * @param boolean $output_on_production Indicates if output should be displayed when running in the production environment.     
     * @return void
     */
    public function __construct($output_on_production = true) {
        parent::__construct('cli');    
        
        if($this->environment == 'production' && !$output_on_production) {
            $this->output_enabled = false;
        }
    }
    
    /**
     * Ouputs the help message for running a command.
     *
     * @param string $first_message (optional) Any message to show before displaying the command format help text.
     * @return void
     */
    public function outputHelp($first_message = '', $module = '', $class = '', $method = '', array $parameters = array()) {
        if(!empty($first_message)) {
            $first_message .= "\n";
        }
        
        if(empty($module)) {
            $module = '<module_name>';
        }
        
        if(empty($class)) {
            $class = '<class_path>';
        }
        else {
            $class_split = explode('Cli\\', $class);
            
            if(count($class_split) > 1) {
                $class = strtolower($class_split[1]);
            }
            else {
                $class = strtolower($class);
            }
        }
        
        if(empty($method)) {
            $method = '<method_name>';
        }
        else {
            $method = str_replace('action', '', $method);
        }
        
        $parameter_display = ' --argument1=value1 --argument2=value2 ... --argumentN=valueN';
        
        if(!empty($parameters)) {   
            $parameter_display = '';
        
            foreach($parameters as $index => $parameter_name) {
                $parameter_number = $index + 1;
                
                $parameter_display .= " --{$parameter_name}=value{$parameter_number}";
            }
        }
    
        echo "{$first_message}" . 
            "Usage is:\n" . 
            "php {$module} {$class} {$method}{$parameter_display}\n";
        
        
        exit;
    }
    
    /**
     * Sets arguments for this instance from the $_SERVER argv property.
     *
     * @return void
     */
    protected function setArguments() {
        if(!isset($_SERVER['argv']) || !empty($_SERVER['SERVER_ADDR'])) {
            throw new Exception("This process can only run from the command line.");
        }
        
        $this->input_arguments = $_SERVER['argv'];
        
        $input_arguments = $this->input_arguments;
        
        if(count($this->input_arguments) < 4) {
            $this->outputHelp('This command requires a module name and class path at minimum.');
        }
        
        //Remove the script name from the list of arguments.
        unset($input_arguments[0]);
        
        //Remove the module name from the list of arguments.
        $installed_modules = Module::getInstalledModules();
        
        $module_name = array_shift($input_arguments);
        
        if(!isset($installed_modules[$module_name])) {
            $modules = implode(',', $installed_modules);
        
            $this->outputHelp("Specified module '{$module_name}' is incorrect. Valid modules are {$modules}.");
        }
        
        $this->module_name = $module_name;
        
        //Remove the class name from the list of arguments and parse it to a namespaced class.
        $class_name = array_shift($input_arguments);
        
        $class_name = str_replace('/', '\\', $class_name);
        $class_name = "\Modules\\{$module_name}\\controllers\\cli\\{$class_name}";

        if(!$this->classExists($class_name)) {
            $this->outputHelp("Specified class '{$class_name}' does not exist.");
        }
        
        $this->command_class_name = $class_name;
        
        $this->method_name = 'action' . array_shift($input_arguments);
        
        $controller_arguments = array();
        
        if(!empty($input_arguments)) {
            foreach($input_arguments as $argument) {
                if(strpos($argument, '=') !== false) {
                    $argument_split = explode('=', $argument);
                    
                    $argument_name = str_replace('-', '', $argument_split[0]);
                    
                    $argument = $argument_split[1];
                    
                    if(strpos($argument, ',') !== false) {
                        $argument = explode(',', $argument);
                    }
                    
                    $controller_arguments[$argument_name] = $argument;
                }
                else {
                    $argument_name = str_replace('-', '', $argument);
                
                    $controller_arguments[$argument_name] = true;
                }
            }
            
            $this->controller_arguments = $controller_arguments;
        }
    }
    
    /**
     * Executes the runtime after initialization.
     *
     * @return void
     */
    public function run() {            
        $this->setArguments();
        
        $command_class_name = $this->command_class_name;
        
        $current_command_class = new $command_class_name($this->module_name);
        
        $current_command_class->init();
        
        $current_command_class->action($this->method_name, $this->controller_arguments);
    }
    
    /**
     * Outputs data.
     *
     * @param string $output.  
     * @return void
     */
    public function cout($output) {
        if($this->output_enabled) {
            echo $output;
        }
    }
    
    /**
     * Outputs data ending with a line break.
     *
     * @param string $output.  
     * @return void
     */
    public function coutLine($output) {
        $this->cout("{$output}\n");
    }
}
