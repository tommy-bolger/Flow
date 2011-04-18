<?php
/**
* Allows the rendering of a group of form checkbox fields and performing validation on its submitted data dynamically.
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
class CheckboxGroup
extends ToggleGroup {
    /**
     * Instantiates a new instance of a CheckboxGroup.
     *      
     * @param string $checkbox_group_name The checkbox group name.
     * @param string $checkbox_group_label (optional) The checkbox group label.
     * @param array $options (optional) The options for the group. Can have either format: array(value => option_label) or array(option_label).     
     * @param array $css_classes (optional) A list of classes.
     * @return void
     */
	public function __construct($checkbox_group_name, $checkbox_group_label = NULL, array $options = array(), $css_classes = array()) {
		parent::__construct("checkbox", $checkbox_group_name, $checkbox_group_label, $options, $css_classes);
	}
}