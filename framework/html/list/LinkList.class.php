<?php
/**
* Allows the rendering of an unordered html list with hyperlinks as elements dynamically.
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
class LinkList
extends UnorderedList {
    /**
     * Initializes a new instance of LinkList.
     *      
     * @param array $links (optional) The links to add as list items.
     * @param array $element_attributes (optional) The html attributes of the link list element.
     * @return void
     */
    public function __construct($links = array(), $element_attributes = array()) {
        parent::__construct($links, $element_attributes);
    }
    
    /**
     * Adds a list item.
     *      
     * @param string $item_value The url path relative to the current site base url.
     * @param string $item_name The display text of this link.
     * @return void
     */
    public function addListItem($item_value, $item_name) {
        $link_item = "<a href=\"" . Http::getBaseUrl() . "{$item_value}\">{$item_name}</a>";
        
        $this->child_elements[] = $link_item;
    }
}