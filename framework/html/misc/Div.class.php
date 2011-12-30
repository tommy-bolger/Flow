<?php
/**
* Allows the rendering of a <div> tag and its child elements dynamically.
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
class Div
extends Element {
    /**
     * Initializes a new instance of Div.
     *      
     * @param array $element_attributes (optional) The html attributes of the div.
     * @param string $element_text (optional) The display text inside of the div.
     * @return void
     */
    public function __construct($element_attributes = array(), $element_text = NULL) {
        parent::__construct("div", $element_attributes, $element_text);
    }
}