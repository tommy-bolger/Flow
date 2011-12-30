<?php
/**
* Allows the rendering of a form image button and performing validation on its submitted data dynamically.
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
        $path_to_image = rtrim($path_to_image, '/');
    
        $path_to_image = page()->getThemeDirectoryPath() . "images/{$path_to_image}";
            
        if(!is_readable($path_to_image)) {
            throw new Exception("Image '{$path_to_image}' does not exist or is not accessible.");
        }
        
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