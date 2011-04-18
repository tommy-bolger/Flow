<?php
/**
* Loads a plantext template with no placeholders into memory as an element.
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
class PlainTemplate {
    /**
    * @var string The file path to the template file.
    */
    private $template_path;

    /**
     * Initializes a new instance of PlainTemplate.
     *      
     * @param string $template_path The path to the template file.
     * @return void
     */
    public function __construct($template_path) {
        $this->template_path = $template_path;
    }
    
    /**
     * Renders and retrieves the element's html.
     *      
     * @return string
     */
    public function toHtml() {
        $template_html = '';
            
        if(Framework::$enable_cache) {
            $template_html = cache()->get($this->template_path);
        }
        else {
            assert('is_readable($this->template_path)');
            
            $template_html = file_get_contents($this->template_path);
            
            if(Framework::$enable_cache) {
                cache()->set($this->template_path, $template_html);
            }
        }
        
        return $template_html;
    }
}