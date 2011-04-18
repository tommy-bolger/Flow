<?php
/**
* Allows the rendering of an html <ol> or <ul> list tag with list elements dynamically.
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
class ListElement
extends Element {
    /**
     * Initializes a new instance of ListElement.
     *      
     * @param string $form_name (optional) The list type. Valid values are 'ordered' and 'unordered'. Defaults to 'unordered'.
     * @param array $element_attributes (optional) The html attributes of this list element.
     * @return void
     */
    public function __construct($type = 'unordered', $element_attributes = array()) {    
        $html_tag = NULL;
    
        switch($type) {
            case 'unordered':
                $html_tag = 'ul';
                break;
            case 'ordered':
                $html_tag = 'ol';
                break;
            default:
                throw new Exception("Type '{$type}' is not a valid list type.");
                break;
        }
    
        parent::__construct($html_tag, $element_attributes);
    }
    
    /**
     * Adds a list item.
     *      
     * @param string $item_value A unique value for this list item.
     * @param string $item_name The display text of this list item.
     * @return void
     */
    public function addListItem($item_value, $item_name) {
        $this->child_elements[$item_name] = $item_value;
    }
    
    /**
     * Adds several list items.
     *      
     * @param array $items The list items to add. Format is item_name => item_value.
     * @return void
     */
    public function addListItems($items) {
        assert('is_array($items)');
    
        if(!empty($items)) {
            foreach($items as $item_name => $item_value) {
                $this->addListItem($item_value, $item_name);
            }
        }
    }
    
    /**
     * Renders and retrieves the list item's html.
     *      
     * @return string
     */
    public function toHtml() {
        $list_html = "<{$this->tag}{$this->renderAttributes()}>";
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $list_item) {
                $list_html .= "<li>{$list_item}</li>";
            }
        }
        
        $list_html .= "</{$this->tag}>";
        
        return $list_html;
    }
}