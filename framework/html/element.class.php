<?php
/**
* Enables the rendering of an html element with a closing tag and its child elements dynamically.
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

namespace Framework\Html;

use \Framework\Display\Template;

class Element {
    /**
    * @var object The template for the element.
    */
    protected $template;
    
    /**
    * @var object The template for contents of the element.
    */
    protected $contents_template;
    
    /**
    * @var array The list of css files to include with this element.
    */
    protected $css_files = array();
    
    /**
    * @var array The list of javascript files to include with this element.
    */
    protected $javascript_files = array();
    
    /**
    * @var array The list of inline javascript to include with this element.
    */
    protected $inline_javascript = array();
    
    /**
    * @var boolean Indicates if javascript is enabled for this element and its children.
    */
    protected $javascript_enabled = true;

    /**
    * @var string The html tag name of the element.
    */
    protected $tag;
    
    /**
    * @var array The attributes of the element.
    */
    protected $attributes = array();

    /**
    * @var array The list of child element objects of the element.
    */
    protected $child_elements = array();

    /**
    * @var string The display text contents of the element.
    */
    protected $text;
    
    /**
     * Initializes a new instance of Element.
     *
     * @param $tag string The element tag name.
     * @param array $attributes (optional) A list of attributes for the element (such as id, class, etc.).
     * @param string $text (optional) The string value of the element that will be displayed.          
     * @return void
     */
    public function __construct($tag, $attributes = array(), $text = NULL) {        
        $this->tag = $tag;

        $this->setAttributes($attributes);

        $this->text = $text;
        
        $this->addElementFiles();
    }
    
    /**
     * Adds an html element object as a child of the current element.
     *
     * @param object $child_object The html element object that will be added as a child.
     * @param string $child_name (optional) The name of the child.
     * @param boolean $append A flag determining if the added child should be appended to a group with the same name.     
     * @return void
     */
    public function addChild($child_object, $child_name = NULL, $append = false) {
        assert('empty($child_object) || is_object($child_object) || is_scalar($child_object) || is_array($child_object)');
        
        if(empty($child_name) && is_object($child_object)) {
            $child_name = $child_object->getId();
        }
        
        if(!empty($child_name) && !$append) {
            assert('!isset($this->child_elements[$child_name])');
        }

        if(empty($child_name)) {
            $this->child_elements[] = $child_object;
        }
        else {
            if(!$append) {
                assert('!isset($this->child_elements[$child_name])');
            
                $this->child_elements[$child_name] = $child_object;
            }
            else {
                $this->child_elements[$child_name][] = $child_object;
            }
        }
    }
    
    /**
     * Gets a specified child html element from the current element.
     *
     * @param string $child_name The name of the child to be retrieved.
     * @return object
     */
    public function getChild($child_name) {
        assert('isset($this->child_elements[$child_name])');    
        
        return $this->child_elements[$child_name];
    }
    
    /**
     * Removes the specified child element from the current element.
     *
     * @param string $child_name The name of the child to be removed.
     * @return void
     */
    public function removeChild($child_name) {
        assert('isset($this->child_elements[$child_name])');
        
        unset($this->child_elements[$child_name]);
    }
    
    /**
     * Sets the template file of the element.
     *
     * @param string $template_file_path The file path to the template file.
     * @return void
     */
    public function setTemplate($template_file_path) {    
        $this->template = new Template($template_file_path);
    }
    
    /**
     * Sets the template file of contents of the element.
     *
     * @param string $template_file_path The file path to the template file.
     * @return void
     */
    public function setContentsTemplate($template_file_path) {    
        $this->contents_template = new Template($template_file_path);
    }
    
    /**
     * Adds an attribute to the element.
     *
     * @param string $attribute_name The name of the attribute.
     * @param mixed $attribute_value (optional) The value of the attribute.     
     * @return void
     */
    public function setAttribute($attribute_name, $attribute_value = NULL) {                
        if(!empty($attribute_value) || $attribute_value == '0') {
            $this->attributes[$attribute_name] = $attribute_value;
        }
        else {
            $this->attributes[$attribute_name] = "";
        }
    }
    
    /**
     * Adds several attributes to an element.
     *
     * @param array $attributes The attributes to add to the element. Format is attribute_name => attribute_value.
     * @return void
     */
    public function setAttributes($attributes) {
        assert('is_array($attributes)');

        if(!empty($attributes)) {
            foreach($attributes as $attribute_name => $attribute_value) {
                $this->setAttribute($attribute_name, $attribute_value);
            }
        }
    }
    
    /**
     * Retrieves an attribute of an element by name.
     *
     * @param string $attribute_name The name of the attribute to retrieve.
     * @return mixed Returns the attribute value if the attribute exists or false if it does not.
     */
    public function getAttribute($attribute_name) {
        if(isset($this->attributes[$attribute_name])) {
            return $this->attributes[$attribute_name];
        }
        else {
            return false;
        }
    }
    
    /**
     * Removes the specified attribute from the current element.
     *
     * @param string $attribute_name The name of the attribute to remove.
     * @return void
     */
    public function removeAttribute($attribute_name) {    
        if(isset($this->attributes[$attribute_name])) {        
            unset($this->attributes[$attribute_name]);
        }
    }
    
    /**
     * Sets the element's id attribute.
     *
     * @param string $element_id The id to add to the element.     
     * @return void
     */
    public function setId($element_id) {    
        $this->setAttribute('id', $element_id);
    }
    
    /**
     * Retrieves the element's id attribute.
     *
     * @return string Returns the element's id if it has one or an empty string if it does not.
     */
    public function getId() {
        if(isset($this->attributes['id'])) {
            return $this->attributes['id'];
        }
        else {
            return "";
        }
    }
    
    /**
     * Sets the current element's css class attribute.
     *
     * @param string $element_class The css class to add to the element.     
     * @return void
     */
    public function addClass($element_class) {
        if(!isset($this->attributes['class'])) {
            $this->attributes['class'] = array();
        }
        
        $this->attributes['class'][] = $element_class;
    }
    
    /**
     * Sets several classes for the element.
     *
     * @param array $element_classes The css classes to add to the element.     
     * @return void
     */
    public function addClasses($element_classes) {
        assert('is_array($element_classes)');
    
        if(!empty($element_classes)) {
            foreach($element_classes as $element_class) {
                $this->addClass($element_class);
            }
        }
    }
    
    /**
     * Sets the text value of the element.
     *
     * @param string $text The text to add to the element.     
     * @return void
     */
    public function setText($text) {
        $this->text = $text;
    }
    
    /**
     * Retrieves the element's html tag name.
     *
     * @return string.
     */
    public function getElementTag() {
        return $this->tag;
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {}
    
    /**
     * Retrieves the element inline code and files to be included with this element.
     *
     * @return array
     */
    public function getElementFiles() {
        $css_files = $this->css_files;
        
        $javascript_files = array();
        
        if($this->javascript_enabled) {
            $javascript_files = $this->javascript_files;
        }
        
        $inline_javascript = $this->inline_javascript;

        if(!empty($this->child_elements)) {
            $child_element_files = $this->getChildElementFiles($this->child_elements);
            
            $css_files += $child_element_files['css'];
                
            if($this->javascript_enabled) {
                $javascript_files += $child_element_files['javascript'];
            }
            
            $inline_javascript += $child_element_files['inline_javascript'];
        }
        
        return array(
            'css' => $css_files,
            'javascript' => $javascript_files,
            'inline_javascript' => $inline_javascript
        );
    }
    
    /**
     * Retrieves all child element files.
     *
     * @param array $child_elements The list of child element objects to retrieve from.
     * @return array
     */
    protected function getChildElementFiles($child_elements) {
        assert('is_array($child_elements)');
    
        $css_files = array();
        $javascript_files = array();
        $inline_javascript = array();
    
        foreach($child_elements as $child_element) {
            if(is_object($child_element)) {
                $child_element_files = $child_element->getElementFiles();
                
                $css_files += $child_element_files['css'];
                $javascript_files += $child_element_files['javascript'];
                $inline_javascript += $child_element_files['inline_javascript'];
            }
            elseif(is_array($child_element) && !empty($child_element)) {
                $sub_child_element_files = $this->getChildElementFiles($child_element);
                
                $css_files += $sub_child_element_files['css'];
                $javascript_files += $sub_child_element_files['javascript'];
                $inline_javascript += $sub_child_element_files['inline_javascript'];
            }
        }
        
        return array(
            'css' => $css_files,
            'javascript' => $javascript_files,
            'inline_javascript' => $inline_javascript
        );
    }
    
    /**
     * Adds a css file to be included with this element.
     *
     * @param string $css_file_path The file path to the css file.
     * @param boolean (optional) $internal A flag indicating if the css file path is an internal relative path. Defaults to true.
     * @return void
     */
    protected function addCssFile($css_file_path, $internal = true) {
        $this->css_files[$css_file_path] = $internal;                
    }
    
    /**
     * Adds a javascript file to be included with this element.
     *
     * @param string $javascript_file_path The file path to the javascript file.
     * @param boolean (optional) $internal A flag indicating if the javascript file path is an internal relative path. Defaults to true.
     * @return void
     */
    protected function addJavascriptFile($javascript_file_path, $internal = true) {
        $this->javascript_files[$javascript_file_path] = $internal;
    }
    
    /**
     * Adds inline javascript to be included with this element.
     *
     * @param string $inline_javascript The inline javascript.
     * @param string (optional) $inline_name The name of the inline block for overriding purposes. Defaults to an empty string.
     * @return void
     */
    protected function addInlineJavascript($inline_javascript, $inline_name = '') {
        if(empty($inline_name)) {    
            $this->inline_javascript[] = $inline_javascript;
        }
        else {
            $this->inline_javascript[$inline_name] = $inline_javascript;        
        }                        
    }
    
    /**
     * Disables javascript for this element.
     *      
     * @return void
     */
    public function disableJavascript() {
        $this->javascript_enabled = false;
        
        $this->addClass('no_js');
    }
    
    /**
     * Renders the element's attributes as html.
     *
     * @return string The rendered attributes.
     */
    protected function renderAttributes() {
        $rendered_attributes = "";
    
        if(!empty($this->attributes)) {
            foreach($this->attributes as $attribute_name => $attribute_value) {
                $rendered_attributes .= " {$attribute_name}";
            
                if(!is_null($attribute_value)) {
                    if(is_array($attribute_value)) {
                        $attribute_value = implode(' ', $attribute_value);
                    }
                      
                    $rendered_attributes .= '="' . $attribute_value . '"';
                }
            }
        }
        
        return $rendered_attributes;
    }
    
    /**
     * Generates the opening tags of the element's html tag with attributes.
     *
     * @return string The element's opening html tag.
     */
    protected function generateOpenTag() {
        return "<{$this->tag}{$this->renderAttributes()}>";
    }
    
    /**
     * Returns the html of the element and its child elements as an array indexed by the child element ids for use with parsing the element's template.
     *
     * @return array
     */
    public function toTemplateArray() {    
        $template_array = array();

        if(isset($this->atributes['id'])) {
            $name = $this->attributes['id'];
        
            $open_tag_template_name = "{$name}_open";
            $close_tag_template_name = "{$name}_close";
            
            $template_array[$open_tag_template_name] = $this->generateOpenTag();
            $template_array[$close_tag_template_name] = "</{$this->tag}>";
        }

        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $child_name => $child_element) {
                $child_html = '';
            
                if(is_scalar($child_element) || empty($child_element)) {                    
                    $child_html = $child_element;
                }
                elseif(is_object($child_element)) {
                    if(!is_string($child_name)) {
                        $child_name = $child_element->getId();
                    }
                    
                    $child_html = $child_element->toHtml();
                }
                elseif(is_array($child_element)) {
                    $first_element = current($child_element);
                
                    if(is_object($first_element)) {
                        foreach($child_element as $sub_element) {
                            $child_html .= $sub_element->toHtml();
                        }
                    }
                    else {
                        $child_html = $child_element;
                    }
                }
                else {
                    throw new \Exception("Child element is not a supported data type.");
                }
                
                if(!empty($child_name)) {                
                    $template_array[$child_name] = $child_html;
                }
                else {
                    throw new \Exception("This element does not have an ID and cannot be used as a template element.");
                }
            }
        }
        
        return $template_array;
    }
    
    /**
     * Returns the contents of the element.
     *
     * @return array
     */
    protected function renderElementContents() {
        $element_contents_html = '';
    
        if(!empty($this->text)) {
            $element_contents_html .= $this->text;
        }
        
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $child_element) {
                if(is_object($child_element)) {
                    $element_contents_html .= $child_element->toHtml();
                }
                else {
                    $element_contents_html .= $child_element;
                }
            }
        }
        
        return $element_contents_html;
    }
    
    /**
     * Renders the current element and all of its child elements as html.
     *
     * @return string The rendered element.
     */
    public function toHtml() {
        $element_html = '';
        
        if(isset($this->template) && $this->template->exists()) {
            $this->template->setPlaceholderValues($this->toTemplateArray());
        
            $element_html .= $this->template->parseTemplate();
        }
        else {
            $element_html .= $this->generateOpenTag();
            
            if(isset($this->contents_template) && $this->contents_template->exists()) {
                $this->contents_template->setPlaceholderValues($this->toTemplateArray());
            
                $element_html .= $this->contents_template->parseTemplate();
            }
            else {
                $element_html .= $this->renderElementContents();
            }
            
            $element_html .= "</{$this->tag}>";
        }
            
        return $element_html;
    }
}