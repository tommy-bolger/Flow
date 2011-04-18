<?php
/**
* Allows the rendering of an <img> tag dynamically.
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
class ImageElement
extends Element {
    /**
     * Initializes a new instance of ImageElement.
     *      
     * @param string $image_file_path The file path to the image relative to the document root.
     * @param array $element_attributes (optional) The html attributes of the image element.
     * @return void
     */
	public function __construct($image_file_path, $element_attributes = array()) {	
		parent::__construct("img", $element_attributes, NULL);
		
		$this->setAttribute('src', $image_file_path);
	}
	
	/**
     * Catches calls to functions not in this class and throws an exception to avoid a fatal error.
     *      
     * @param string $function_name The called function name.
     * @param array $arguments The function arguments.
     * @return void
     */
	public function __call($function_name, $arguments) {
        throw new Exception("Function name '{$function_name}' does not exist in this class.");
	}
	
	/**
	 * Renders and retrieves the image element's html.
	 *
	 * @return string
	 */
	public function toHtml() {
		return "<{$this->tag}{$this->renderAttributes()} />";
	}
}