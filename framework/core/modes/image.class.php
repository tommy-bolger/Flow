<?php
/**
* The conductor class for the image mode of the framework to handle image requests.
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
namespace Framework\Core\Modes;

use \Framework\Utilities\Image as ImageUtility;

require_once(__DIR__ . '/file.class.php');

class Image
extends File {
    /**
    * @var object The framework error handler class name.
    */
    protected $error_handler_class = '\\Framework\\Debug\\ImageError';
    
    /**
    * @var string The name of the assets folder to use in the full path.
    */
    protected $assets_folder = 'images';
    
    /**
    * @var integer The width to resize the image to.
    */
    protected $width;
    
    /**
    * @var integer The height to resize the image to.
    */
    protected $height;
    
    /**
     * Initializes an instance of the framework in image mode and processes an image request.
     *
     * @return void
     */
    public function __construct() {    
        parent::__construct('image');
        
        $this->width = request()->get->getVariable('width', 'integer');
        
        $this->height = request()->get->getVariable('height', 'integer');
        
        if(!empty($this->width) && !empty($this->height)) {
            $this->resizeImage();
        }
    }
    
    /**
     * Indicates if the requested file has a valid extension.
     *
     * @return void
     */
    protected function validateExtension() {
        if(ImageUtility::isValidExtension($this->extension)) {
            //If the image has an extension of jpg convert it to jpeg for the header
            $image_type = $this->extension;
            
            if($image_type == 'jpg') {
                $image_type = 'jpeg';
            }
        
            header("Content-type: image/{$image_type}");
            header("Content-Disposition: filename={$this->full_name}");
        }
        else {
            throw new \Exception("Image '{$this->full_name}' does not have a supported file extension.");
        }
    }
    
    /**
     * Indicates to the client that the requested file could not be found.
     *
     * @return void
     */
    public function initializeNotFound() {
        parent::initializeNotFound();
        
        header("Content-type: image/png");
    
        $this->full_path = self::$installation_path . "/public/assets/images/not_found.png";
    }
    
    /**
     * Resizes the requested image to the specified dimensions.
     *
     * @return void
     */
    private function resizeImage() {
        //If a width and height have been specified then resize a copy of the image to those dimensions and save the copy to the cache folder.
        $image_cache_path = self::$installation_path . "/cache/images";
        
        $image_cache_name = "{$this->module_name}_{$this->theme_name}_{$this->name}_{$this->width}_{$this->height}";
    
        $image_cache_file_path = "{$image_cache_path}/{$image_cache_name}.{$this->extension}";
        
        //If the resized image does not exist perform the resize.
        if(!is_file($image_cache_file_path)) {
            $image = new ImageUtility($this->full_path);
        
            $image->resize($this->width, $this->height, $image_cache_path, $image_cache_name);
        }
        
        $this->full_path = $image_cache_file_path;
    }
}