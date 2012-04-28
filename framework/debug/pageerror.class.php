<?php
/**
* The error handling class for the framework's web mode.
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
namespace Framework\Debug;

class PageError
extends Error {
    /**
    * @var string The path to the html template to use for display of an error in web mode for a production environment.
    */
    private $template_path;
    
    /**
     * Initializes the error handler.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets the path to the error template when running in web mode.
     *
     * @param string $template_path The path to the error template.                          
     * @return void
     */
    public function setTemplatePath($template_path) {
        if(is_file($template_path) && is_readable($template_path)) {
            $this->template_path = realpath($template_path);
        }
        else {
            throw new \Exception("Web template path '{$template_path}' cannot be read.");
        }
    }
    
    /**
     * Adds the error message to a log file.
     *
     * @param string $error_log_message The error message to log.                          
     * @return void
     */
    public function logMessage($error_log_message) {
        error_log($error_log_message);
    }
    
    /**
     * Retrieves the debug output of an error when running in web mode.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return string
     */
    public function getDebugHtml($error_code, $error_message, $error_file, $error_line, $error_trace) {
        $error_output = "
            <h1>An Error has Occurred</h1>
            <strong>Message:</strong><br />
            <hr>
            <pre>{$error_message}</pre><br />
            <strong>Code:</strong><br />
            <hr>
            <pre>{$error_code}</pre><br />
            <strong>File:</strong><br />
            <hr>
            <pre>{$error_file}</pre><br />
            <strong>Line:</strong><br />
            <hr>
            <pre>{$error_line}</pre>
        ";
        
        $error_output .= "
            <br />
            <strong>Trace:</strong><br />
            <hr>
            <pre>{$error_trace}</pre>
        ";
        
        return $error_output;
    }
    
    /**
     * Retrieves the html output of an error when running in web mode.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return string
     */
    protected function getDisplay($error_code, $error_message, $error_file, $error_line, $error_trace) {
        $error_output = "";
    
        $environment = framework()->getEnvironment();
        
        if(empty($environment) || $environment == 'production') {
            if(empty($this->template_path)) {
                $this->template_path = framework()->installation_path . '/protected/framework_error.php';
            }
            
            if(is_file($this->template_path)) {
                ob_start();
            
                include($this->template_path);
                
                $error_output = ob_get_clean();
            }
            else {
                $error_output = "<h1>An unexpected error has been encountered.</h1>";
            }
        }
        else {
            $error_output = $this->getDebugHtml($error_code, $error_message, $error_file, $error_line, $error_trace);
        }
        
        return $error_output;
    }
}