<?php
/**
* Allows the rendering of a <p> tag and its child elements dynamically.
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
class Paragraph
extends Element {
    /**
	 * Initializes a new instance of Paragraph.
	 *
	 * @param string $element_value (optional) The text of the paragraph.
	 * @param array $element_attributes (optional) The attributes of the paragraph.	 	 
	 * @return void
	 */
	public function __construct($element_value = NULL, $element_attributes = array()) {
		parent::__construct("p", $element_attributes, $element_value);
	}
}