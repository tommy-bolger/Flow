<?php
/**
* Allows the rendering of a form image button and performing validation on its submitted data dynamically.
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

use \Framework\Html\Form\FieldObjects\Button;

class ImageButton
extends Button {
    /**
     * Instantiates a new instance of ImageButton.
     *      
     * @param string $image_button_name The image button name.
     * @param string $path_to_image (optional) The image button file location relative to the document root.
     * @param string $image_button_alt (optional) The image button alt text.
     * @param array $css_classes (optional) A list of classes.
     * @return void
     */
    public function __construct($image_button_name, $path_to_image = "", $image_button_alt = "", $css_classes = array()) {
        parent::__construct('image', $image_button_name, 'button', $css_classes);
        
        $this->setImageSource($path_to_image);
        
        $this->setImageAlt($image_button_alt);
    }
    
    /**
     * Sets the source image of an image button.
     *      
     * @param string $path_to_image The file path to the image relative to the document root.
     * @return void
     */
    public function setImageSource($path_to_image) {        
        $this->setAttribute('src', $path_to_image);
    }
    
    /**
     * Sets the alt text of the image.
     *      
     * @param string $alt_text
     * @return void
     */
    public function setImageAlt($image_alt_text = "") {
        $alt_text = $image_alt_text;
        
        if(empty($alt_text)) {
            $alt_text = ucfirst(str_replace(array('_', '-'), ' ', $this->name));
        }
        
        $this->setAttribute('alt', $alt_text);
    }
}