<?php
/**
* Allows the rendering of an html <ul> list tag with list elements dynamically.
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
class UnorderedList
extends ListElement {
    /**
     * Initializes a new instance of UnorderedList.
     *      
     * @param array $list_items (optional) The items to add to this unordered list.
     * @param array $element_attributes (optional) The html attributes of the unordered list element.
     * @return void
     */
    public function __construct($list_items = array(), $element_attributes = array()) {
        parent::__construct('unordered', $element_attributes);
        
        $this->addListItems($list_items);
    }
}