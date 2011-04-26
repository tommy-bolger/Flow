<?php
/**
* Allows the rendering of a form dropdown field and performing validation on its submitted data dynamically.
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
class Dropdown
extends SelectField {
    /**
     * Instantiates a new instance of Dropdown.
     *      
     * @param string $dropdown_name The dropdown name.
     * @param string $dropdown_label (optional) The dropdown label.
     * @param array $options (optional) The options for the dropdown field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name).
     * @param array $css_classes (optional) A list of css classes.
     * @return void
     */
    public function __construct($dropdown_name, $dropdown_label = "", $options = array(), $css_classes = array()) {
        parent::__construct($dropdown_name, $dropdown_label, $options, $css_classes);
    }
}