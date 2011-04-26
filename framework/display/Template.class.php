<?php
/**
* Loads a template from a file and replaces placeholder text with specified values.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Template {
    /**
    * @var string The path to the template file.
    */
    private $template_file_path;
    
    /**
    * @var string Loaded template.
    */
    private $template;
    
    /**
    * @var string The template with its placeholders replaced.
    */
    private $parsed_template;
    
    /**
    * Instantiates a new instance of Template.
    * 
    * @param string $template_file_path The path to the template file.
    * @param boolean $relative A flag determining of the template is in a directory within the current theme. 
    * @return void.
    */
    public function __construct($template_file_path, $relative = true) {
        $this->setTemplateFilePath($template_file_path, $relative);
    }
    
    /**
    * Sets the path to the template file.
    * 
    * @param string $template_file_path The path to the template file.
    * @param boolean $relative A flag determining of the template is in a directory within the current theme. 
    * @return void.
    */
    public function setTemplateFilePath($template_file_path, $relative = true) {
        if($relative) {
            $template_file_path = ltrim($template_file_path, '/');
        
            $template_file_path =  page()->getThemeDirectoryPath() . "templates/{$template_file_path}";
        }
        
        $this->template_file_path = $template_file_path;
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
    * Loads the contents of a template into memory.
    * 
    * @return void.
    */
    private function getTemplateFileContents() {
        if(Framework::$enable_cache) {
            $this->template = cache()->get($this->template_file_path);

            if(!empty($this->template)) {
                return;
            }
        }
    
        if(empty($this->template)) {
            if(is_readable($this->template_file_path)) {
                $this->template = file_get_contents($this->template_file_path);
                
                if(Framework::$enable_cache) {
                    cache()->set($this->template_file_path, $this->template);
                }
            }
            else {
                throw new SquException("Template file '{$this->template_file_path}' not found or cannot be read.");
            }
        }
    }
    
    /**
    * Returns the template with its placeholders replaced with specified values.
    * 
    * @param array $template_placeholder_content The placeholders and their values.
    * @return string The parsed template.
    */
    public function parseTemplate($template_placeholder_content = array()) {
        assert('is_array($template_placeholder_content)');
    
        $this->getTemplateFileContents();
    
        if(!empty($template_placeholder_content)) {
            if(!isset($this->parsed_template)) {
                $this->parsed_template = str_replace(
                    array_keys($template_placeholder_content), 
                    $template_placeholder_content,
                    $this->template
                );
            }
            
            return $this->parsed_template;
        }
        else {
            return $this->template;
        }
    }
}