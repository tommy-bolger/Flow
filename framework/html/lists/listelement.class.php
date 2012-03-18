<?php
/**
* Allows the rendering of an html <ol> or <ul> list tag with list elements dynamically.
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

class ListElement
extends \Framework\Html\Element {
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
                throw new \Exception("Type '{$type}' is not a valid list type.");
                break;
        }
    
        parent::__construct($html_tag, $element_attributes);
    }
    
    /**
     * Adds a list item.
     *      
     * @param string $item_value A unique value for this list item.
     * @param string $item_name The display text of this list item.
     * @param array $attributes (optional) The attributes of this list item. Format is attribute_name => attribute_value.     
     * @return void
     */
    public function addListItem($item_value, $item_name, $attributes = array()) {
        assert('is_array($attributes)');
    
        $this->child_elements[$item_name] = array(
            'value' => $item_value,
            'attributes' => $attributes
        );
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
     * Renders and retrieves an individual item's html.
     * 
     * @param array $item The content and attributes of the item.
     * @param string $item_name The name of the item.     
     * @return string
     */
    protected function getItemHtml($item, $item_name) {
        assert('is_array($item)');
        
        $attributes_formatted = '';
        
        if(!empty($item['attributes'])) {
            $attributes = $item['attributes'];
            
            foreach($attributes as $attribute_name => $attribute_value) {            
                if(is_array($attribute_value)) {
                    $attribute_value = implode(' ', $attribute_value);
                }
            
                $attributes_formatted .= " {$attribute_name}=\"{$attribute_value}\"";
            }
        }
    
        return "<li{$attributes_formatted}>{$item['value']}</li>";
    }
    
    /**
     * Renders and retrieves the list item's html.
     *      
     * @return string
     */
    public function toHtml() {
        $list_html = "<{$this->tag}{$this->renderAttributes()}>";
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $item_name => $item) {
                $list_html .= $this->getItemHtml($item, $item_name);
            }
        }
        
        $list_html .= "</{$this->tag}>";
        
        return $list_html;
    }
}