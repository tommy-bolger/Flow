<?php
/**
* Allows the rendering of an html page and its child elements dynamically.
* Copyright (c) 2017, Tommy Bolger
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

use \Exception;
use \Framework\Core\Framework;
use \Framework\Minification\Html;
use \Framework\Minification\Css;
use \Framework\Minification\Javascript;
use \Framework\Utilities\Http;
use \Framework\Display\Template;
use \Framework\Html\Body;

class Page {    
    /**
    * @var object The instance of the framework.
    */
    protected $framework;
    
    /**
    * @var object The template for the page.
    */
    protected $template;
    
    /**
    * @var array A list of specified meta tags for the page.
    */
    protected $meta_tags = array();

    /**
    * @var string The file path to the page style including templates, images, and css.
    */
    protected $theme_directory_path;
    
    /**
    * @var array The file path list to the css files.
    */
    protected $css_base_paths = array();
    
    /**
    * @var array The file path list to the javascript files.
    */
    protected $javascript_base_paths = array();
    
    /**
    * @var array A list of all inline css to be output on the page.
    */
    protected $inline_css = array();
    
    /**
    * @var array A list of the css files included by the page that are located on the same server as the current page.
    */
    protected $internal_css_files = array();
    
    /**
    * @var array A list of the css files included by the page that are located on an external server.
    */
    protected $external_css_files = array();
    
    /**
    * @var array A list of all inline javascript to be output on the page.
    */
    protected $inline_javascript = array();
    
    /**
    * @var array A list of the javascript files included by the page that are located on the same server as the current page.
    */
    protected $internal_javascript_files = array();
    
    /**
    * @var array A list of the javascript files included by the page that are located on an external server.
    */
    protected $external_javascript_files = array();
    
    /**
    * @var string The file path to the assets utilized by the page.
    */
    protected $assets_path;
    
    /**
    * @var string The http path to the assets utilized by the page.
    */
    protected $assets_http_path;
    
    /**
    * @var string The http path to the module's css.
    */            
    protected $css_http_path;
    
    /**
    * @var string The http path to the module's javascript.
    */        
    protected $javascript_http_path;
    
    /**
    * @var boolean A flag that sets the page to be cached in memory and on the file system.
    */
    protected $cache_page = false;
    
    /**
    * @var boolean A flag that sets the page load javascript.
    */
    protected $enable_javascript;

    /**
    * @var string The class name of the page.
    */
    protected $name;
    
    /**
    * @var string The title of the page.
    */
    protected $title;
    
    /**
    * @var object The page body and its child elements.
    */
    protected $body;

    /**
     * Initializes a new instance of Page.
     *
     * @param string $page_name (optional) The name of the page. Defaults to an empty string. The name of the requested page is used if an empty string is specified.
     * @param boolean $enable_cache Indicates if this page should be cached. Defaults to false.
     * @return void
     */
    public function __construct($page_name = '') {    
        $this->framework = Framework::getInstance();

        if(empty($page_name)) {
            $this->name = $this->framework->getPageClassName();
        }
        else {
            $this->name = $page_name;
        }
        
        $this->enable_javascript = $this->framework->getConfiguration()->enable_javascript;
        
        $this->assets_path = $this->framework->getInstallationPath() . '/public/assets';
    }
    
    /**
     * Retrieves the body element of the page.
     *
     * @param $variable_name The name of the variable to retrieve. Should always be 'body'.       
     * @return object
     */
    public function body() {
        if(!isset($this->body)) {
            $this->body = new Body();
            
            $this->body->setPage($this);
        }
    
        return $this->body;
    }

    /**
     * Retrieves the name of the page.
     *     
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Sets the title for the page.
     *
     * @param string $title The title of the page.
     * @return void
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * Adds a meta tag to the page.
     *
     * @param string $index_name The array key used to make this tag unique when stored in the $meta_tag class property.
     * @param string $first_tag_name The name of the first attribute of the meta tag.
     * @param string $first_tag_value The value of the first attribute of the meta tag.
     * @param string $content The value of the content attribute of the meta tag.
     * @return void
     */
    public function addMetaTag($index_name, $first_tag_name, $first_tag_value, $content) {
        $meta_tag = array(
            $first_tag_name => $first_tag_value,
            'content' => $content
        );
    
        $this->meta_tags[$index_name] = $meta_tag;
    }
    
    /**
     * Validates a directory path and returns the valid directory path.
     *
     * @param string $directory_path The directory path.
     * @return string
     */
    protected function setDirectory($directory_path) {
        //Make the directory path have a trailing slash        
        return rtrim($directory_path, '/') . '/';
    }

    /**
     * Sets the default theme directory of the page.
     *
     * @param string $theme_directory_path The theme directory path.
     * @return void
     */
    public function setThemeDirectory($theme_directory_path) {
        $this->theme_directory_path = $this->setDirectory($theme_directory_path);
    }
    
    /**
     * Retrieves the path to the default theme directory of the page.
     *
     * @return string
     */
    public function getThemeDirectoryPath() {
        return $this->theme_directory_path;
    }
    
    /**
     * Sets the template file of the page.
     *
     * @param string $template_file_path The file path to the template file.
     * @return void
     */
    public function setTemplate($template_file_path, $relative = true) {    
        $this->template = new Template($template_file_path, $relative);
    }
    
    /**
     * Prepends css directories to the current list.
     *
     * @param array $css_directory_paths The list of paths css files can reside in.
     * @return void
     */
    public function prependCssDirectories(array $css_directory_paths) {        
        foreach($css_directory_paths as $css_directory_path) {
            $css_base_path = $this->setDirectory($css_directory_path);
        
            array_unshift($this->css_base_paths, $css_base_path);
        }
    }       
    
    /**
     * Sets the default css directories.
     *
     * @param array $css_directory_paths The list of paths css files can reside in.
     * @return void
     */
    public function setCssDirectories(array $css_directory_paths) {
        foreach($css_directory_paths as $css_directory_path) {        
            $this->css_base_paths[] = $this->setDirectory($css_directory_path);
        }
    }
    
    /**
     * Prepends javascript directories to the current list.
     *
     * @param array $javascript_directory_paths The list of paths javascript files can reside in.
     * @return void
     */
    public function prependJavascriptDirectories(array $javascript_directory_paths) {        
        foreach($javascript_directory_paths as $javascript_directory_path) {
            $javascript_base_path = $this->setDirectory($javascript_directory_path);
        
            array_unshift($this->javascript_base_paths, $javascript_base_path);
        }
    }        
    
    /**
     * Sets the default javascript directories.
     *
     * @param array $javascript_directory_path The list of paths javascript files can reside in.
     * @return void
     */
    public function setJavascriptDirectories(array $javascript_directory_paths) {
        foreach($javascript_directory_paths as $javascript_directory_path) {        
            $this->javascript_base_paths[] = $this->setDirectory($javascript_directory_path);
        }
    }
    
    /**
     * Adds inline css to be output on the page.
     *
     * @param string $inline_css The inline css.
     * @return void
     */
    public function addInlineCss($inline_css) {
        $this->inline_css[] = $inline_css;
    }
    
    /**
     * Adds a css file to be included on the page.
     *
     * @param string $css_file_path The file path to the css file.
     * @param boolean (optional) $internal A flag indicating if the css file path is an internal relative path. Defaults to true.
     * @return void
     */
    public function addCssFile($css_file_path, $internal = true) {        
        if($internal) {
            if(!isset($this->internal_css_files[$css_file_path]) && !empty($this->css_base_paths)) {            
                $trimmed_css_file_path = ltrim($css_file_path, '/');
                
                foreach($this->css_base_paths as $css_base_path) {
                    $possible_css_path = $css_base_path . $trimmed_css_file_path;
                    
                    if(is_file($possible_css_path)) {
                        $this->internal_css_files[$css_file_path] = $possible_css_path;
                        break;
                    }
                }
            }
        }
        else {
            $this->internal_css_files[$css_file_path] = $css_file_path;
        }
    }
    
    /**
     * Adds several css files to the page.
     *
     * @param array $css_files The file paths of the css files to add to the page.
     * @param boolean (optional) $internal A flag indicating if the css file path is an internal relative path. Defaults to true.
     * @return void
     */
    public function addCssFiles(array $css_files, $internal = true) {    
        if(!empty($css_files)) {
            foreach($css_files as $css_file) {
                $this->addCssFile($css_file, $internal);
            }
        }
    }
    
    /**
     * Adds inline javascript to be output on the page.
     *
     * @param string $inline_javascript The inline javascript.
     * @return void
     */
    public function addInlineJavascript($inline_javascript) {
        if(!$this->enable_javascript) {
            return false;
        }
    
        $this->inline_javascript[] = $inline_javascript;
    }
    
    /**
     * Adds a javascript file to be included on the page.
     *
     * @param string $javascript_file_path The file path to the javascript file.
     * @param boolean (optional) $internal A flag indicating if the javascript file path is an internal relative path. Defaults to true.
     * @return void
     */
    public function addJavascriptFile($javascript_file_path, $internal = true) {
        if(!$this->enable_javascript) {
            return false;
        }

        if($internal) {
            if(!isset($this->internal_javascript_files[$javascript_file_path]) && !empty($this->javascript_base_paths)) {
                $javascript_file_full_path = '';
            
                $trimmed_javascript_file_path = ltrim($javascript_file_path, '/');
                
                foreach($this->javascript_base_paths as $javascript_base_path) {
                    $possible_javascript_path = $javascript_base_path . $trimmed_javascript_file_path;

                    if(is_file($possible_javascript_path)) {
                        $this->internal_javascript_files[$javascript_file_path] = $possible_javascript_path;
                        break;
                    }
                }
            }
        }
        else {
            $this->external_javascript_files[$javascript_file_path] = $javascript_file_path;
        }
    }
    
    /**
     * Adds several javascript files to the page.
     *
     * @param array $javascript_files The file paths of the javascript files to add to the page.
     * @param boolean (optional) $internal A flag indicating if the javascript file path is an internal relative path. Defaults to true.
     * @return void
     */
    public function addJavascriptFiles(array $javascript_files, $internal = true) {
        if(!$this->enable_javascript) {
            return false;
        }
    
        if(!empty($javascript_files)) {
            foreach($javascript_files as $javascript_file) {
                $this->addJavascriptFile($javascript_file, $internal);
            }
        }
    }
    
    /**
     * Renders the page meta tags.
     *
     * @return string The rendered page meta tags.
     */
    protected function renderMetaTags() {
        $meta_tag_html = '';
        
        if(!empty($this->meta_tags)) {        
            foreach($this->meta_tags as $meta_tag) {
                $meta_tag_html .= '<meta';
                
                foreach($meta_tag as $tag_attribute_name => $tag_attribute_value) {
                    $meta_tag_html .= " {$tag_attribute_name}=\"{$tag_attribute_value}\"";
                }
                    
                $meta_tag_html .= " />";
            }
        }
        
        return $meta_tag_html;
    }
    
    /**
     * Renders the inline css and css file include tags of the page.
     *
     * @return string The rendered css.
     */
    protected function renderCss() {
        $css_html = "";
        
        if(!empty($this->external_css_files)) {
            foreach($this->external_css_files as $css_file) {
                $css_html .= "<link rel=\"stylesheet\" href=\"{$css_file}\" type=\"text/css\" />";
            }
        }

        if(!empty($this->name)) {
            $this->addCssFile("pages/{$this->name}.css");
        }
    
        if(!empty($this->internal_css_files)) {
            $css_file_cache_name = $this->name . implode('-', $this->internal_css_files);
        
            $css_hash_name = file_cache()->exists($css_file_cache_name, 'css/', 'gz');

            if($css_hash_name === false) {
                $css_minifier = new Css($this->internal_css_files);
            
                $css_temp_hash_name = file_cache()->exists($css_file_cache_name, 'css/', 'tmp');
            
                if($css_temp_hash_name === false) {
                    $css_hash_name = file_cache()->set($css_file_cache_name, $css_minifier->getUnminifiedData(), 'css/', 'tmp');
                }
                else {
                    $css_hash_name = $css_temp_hash_name;
                }
            }
            
            $css_http_path = "{$this->css_http_path}/{$css_hash_name}";
            
            $css_html .= "<link rel=\"stylesheet\" href=\"{$css_http_path}\" type=\"text/css\" />";
        }
        
        if(!empty($this->inline_css)) {
            $css_html .= "\n<style type=\"text/css\">\n" . implode("\n</style><style type=\"text/css\">\n", $this->inline_css) . "\n</style>";
        }
        
        return $css_html;
    }
    
    /**
     * Renders the inline javascript and javascript file include tags of the page.
     *
     * @return string The rendered javascript.
     */
    protected function renderJavascript() {
        if(!$this->enable_javascript) {
            return false;
        }
    
        $javascript_html = "";
        
        if(!empty($this->external_javascript_files)) {
            foreach($this->external_javascript_files as $javascript_file) {
                $javascript_html .= "<script type=\"text/javascript\" src=\"{$javascript_file}\"></script>";
            }
        }

        if(!empty($this->internal_javascript_files)) {
            $javascript_file_cache_name = $this->name . implode('-', $this->internal_javascript_files);
        
            $javascript_hash_name = file_cache()->exists($javascript_file_cache_name, 'javascript/', 'gz');
            
            if($javascript_hash_name === false) {
                $javascript_minifier = new Javascript($this->internal_javascript_files);
                
                $javascript_temp_hash_name = file_cache()->exists($javascript_file_cache_name, 'javascript/', 'tmp');
                
                if($javascript_temp_hash_name === false) {
                    $javascript_hash_name = file_cache()->set($javascript_file_cache_name, $javascript_minifier->getUnminifiedData(), 'javascript/', 'tmp');
                }
                else {
                    $javascript_hash_name = $javascript_temp_hash_name;
                }
            }
            
            $javascript_http_path = "{$this->javascript_http_path}/{$javascript_hash_name}";
            
            $javascript_html .= "<script type=\"text/javascript\" src=\"{$javascript_http_path}\"></script>";
        }
        
        if(!empty($this->name)) {
            $this->addJavascriptFile("{$this->name}.js");
        }
        
        if(!empty($this->inline_javascript)) {
            $javascript_html .= "\n<script type=\"text/javascript\">\n" . 
                implode("\n</script><script type=\"text/javascript\">\n", $this->inline_javascript) . 
                "\n</script>";
        }
        
        return $javascript_html;
    }
    
    /**
     * Renders the page title.
     *
     * @return string The rendered title tag.
     */
    protected function renderTitle() {
        return "<title>{$this->title}</title>";
    }
    
    /**
     * Retrieves page properties and content as an array for template parsing.
     *
     * @return array
     */
    protected function toTemplateArray() {
        $template_values = array();
        
        if(!empty($this->title)) {
            $template_values['title'] = $this->renderTitle();
        }
        
        if(!empty($this->meta_tags)) {
            $template_values['meta_tags'] = $this->renderMetaTags();
        }
    
        if(!empty($this->internal_css_files) || !empty($this->external_css_files) || !empty($this->inline_css)) {
            $template_values['css'] = $this->renderCss();
        }
        
        if(!empty($this->internal_javascript_files) || !empty($this->external_javascript_files) || !empty($this->inline_javascript)) {
            $template_values['javascript'] = $this->renderJavascript();
        }
        
        if(isset($this->body)) {
            $template_values = array_merge($template_values, $this->body->toTemplateArray());
        }
        
        return $template_values;
    }
    
    /**
     * Renders the page into html and returns it.
     *
     * @return string
     */
    public function render() {
        //Add all element files/inline code
        if(!empty($this->body)) {
            $element_files = $this->body->getElementFiles();
    
            $css_files = $element_files['css'];
            $javascript_files = $element_files['javascript'];
            $inline_javascript = $element_files['inline_javascript'];
    
            foreach($css_files as $css_file_path => $internal) {
                $this->addCssFile($css_file_path, $internal);
            }
    
            foreach($javascript_files as $javascript_file_path => $internal) {
                $this->addJavascriptFile($javascript_file_path, $internal);
            }
            
            foreach($inline_javascript as $inline_snippet) {
                $this->addInlineJavascript($inline_snippet);
            }
        }
    
        $page_html = "";

        if(isset($this->template)) {            
            $this->template->setPlaceholderValues($this->toTemplateArray());
            
            $page_html = $this->template->parseTemplate();
        }
        else {
            throw new Exception("This page does not have a template set.");
        }
        
        $framework_cache = $this->framework->getCache();

        //If caching is enabled and this page html is not cached then do so
        if($framework_cache->initialized() && $this->cache_page) {
            $html_minifier = new Html();
            $html_minifier->setUnminifiedData($page_html);
            $html_minifier->clean();
            $html_minifier->compress();
        
            $page_html = $html_minifier->getMinifiedData();
            
            file_cache()->set($this->name, $page_html, 'html/', 'gz');
        
            $framework_cache->set($this->name, $page_html, 'html');
        }
        else {
            ob_end_flush();
        }
        
        return $page_html;
    }
}