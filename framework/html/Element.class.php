<?php
/**
* Enables the rendering of an html element with a closing tag and its child elements dynamically.
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
class Element {
    /**
    * @var object The template for the element.
    */
    private $template;

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
	}
	
	/**
	 * Calls a new instance of the html element specified and adds the new element as a child of the current element.
	 *
	 * @param string $function_name The name of the function in the following format: add<element_name>, example: addText
	 * @param array $arguments The arguments that are to be passed to the constructor of the new html element. 	 
	 * @return object The new instance of the child html element.
	 */
	public function __call($function_name, $arguments) {
		assert('strpos($function_name, "add") !== FALSE');
		
		$class_name = ltrim($function_name, "add");
		
		$reflection_object = new ReflectionClass($class_name); 

		$child_object = $reflection_object->newInstanceArgs($arguments);

		$this->addChild($child_object);
		
		return $child_object;
	}
	
	/**
	 * Adds an html element object as a child of the current element.
	 *
	 * @param object $child_object The html element object that will be added as a child.
	 * @param string $child_name (optional) The name of the child.	 
	 * @return void
	 */
	public function addChild($child_object, $child_name = NULL) {
        assert('is_object($child_object)');
        
		if(empty($child_name)) {
			$child_name = $child_object->getId();
		}

		if(empty($child_name)) {			
			$this->child_elements[] = $child_object;
		}
		else {
			assert('!isset($this->child_elements[$child_name])');
		
			$this->child_elements[$child_name] = $child_object;
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
	 * Adds an attribute to the element.
	 *
	 * @param string $attribute_name The name of the attribute.
	 * @param mixed $attribute_value (optional) The value of the attribute.	 
	 * @return void
	 */
	public function setAttribute($attribute_name, $attribute_value = NULL) {		        
        if(!empty($attribute_value)) {
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
	
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $child_element) {
                $child_template_tag_name = $child_element->getId();
	
                if(!empty($child_template_tag_name)) {
                    $child_template_tag_name = '{{' . strtoupper($child_template_tag_name) . '}}';
                
                    $template_array[$child_template_tag_name] = $child_element->toHtml();
                }
                else {
                    throw new Exception("This element does not have an ID and cannot be used as a template element.");
                }
            }
        }
        
        return $template_array;
	}
	
	/**
	 * Renders the current element and all of its child elements as html.
	 *
	 * @return string The rendered element.
	 */
	public function toHtml() {
        $element_html = $this->generateOpenTag();
	
        if(isset($this->template)) {
            $element_html .= $this->template->parseTemplate($this->toTemplateArray());
        }
        else {            
            if(!empty($this->text)) {
    			$element_html .= "{$this->text}";
    		}
    		
    		if(!empty($this->child_elements)) {
    			foreach($this->child_elements as $child_element) {
    				$element_html .= "{$child_element->toHtml()}";
    			}
    		}
        }
        
        $element_html .= "</{$this->tag}>";
            
        return $element_html;
	}
}