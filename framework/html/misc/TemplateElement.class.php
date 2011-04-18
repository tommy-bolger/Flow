<?php
/**
* Loads a template with placeholders to replace into memory as an element.
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
class TemplateElement
extends Element {
    /**
     * Initializes a new instance of TemplateElement.
     *      
     * @param string $template_file_name The path to the template file.
     * @return void
     */
    public function __construct($template_file_name) {
        $this->setTemplate($template_file_name);
    }
    
    /**
     * Retrieves the parsed template html.
     *      
     * @return string
     */
    public function toHtml() {
        return $this->template->parseTemplate($this->toTemplateArray());
    }
}