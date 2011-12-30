<?php
/**
* Allows the rendering of a form float field as a text input and performing validation on its submitted data dynamically.
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

namespace Framework\Html\Form\Fields;

class FloatField
extends Textbox {
    /**
    * @var integer The number of digits on the left side of the decimal.
    */
    private $left_precision;
    
    /**
    * @var integer The number of digits on the right side of the decimal.
    */
    private $right_precision;

    /**
     * Instantiates a new instance of a float field.
     *      
     * @param string $float_name The float field name.
     * @param string $float_label (optional) The float field label.
     * @param string $float_value (optional) The float field value.
     * @return void
     */
    public function __construct($float_name, $float_label = "", $float_value = NULL) {
        parent::__construct($float_name, $float_label, $float_value, array('float_field'));
    }
    
    public function setMaxLength($max_length) {}
    
    /**
     * Sets the precision of the float field.
     *      
     * @param integer $left_precision The number of digits allowed to the left of the decimal place.
     * @param integer $right_precision The number of digits allowed to the right of the decimal place.
     * @return void
     */
    public function setPrecision($left_precision, $right_precision) {
        if(filter_var($left_precision, FILTER_VALIDATE_INT) === false) {
            throw new \Exception('Left precision can only be an integer value.');
        }
        
        if(filter_var($right_precision, FILTER_VALIDATE_INT) === false) {
            throw new \Exception('right precision can only be an integer value.');
        }
    
        $this->left_precision = $left_precision;
        
        $this->right_precision = $right_precision;
        
        parent::setMaxLength($left_precision + $right_precision + 1);
    }
    
    /**
     * Validates the float field's submitted value.
     *      
     * @return boolean
     */
    public function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            if(filter_var($this->value, FILTER_VALIDATE_FLOAT) === false) {
                $this->setErrorMessage("{$this->label} is not a valid decimal value.");
            
                return false;
            }
            
            $value_split = explode('.', $this->value);
            
            if(isset($this->left_precision) && isset($value_split[0]) && strlen($value_split[0]) > $this->left_precision) {
                $this->setErrorMessage("{$this->label} can only have {$this->left_precision} digit(s) before the decimal place.");
                
                return false;
            }
            
            if(isset($this->right_precision) && isset($value_split[1]) && strlen($value_split[1]) > $this->right_precision) {
                $this->setErrorMessage("{$this->label} can only have {$this->right_precision} digit(s) after the decimal place.");
                
                return false;
            }
        }
        
        return true;
    }
}