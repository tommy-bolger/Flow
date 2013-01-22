<?php
/**
* Loads a template from a file and replaces placeholder text with specified values.
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
namespace Framework\Display;

class Template {
    /**
    * @var string The base template path to use for relative templates.
    */
    private static $base_path = array();

    /**
    * @var string The path to the template file.
    */
    private $template_file_path;
    
    /**
    * @var NULL|boolean Indicates if the template file exists at a specified path.
    */
    private $template_file_exists;
    
    /**
    * @var array The values of the template placeholders.
    */
    private $placeholder_values;
    
    /**
    * @var string The template with its placeholders replaced.
    */
    private $parsed_template;
    
    /**
    * Adds a base path for relative templates.
    * 
    * Paths are added to the beginning of the list of paths making them higher priority over previous paths. 
    * 
    * @param string $base_path The base template path.
    * @return void.
    */
    public static function addBasePath($base_path) {
        $base_path = rtrim($base_path, '/');

        self::$base_path[] = $base_path;
    }
    
    /**
    * Instantiates a new instance of Template.
    * 
    * @param string $template_file_path The path to the template file.
    * @param boolean $relative A flag determining of the template is in a directory within the current theme. 
    * @return void.
    */
    public function __construct($template_file_path, $relative = true) {
        $this->setTemplate($template_file_path, $relative);
    }
    
    /**
    * Sets the path to the template file.
    * 
    * @param string $template_file_path The path to the template file.
    * @param boolean $relative A flag determining of the template is in a directory within the current theme. 
    * @return void.
    */
    public function setTemplate($template_file_path, $relative = true) {
        if($relative) {
            $template_file_path = ltrim($template_file_path, '/');

            foreach(self::$base_path as $base_path) {
                $this->template_file_path = "{$base_path}/{$template_file_path}";

                if(is_file($this->template_file_path)) {                    
                    $this->template_file_exists = true;
                    
                    break;
                }
            }
        }
        else {
            $this->template_file_path = $template_file_path;
        }
    }
    
    /**
    * Sets the values to replace placeholders with in the template.
    * 
    * @param array $placeholder_values The placeholder values with the array key of each element as the placeholder.
    * @return void.
    */
    public function setPlaceholderValues($placeholder_values) {
        assert('is_array($placeholder_values)');
        
        $this->placeholder_values = $placeholder_values;
    }
    
    /**
    * Gets the full path of the template file.
    * 
    * @return string The file path of the template file.
    */
    public function getTemplateFilePath() {
        return $this->template_file_path;
    }

    /**
    * Gets the template string.
    * 
    * @return string The template string.
    */
    public function getTemplate() {
        return $this->template;
    }
    
    /**
    * Retrieves a template placeholder value.
    * 
    * @return string
    */
    public function __get($variable_name) {
        if(isset($this->placeholder_values[$variable_name])) {
            return $this->placeholder_values[$variable_name];
        }
        
        return '';
    }
    
    /**
    * Indicates if a template placeholder value is set.
    * 
    * @return boolean
    */
    public function __isset($variable_name) {        
        return isset($this->placeholder_values[$variable_name]);
    }

    /**
    * Retrieves all template placeholder values as a string glued together with a specified delimiter.
    * 
    * @param string $delimiter    
    * @return string
    */
    protected function getAll($delimiter = '') {
        if(empty($this->placeholder_values)) {
            return '';
        }
    
        return implode($delimiter, $this->placeholder_values);
    }
    
    /**
    * Returns if the template file exists.
    * 
    * @return boolean
    */
    public function exists() {
        if(!isset($this->template_file_exists)) {
            $this->template_file_exists = is_file($this->template_file_path);
        }
        
        return $this->template_file_exists;
    }
    
    /**
    * Returns the template with its placeholders replaced with specified values.
    * 
    * @return string The parsed template.
    */
    public function parseTemplate() {    
        if(!isset($this->parsed_template)) {
            ob_start();
            
            include($this->template_file_path);
            
            $this->parsed_template = ob_get_clean();
        }
        
        return $this->parsed_template;
    }
}