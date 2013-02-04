<?php
/**
* Base class of a module that loads a module configuration and enforces it being enabled or disabled.
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
namespace Framework\Modules;

use \Framework\Core\Framework;
use \Framework\Utilities\Http;

class WebModule 
extends Module {    
    /**
    * @var string The module's theme.
    */
    protected $theme;
    
    /**
    * @var string The path to the assets of the current module.
    */
    protected $assets_path;
    
    /**
    * @var string The http path to the assets of the current module.
    */
    protected $assets_http_path;
    
    /**
    * @var string The path to the module's theme.
    */
    protected $theme_path;
    
    /**
    * @var string The path to the templates of the current style.
    */
    protected $templates_path;
    
    /**
    * @var string The http path to the module's css.
    */            
    protected $css_http_path;
    
    /**
    * @var string The http path to the module's javascript.
    */        
    protected $javascript_http_path;                
    
    /**
    * @var string The path to the images of the current module.
    */
    protected $images_path;
    
    /**
    * @var string The http path to the images of the current module.
    */
    protected $images_http_path;
    
    /**
    * @var string The path to the downloadable files of the current module.
    */
    protected $files_path;
    
    /**
    * @var string The http path to the downloadable files of the current module.
    */
    protected $files_http_path;

    /**
     * Initializes the current module.
     *
     * @param string $module_name The name of the module.     
     * @return void
     */
    public function __construct($module_name) {
        parent::__construct($module_name);
        
        $this->theme = $this->configuration->theme;
        
        $page_assets_path = $this->framework->installation_path;
        
        //Set the module's assets path
        $this->assets_path = "{$page_assets_path}/modules/{$module_name}/assets";
        
        $this->assets_http_path = Http::getBaseUrl() . "assets";
        
        $this->css_http_path = "{$this->assets_http_path}/css/modules/{$module_name}";
        
        $this->javascript_http_path = "{$this->assets_http_path}/javascript/modules/{$module_name}";
        
        //Set the module's style path
        $this->theme_path = "{$this->assets_path}/styles/{$this->theme}";
        
        $this->templates_path = "{$this->theme_path}/templates";
            
        $this->images_path = "{$this->assets_path}/images";
        
        $this->images_http_path = "{$this->assets_http_path}/images/modules/{$module_name}";
        
        $this->files_path = "{$this->assets_path}/files";
        
        $this->files_http_path = "{$this->assets_http_path}/files/modules/{$module_name}";
    }
    
    /**
     * Retrieves the module's assets path.
     *     
     * @return string
     */
    public function getAssetsPath() {
        return $this->assets_path;
    }
    
    /**
     * Retrieves the module's assets path.
     *     
     * @return string
     */
    public function getAssetsHttpPath() {
        return $this->assets_http_path;
    }
    
    /**
     * Retrieves the module's css http path.
     *     
     * @return string
     */
    public function getCssHttpPath() {
        return $this->css_http_path;
    }
    
    /**
     * Retrieves the module's javascript http path.
     *     
     * @return string
     */
    public function getJavascriptHttpPath() {
        return $this->javascript_http_path;
    }
    
    /**
     * Retrieves the module's theme path.
     *     
     * @return string
     */
    public function getThemePath() {
        return $this->theme_path;
    }
    
    /**
     * Retrieves the module's templates path.
     *     
     * @return string
     */
    public function getTemplatesPath() {
        return $this->templates_path;
    }
    
    /**
     * Retrieves the module's images path.
     *     
     * @return string
     */
    public function getImagesPath() {
        return $this->images_path;
    }
    
    /**
     * Retrieves the module's images http path.
     *     
     * @return string
     */
    public function getImagesHttpPath() {
        return $this->images_http_path;
    }
    
    /**
     * Retrieves the module's files path.
     *     
     * @return string
     */
    public function getFilesPath() {
        return $this->files_path;
    }
    
    /**
     * Retrieves the module's files http path.
     *     
     * @return string
     */
    public function getFilesHttpPath() {
        return $this->files_http_path;
    }
}