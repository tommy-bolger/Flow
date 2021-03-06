<?php
/**
* The base error handling class of the framework for PHP versions less than 7.0.
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

use \Exception;

class ErrorLegacy {
    /**
    * @var object The current instance of the framework.
    */
    protected $framework;
    
    /**
    * @var string The unique identifier of the current caught error.
    */
    protected $incident_number;

    /**
    * @var integer The ID of the module where the error originated.
    */
    protected $module_id;
    
    /**
     * Initializes the error handler.
     *
     * @return void
     */
    public function __construct() {
        $this->framework = Framework::getInstance();
    
        //Register an exception handler that routes to the error handler
        $this->setErrorHandler();
        
        //Register the framework error handler with PHP
        $this->setExceptionHandler();
        
        ini_set('display_errors', 0);
        
        error_reporting(E_ALL);
        
        //Register a shutdown function to handle fatal errors
        register_shutdown_function(array($this, 'handleFatalError'));
    }
    
    /**
     * Sets the framework exception handler.
     *
     * @return void
     */
    protected function setExceptionHandler() {
        set_exception_handler(array($this, 'handleException'));
    }
    
    /**
     * Sets the framework error handler.
     *
     * @return void
     */
    protected function setErrorHandler() {
        set_error_handler(array($this, "handleError"));
    }

    /**
     * The exception handler of the framework.
     *
     * @return void
     */
    public function handleException(Exception $exception) {
        $this->handleError(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }

    /**
     * Displays framework/applications errors and exceptions and stops execution of the framework.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return void
     */
    public function handleError($error_code, $error_message, $error_file = '', $error_line = '', $error_trace = '') {
        //Reset the error and exception handlers to ones that don't do anything so the original error process can be completed
        set_error_handler(array($this, 'errorModeErrorHandler'));
        set_exception_handler(array($this, 'errorModeExceptionHandler'));
        
        $this->incident_number = substr(sha1(uniqid(mt_rand(), true)), 0, 9);
    
        $error_log_message = "Incident Number: {$this->incident_number}\nMessage: {$error_message}\nCode: {$error_code}\nFile: {$error_file}\nLine: {$error_line}";
        
        if(is_array($error_trace) || empty($error_trace)) {
            $error_trace = $this->getExceptionTraceFromDebug();
        }
        
        $error_log_message .= "\nTrace: {$error_trace}";                

        //Log the error into the database if in a production environment and database logging is enabled 
        if($this->framework->getEnvironment() == 'production') {
            //If the configuration has been loaded then attempt to log the error in the database.
            $framework_configuration = $this->framework->getConfiguration();
            
            if(!empty($framework_configuration) && $framework_configuration->isLoaded()) {
                try {
                    db()->insert('cms_errors', array(
                        'incident_number' => $this->incident_number,
                        'error_code' => $error_code,
                        'error_message' => $error_message,
                        'error_file' => $error_file,
                        'error_line' => $error_line,
                        'error_trace' => $error_trace,
                        'module_id' => $this->module_id,
                        'created_time' => date('Y-m-d H:i:s')
                    ));
                }
                catch(Exception $exception) {}
            }
        }
        
        $this->logMessage($error_log_message);

        echo $this->getDisplay($error_code, $error_message, $error_file, $error_line, $error_trace);
        
        exit;
    }
    
    /**
     * Initializes any error handling specific to the framework's development mode.
     *
     * @return void
     */
    public function initializeDevelopment() {
        error_reporting(-1);
    }
    
    /**
     * Intercepts and handles fatal errors.
     *                        
     * @return void
     */
    public function handleFatalError() {
        $is_error = false;
        
        $error = error_get_last();

        if(!empty($error)) {
            switch($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    $is_error = true;
                    break;
            }
        }

        if($is_error) {            
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    /**
     * Converts debug_backtrace() output into exception trace output. 
     *                        
     * @return string
     */
    protected function getExceptionTraceFromDebug() {
        $debug_backtrace = debug_backtrace(false);
        
        $exception_trace = '';
        $trace_line_number = 0;
        
        foreach($debug_backtrace as $trace) {
            if(isset($trace['line'])) {            
                $class_name = '';
                
                if(isset($trace['class'])) {
                    $class_name = $trace['class'];
                }
                
                if($class_name != 'Error') {
                    $trace_line = "#{$trace_line_number} {$trace['file']}({$trace['line']}): ";
                
                    if(!empty($class_name)) {
                        $trace_line .= "{$class_name}{$trace['type']}";
                    }
                
                    $arguments = implode(', ', $trace['args']);
                    
                    $trace_line .= "{$trace['function']}({$arguments})\n";
                    
                    $exception_trace .= $trace_line;
                    
                    $trace_line_number++;
                }
            }
        }
        
        $exception_trace .= "#{$trace_line_number} {main}";
        
        return $exception_trace;
    }
    
    /**
     * Sets the path to the error template when running in web mode.
     *
     * @param integer $module_id The module ID.                          
     * @return void
     */
    public function setModuleId($module_id) {
        $this->module_id = $module_id;
    }
    
    /**
     * Adds the error message to a log file.
     *
     * @param string $error_log_message The error message to log.                          
     * @return void
     */
    public function logMessage($error_log_message) {
        $error_log_message = "[---------- " . date('H:i:s') . " ----------]\n{$error_log_message}\n\n";
        
        error_log($error_log_message, 3, "{$this->framework->getInstallationPath()}/logs/" . date('Y-m-d') . ".log");
    }
    
    /**
     * Displays a formatted error for when running without an GUI.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return string
     */
    protected function getDisplay($error_code, $error_message, $error_file, $error_line, $error_trace) {
        $error_output = "\n=====================================================================\n" .  
            "An Error Has Occurred:\n\n" .
            "{$error_message}\n" . 
            "---------------------------------------------------------------------\n" .
            "Code:\n\n" .
            "{$error_code}\n" .
            "---------------------------------------------------------------------\n" . 
            "File:\n\n" .
            "{$error_file}\n" .
            "---------------------------------------------------------------------\n" . 
            "Line:\n\n" .
            "{$error_line}\n" . 
            "---------------------------------------------------------------------\n" .
            "Trace:\n\n" . 
            "{$error_trace}\n" . 
            "=====================================================================\n";
        
        return $error_output;
    }
    
    public function errorModeErrorHandler($error_code = '', $error_message = '', $error_file = '', $error_line = '', $error_trace = '') {}
    
    public function errorModeExceptionHandler($exception) {}
}