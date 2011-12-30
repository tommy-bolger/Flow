<?php
/**
* Allows the rendering of a form checkbox field and performing validation on its submitted data dynamically.
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
class Checkbox
extends ToggleField {
    /**
     * Instantiates a new instance of a checkbox.
     *      
     * @param string $checkbox_name The checkbox name.
     * @param string $checkbox_label (optional) The checkbox label.
     * @param boolean $is_checked (optional) Field checked flag, defaults to false.
     * @param string $checkbox_value (optional) The checkbox value, defaults to 'yes'.         
     * @param array $css_classes (optional) An array of classes.
     * @return void
     */
    public function __construct($checkbox_name, $checkbox_label = NULL, $is_checked = false, $checkbox_value = 'yes', $css_classes = array()) {
        parent::__construct("checkbox", $checkbox_name, $checkbox_label, $css_classes, $checkbox_value, $is_checked);
    }
}