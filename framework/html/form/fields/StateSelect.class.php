<?php
/**
* Allows the rendering of a form dropdown field automatically populated with U.S. states and performing validation on its submitted data dynamically.
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
class StateSelect
extends SelectField {
    /**
     * Initializes a new instance of StateSelect.
     *      
     * @param string $select_name The state select name.
     * @param string $select_label (optional) The state select label
     * @param array $css_classes (optional) A list of css classes for this field.   
     * @return void
     */
    public function __construct($select_name, $select_label = "", $css_classes = array()) {
        $options = db()->getMappedColumn("
            SELECT state_id, abbreviation || ' - ' || \"name\" AS state_name
            FROM us_states
            ORDER BY abbreviation
        ");
    
        parent::__construct($select_name, $select_label, $options, $css_classes);
    }
}