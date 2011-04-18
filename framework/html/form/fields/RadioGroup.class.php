<?php
/**
* Allows the rendering of a group of form radio buttons and performing validation on its submitted data dynamically.
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
class RadioGroup
extends ToggleGroup {
    /**
     * Instantiates a new instance of RadioGroup.
     *      
     * @param string $radio_group_name The radio group name.
     * @param string $radio_group_label (optional) The radio group label.
     * @param array $options (optional) The options for the group. Can have either format: array(value => option_label) or array(option_label).     
     * @param array $css_classes A list of classes for this field.
     * @return void
     */
	public function __construct($radio_group_name, $radio_group_label = NULL, $options = array(), $css_classes = array()) {
		parent::__construct("radio", $radio_group_name, $radio_group_label, $options, $css_classes);
	}
}