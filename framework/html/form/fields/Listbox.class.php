<?php
/**
* Allows the rendering of a form select field as a listbox and performing validation on its submitted data dynamically.
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
class Listbox
extends SelectField {
    /**
     * Instantiates a new instance of Listbox.
     *      
     * @param string $listbox_name The listbox name.
     * @param string $listbox_label (optional) The listbox label.
     * @param array $options (optional) The options for the listbox field. Can be in either format: option_value => option_name OR group_name => array(option_value => option_name).
     * @param array $css_classes A list of css classes.
     * @return void
     */
	public function __construct($listbox_name, $listbox_label = "", $options = array(), $css_classes = array()) {
		parent::__construct($listbox_name, $listbox_label, $options, $css_classes);
		
		$this->setMultiSelect();
	}
}