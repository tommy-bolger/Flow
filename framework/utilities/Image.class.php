<?php
/**
* Manipulates an image.
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
class Image {
    /**
    * @var mixed An open resource to the image file.
    */
    private $file_resource;

    /**
    * @var string The path to the image file.
    */
    private $file_path;

    /**
    * @var string The path to the directory of the image file.
    */
    private $base_path;
    
    /**
    * @var string The extension of the image file.
    */
    private $file_type;
    
    /**
    * @var float The width of the image file.
    */
    private $width;
    
    /**
    * @var float The height of the image file.
    */
    private $height; 

    /**
    * Instantiates a new instance of Image.
    * 
    * @param string $image_path The path oo the image to load.
    * @return void
    */
    public function __construct($image_path) {
        $this->file_path = $image_path;
    
        $this->base_path = dirname($image_path);
        
        $this->loadImage();
    }
    
    /**
    * Loads an image into memory.
    * 
    * @return void
    */
    private function loadImage() {        
        if(strpos($this->file_path, '.') !== false) {
            $file_path_split = explode('.', $this->file_path);
            
            if(is_array($file_path_split)) {
                $extension_index = count($file_path_split) - 1;
                
                if(isset($file_path_split[$extension_index])) {
                    $this->file_type = $file_path_split[$extension_index];
                }
            }
        }
        
        $image_information = getimagesize($this->file_path);
        
        $this->width = $image_information[0];
        
        $this->height = $image_information[1];
    
        switch($this->file_type) {
            case 'jpg':
            case 'jpeg':
                $this->file_resource = imagecreatefromjpeg($this->file_path);
                break;
            case 'png':
                $this->file_resource = imagecreatefrompng($this->file_path);
                break;
            case 'gif':
                $this->file_resource = imagecreatefromgif($this->file_path);
                break;
            default:
                throw new Exception("Invalid image extension: '{$this->file_type}'.");
                break;
        }
    }
    
    /**
    * Converts the loaded image file to a jpeg and saves it to a specified location.
    * 
    * @param string $file_save_path (optional) The path to save the new jpeg file to. If empty the contents of the new jpeg are returned as a string.
    * @param integer $quality (optional) The quality of the new jpeg image relative to the original. Can be values 0 for worst though 100 for best. Defaults to 75.
    * @return string The new jpeg data from memory.
    */
    public function saveAsJPEG($file_save_path = NULL, $quality = 75) {
        $image_string = '';
    
        if(!empty($file_save_path)) {
            if(is_writable($file_save_path)) {
                imagejpeg($this->file_resource, $file_save_path, $quality);
            }
            else {
                throw new Exception("File path '{$file_save_path}' cannot be written to.");
            }
        }
        else {
            ob_start();
            
            imagejpeg($this->file_resource, NULL, $quality);
            
            $image_string = ob_get_clean();
        }
        
        return $image_string;
    }
    
    /**
    * Converts the loaded image file to a png and saves it to a specified location.
    * 
    * @param string $file_save_path The path to save the new png file to.
    * @param integer $quality The quality of the new png image relative to the original. Can be values 0 for worst through 9 for best. Defaults to 9.
    * @return string The new png data from memory.
    */
    public function saveAsPNG($file_save_path = NULL, $quality = 9) {
        $image_string = '';
    
        if(!empty($file_save_path)) {
            if(is_writable($file_save_path)) {
                imagepng($this->file_resource, $file_save_path, $quality);
            }
            else {
                throw new Exception("File path '{$file_save_path}' cannot be written to.");
            }
        }
        else {
            ob_start();
            
            imagepng($this->file_resource, NULL, $quality);
            
            $image_string = ob_get_clean();
        }
        
        return $image_string;
    }
    
    /**
    * Converts the loaded image file to a gif and saves it to a specified location.
    * 
    * @param string $file_save_path (optional) The path to save the new gif file to. If not specified the contents of the new gif are returned as a string.
    * @return string The new gif data from memory.
    */
    public function saveAsGIF($file_save_path = NULL) {
        $image_string = '';
    
        if(!empty($file_save_path)) {
            if(is_writable($file_save_path)) {
                imagegif($this->file_resource, $file_save_path);
            }
            else {
                throw new Exception("File path '{$file_save_path}' cannot be written to.");
            }
        }
        else {
            ob_start();
            
            imagegif($this->file_resource);
            
            $image_string = ob_get_clean();
        }
        
        return $image_string;
    }
    
    /**
    * Resizes an image and saves the resized image to a specified file path.
    * 
    * @param float $resize_width The width of the resized image.
    * @param float $resize_height The height of the resized image.
    * @param string $save_path The directory to save the resized image to.
    * @param string $file_name The name of the resized image.    
    * @return void.
    */
    public function resize($resize_width, $resize_height, $save_path, $file_name) {
        $save_path = rtrim($save_path, '/') . '/';
    
        $resized_image = imagecreatetruecolor($resize_width, $resize_height);

        imagecopyresampled($resized_image, $this->file_resource, 0, 0, 0, 0, $resize_width, $resize_height, $this->width, $this->height);
        
        if(is_writable($save_path)) {
            $successful = false;
        
            $file_save_path = "{$save_path}{$file_name}.{$this->file_type}";

            switch($this->file_type) {
                case 'jpg':
                case 'jpeg':
                    $successful = imagejpeg($resized_image, $file_save_path, 100);
                    break;
                case 'png':
                    $successful = imagepng($resized_image, $file_save_path, 9);
                    break;
                case 'gif':
                    $successful = imagegif($resized_image, $file_save_path);
                    break;
            }
            
            if(!$successful) {
                throw new Exception("Resize of '{$this->file_path}' failed.");
            }
        }
        else {
            throw new Exception("Directory '{$save_path}' is not writable.");
        }
    }
    
    /**
    * Resizes an image scaled to the specified width and saves the resized image to a specified file path.
    * 
    * @param float $resize_width The width of the resized image.
    * @param string $save_path The directory to save the resized image to.
    * @param string $file_name The name of the resized image.    
    * @return void.
    */
    public function resizeScaleByWidth($resize_width, $save_path, $file_name) {
        $width_decimal_percentage = $resize_width / $this->width;
        $resize_height = $this->height * $width_decimal_percentage;
    
        $this->resize($resize_width, $resize_height, $save_path, $file_name);
    }
    
    /**
    * Resizes an image scaled to the specified height and saves the resized image to a specified file path.
    * 
    * @param float $resize_height The height of the resized image.
    * @param string $save_path The directory to save the resized image to.
    * @param string $file_name The name of the resized image.    
    * @return void.
    */
    public function resizeScaleByHeight($resize_height, $save_path, $file_name) {
        $height_decimal_percentage = $resize_height / $this->height;
        $resize_width = $this->width * $height_decimal_percentage;
    
        $this->resize($resize_width, $resize_height, $save_path, $file_name);
    }
}