<?php
/**
* The base class for record models.
* Copyright (c) 2016, Tommy Bolger
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
namespace Framework\Core;

use \Exception;

class RecordModel {
    public static function getFullClassName() {
        return get_called_class();
    }

    public function __set($variable_name, $variable_value) {
        if(strlen($variable_value) > 0) {
            $property_value = $this->getPropertyValue($variable_name, $variable_value);
            
            $this->$variable_name = $property_value;
        }
    }
    
    public function __get($property_name) {
        return $this->$property_name;
    }
    
    public function isset($property_name) {
        return isset($this->$property_name);
    }
    
    protected function getPropertyValue($property_name, $property_value) {
        return $property_value;
    }

    public function setPropertiesFromArray(array $property_values, $error_on_invalid_property = false) {
        if(!empty($property_values)) {
            foreach($property_values as $property_name => $property_value) {
                if(property_exists($this, $property_name)) {
                    $this->__set($property_name, $property_value);
                }
                else {
                    if($error_on_invalid_property) {
                        throw new Exception("'{$property_name}' is not a valid property of this object.");
                    }
                }
            }
        }
    }
    
    public function setPropertiesFromIndexedArray(array $indexed_property_values) {
        if(!empty($indexed_property_values)) {
            $properties = static::getPropertyNames();
            
            $property_count = count($properties);
            $indexed_property_value_count = count($indexed_property_values);
            
            if($property_count != $indexed_property_value_count) {
                throw new Exception("{$property_count} properties exist in this object. {$indexed_property_value_count} indexed property values were given. The number of indexed values must match the number of properties.");
            }
            
            foreach($properties as $property_index => $property_name) {
                $this->__set($property_name, $indexed_property_values[$property_index]);
            }
        }
    }
    
    public function setPropertiesFromObject($property_values, $error_on_invalid_property = false) {
        assert('is_object($property_values)');
        
        $property_values_array = get_object_vars($property_values);
        
        $this->setPropertiesFromArray($property_values_array, $error_on_invalid_property);
    }
    
    public function getPropertyNames($include_null_properties = true) {        
        $property_names = array_keys(get_object_vars($this));
        
        if(empty($include_null_properties)) {
            foreach($property_names as $index => $property_name) {
                if(is_null($this->$property_name)) {
                    unset($property_names[$index]);
                }
            }
        }
        
        return $property_names;
    }
    
    public function toArray($include_null_properties = true) {    
        $properties = get_object_vars($this);
        
        if(empty($include_null_properties)) {
            foreach($properties as $property_name => $property_value) {
                if(is_null($property_value)) {
                    unset($properties[$property_name]);
                }
            }
        }
        
        return $properties;
    }
}