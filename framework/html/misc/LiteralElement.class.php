<?php
/**
* Adds straight text to the page renderer as an element.
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
class LiteralElement {
    /**
    * @var string The id attribute of the element.
    */
    private $id;
    
    /**
    * @var string The contents of the element.
    */
    private $html;

    /**
     * Initializes a new instance of Div.
     *      
     * @param string $element_id The element's id.
     * @param string $element_html The contents of the element.
     * @return void
     */
    public function __construct($element_id, $element_html) {
        $this->id = $element_id;
        $this->html = $element_html;
    }
    
    /**
     * Retrieves the id of the element.
     *      
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Retrieves the html of the element and its contents as an array indexed by its id for use with parsing the element's template.
     *
     * @return array
     */
    public function getTemplateArray() {
        return array('{{' . strtoupper($this->id) . '}}' => $this->html);
    }
    
    /**
     * Renders and retrieves the element's html.
     *      
     * @return string
     */
    public function toHtml() {
        return $this->html;
    }
}