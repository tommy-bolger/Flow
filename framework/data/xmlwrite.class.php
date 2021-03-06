<?php    
/**
* Constructs an XML file and allows transformation via XSL.
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
namespace Framework\Data;

use \SimpleXMLElement;
use \stdClass;

class XMLWrite {
    /**
    * @var object The XMLWriter object.
    */
    protected $xml_data;
    
    public static function convertXmlToObject(SimpleXMLElement $xml_object, $namespace = '') {
        $object_structure = new stdClass();
        
        //Add any XML attributes as properties of the current object structure
        $xml_attributes = $xml_object->attributes();
    
        if(!empty($xml_attributes)) {
            foreach($xml_attributes as $attribute_name => $attribute_value) {
                $object_structure->$attribute_name = (string)$attribute_value;
            }
        }
        
        $children = NULL;
        
        if(!empty($namespace)) {
            $children = $xml_object->children($namespace, true);
        }
        else {
            $children = $xml_object->children();
        }
    
        if(!empty($children)) {
            //If there are any children of this xml element then add those as properties of the current object structure
            foreach($children as $child_name => $child) {
                //Convert this child element into an object structure via recursive call to this function
                $child_object = self::convertXmlToObject($child, $namespace);
                
                /*
                    If the processed child only has 1 property and that property is 'value' 
                    (a value set for scalar vaules by this function) then set the value of this
                    child as that directly.
                */ 
                if(count($child_object) <= 1 && isset($child_object->value)) {
                    $child_object = $child_object->value;
                }
                
                /*
                    Convert the child object into an array. If the array version of the object has no
                    elements, indicating an empty object, then set the child object to NULL.
                */
                $child_object_array = (array)$child_object;
                
                if(count($child_object_array) == 0) {
                    $child_object = NULL;
                }
    
                if(!isset($object_structure->$child_name)) {
                    $object_structure->$child_name = $child_object;
                }
                /*
                    If the child name already exists then the property is an array. Convert the
                    child property into an array and append additional child elements of this name
                    as new array elements.
                */
                else {
                    if(!is_array($object_structure->$child_name)) {
                        $current_child = $object_structure->$child_name;
                        
                        $object_structure->$child_name = array(
                            $current_child
                        );
                    }
                    
                    $object_structure->{$child_name}[] = $child_object;
                }
            }
        }
        //If the current object structure has no children but has a value then set that as a property
        else {
            $value = (string)$xml_object;
        
            if(strlen($value) > 0) {
                $object_structure->value = $value;
            }
        }
        
        return $object_structure;
    }
    
    /**
     * Instantiates a new instance of XmlWrite.
     *
     * @param string $xml_version (optional) The XML version of the document. Defaults to '1.0'.
     * @return void
     */
    public function __construct($xml_version = "1.0") {    
        $this->xml_data = new XMLWriter();
        
        $this->xml_data->openMemory();
        
        $this->xml_data->startDocument($xml_version);
        
        $this->xml_data->setIndent(true);
    }
    
    /**
     * Provides access to all XMLWriter functions through the xml_data object.
     *
     * @param string $function_name The name of the function being called.
     * @param mixed $function_arguments The arguments for the called function.          
     * @return mixed Any results that the called XMLWriter function might return.
     */
    public function __call($function_name, $function_arguments) {
        return call_user_func_array(array($this->xml_data, $function_name), $function_arguments);
    }
    
    /**
     * Adds an XML element with optional values and attributes and automatically adds a closing tag for the element.
     *
     * @param string $element_name The name of the xml element.
     * @param string $element_value (optional) The element's value. This is optional.
     * @param array $element_attributes (optional) The element's attributes. This is optional.
     * @return void
     */
    public function addElement($element_name, $element_value = NULL, array $element_attributes = array()) {    
        $this->xml_data->startElement($element_name);
        
        if(!empty($element_attributes)) {
            $this->addElementAttributes($element_attributes);
        }
        
        $this->xml_data->text($element_value);
        
        $this->xml_data->endElement();
    }
    
    /**
     * Adds element attributes to the current element.
     *
     * @param array $element_attributes The element attributes to add to the current element.
     * @return void
     */
    public function addElementAttributes(array $element_attributes) {            
        if(!empty($element_attributes)) {
            foreach($element_attributes as $element_attribute_name => $element_attribute_value) {
                $this->xml_data->startAttribute($element_attribute_name);
                
                $this->xml_data->text($element_attribute_value);
                
                $this->xml_data->endAttribute();
            }
        }
    }
    
    /**
     * Transforms the XML from a specified XSL document.
     *
     * @param string $xslt_path The file path to the xsl document.
     * @return string The transformed XML.
     */
    public function transform($xsl_path) {        
        if(!is_readable($xsl_path)) {
            throw new \Exception("XSLT path '{$xsl_path}' is not readable.");
        }

        $xml_document = new DomDocument();
        $xml_document->loadXML($this->xml_data->outputMemory());
        
        $xslt_document = new DomDocument();
        $xslt_document->load($xslt_path);
        
        $xslt_processor = new XSLTprocessor();
        $xslt_processor->importStyleSheet($xslt_document);
    
        return $xslt_processor->transformToXML($xml_document);
    }
}