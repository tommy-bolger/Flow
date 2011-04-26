<?php
/**
* Allows the rendering of a form hidden field dynamically.
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
class Hidden
extends Field {
    /**
     * Initializes a new instance of Hidden.
     *      
     * @param string $hidden_name The name of the hidden field.
     * @param mixed $hidden_value (optional) The hidden field's default value.   
     * @return void
     */
    public function __construct($hidden_name, $hidden_value = NULL) {
        parent::__construct("hidden", $hidden_name);
        
        $this->setDefaultValue($hidden_value);
    }

    public function toTemplate() {}
    
    /**
     * Renders and retrieves the hidden field's html.
     *      
     * @return string
     */
    public function toHtml() {
        return $this->getFieldHtml();
    }
}