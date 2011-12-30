<?php
/**
* Allows the rendering of a form password field and performing validation on its submitted data dynamically.
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
class Password
extends Field {
    /**
     * Instantiates a new instance of Password.
     *      
     * @param string $password_name The password name.
     * @param string $password_label (optional) The password label.
     * @param string $password_value (optional) The password default value.     
     * @param array $css_classes (optional) A list of css classes.
     * @return void
     */
    public function __construct($password_name, $password_label = "", $password_value = NULL, $css_classes = array()) {
        parent::__construct("password", $password_name, $password_label, $css_classes);
        
        $this->setDefaultValue($password_value);
    }
}