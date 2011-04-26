<?php
/**
* Allows the rendering of a form radio button field and performing validation on its submitted data dynamically.
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
class Radio
extends ToggleField {
    /**
     * Instantiates a new instance of Radio.
     *      
     * @param string $radio_name The radio button name
     * @param string $radio_label The radio button label.
     * @param string $radio_value The radio button value.
     * @param boolean $is_checked (optional) Field checked flag. Defaults to false.
     * @param array $css_classes (optional) A list of classes for this field.   
     * @return void
     */
    public function __construct($radio_name, $radio_label, $radio_value, $is_checked = false, $css_classes = array()) {
        parent::__construct("radio", $radio_name, $radio_label, $css_classes, $radio_value, $is_checked);
    }
}