<?php
/**
* Allows the rendering of an unordered html list with hyperlinks as elements dynamically.
* Copyright (c) 2011, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Framework\Html\Lists;

class LinkList
extends UnorderedList {
    protected $active_item_name;

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
     * @param string $item_value The name of the page.
     * @param string $item_name The display text of this link.
     * @param array $attributes (optional) The attributes of this list item. Format is attribute_name => attribute_value.
     * @return void
     */
    public function addListItem($item_value, $item_name, array $attributes = array()) {    
        $link_item = "<a href=\"{$item_value}\">{$item_name}</a>";
        
        parent::addListItem($link_item, $item_name, $attributes);
    }
    
    /**
     * Sets the link that will be marked with the active class.
     *      
     * @param string $active_item_name The name of the item that will be marked as active.
     * @return void
     */
    public function setActiveItem($active_item_name) {
        $this->active_item_name = $active_item_name;
    }
    
    /**
     * Renders and retrieves an individual item's html.
     *      
     * @return string
     */
    protected function getItemHtml(array $item, $item_name) {                
        if($item_name == $this->active_item_name) {            
            $item['attributes']['class'][] = 'active';
        }
    
        return parent::getItemHtml($item, $item_name);
    }
}