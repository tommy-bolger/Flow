<?php
/**
* Allows the rendering of an html page and its child elements dynamically.
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

namespace Framework\Html;

use \Framework\Core\Framework;
use \Framework\Utilities\Minify;
use \Framework\Utilities\Http;
use \Framework\Display\Template;
use \Framework\Html\Body;

class page {
    /**
    * @var object Stores an instance of this class.
    */
    private static $page;

    /**
    * @var array A list of valid html doctype tags and their html.
    */
    private static $html_doctypes = array(
        'html_401_strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html_401_transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html_401_frameset' =>  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        'xhtml_1_strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml_1_transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml_1_frameset' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'xhtml_1_1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
    );
    
    /**
    * @var object The template for the page.
    */
    private $template;
    
    /**
    * @var string The selected doctype for the page.
    */
    private $doctype;
    
    /**
    * @var array A list of specified meta tags for the page.
    */
    private $meta_tags = array();

    /**
    * @var string The file path to the page style including templates, images, and css.
    */
    private $theme_directory_path;
    
    /**
    * @var array The file path list to the css files.
    */
    private $css_base_paths = array();
    
    /**
    * @var array The file path list to the javascript files.
    */
    private $javascript_base_paths = array();
    
    /**
    * @var array A list of all inline css to be output on the page.
    */
    private $inline_css = array();
    
    /**
    * @var array A list of the css files included by the page that are located on the same server as the current page.
    */
    private $internal_css_files = array();
    
    /**
    * @var array A list of the css files included by the page that are located on an external server.
    */
    private $external_css_files = array();
    
    /**
    * @var array A list of all inline javascript to be output on the page.
    */
    private $inline_javascript = array();
    
    /**
    * @var array A list of the javascript files included by the page that are located on the same server as the current page.
    */
    private $internal_javascript_files = array();
    
    /**
    * @var array A list of the javascript files included by the page that are located on an external server.
    */
    private $external_javascript_files = array();
    
    /**
    * @var string The file path to the assets utilized by the page.
    */
    protected $assets_path;
    
    /**
    * @var boolean A flag that sets the page to be cached in memory and on the file system.
    */
    protected $cache_page = false;
    
    /**
    * @var boolean A flag that sets the page load javascript.
    */
    protected $enable_javascript;
    
    /**
    * @var boolean A flag that tells the page to automatically include analytics javascript.
    */
    protected $load_analytics = false;
    
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
    private $body;
    
    /**
     * Retrieves the current instance of Page.
     *
     * @return object
     */
    public static function getPage() {
        if(!isset(self::$page)) {
            throw new \Exception("The current page as not been instantiated and cannot be retrieved.");
        }
    
        return self::$page;
    }

    /**
     * Initializes a new instance of Page.
     *          
     * @return void
     */
    public function __construct() {
        if(!isset($this->name)) {
            $this->name = framework()->getPageClassName();
        }
    
        //Add this header to help prevent clickjacking attacks in modern browsers
        header('X-Frame-Options: DENY');

        if(Framework::$enable_cache && $this->cache_page) {
            header('Content-Encoding: gzip');

            //Immediately call the displayCache function
            $this->displayCache();
        }
        else {
            ini_set('zlib.output_compression', 1);

            ini_set('zlib.output_compression_level', 9);
            
            ob_start();
        }
        
        if(!isset(self::$page)) {
            self::$page = $this;
        }
        else {
            throw new \Exception("Another page has already been initialized.");
        }
        
        $this->enable_javascript = config('framework')->enable_javascript;
        
        //Set the assets location
        $this->assets_path = framework()->installation_path . '/public/assets';
    }
    
    /**
     * Catches calls to functions not in this class and throws an exception to prevent a fatal error.
     *
     * @param $function_name The name of the called function.
     * @param $arguments The arguments of the called function.          
     * @return void
     */
    public function __call($function_name, $arguments) {
        throw new \Exception("Function '{$function_name}' does not exist in this class.");
    }
    
    /**
     * Retrieves the body element of the page.
     *
     * @param $variable_name The name of the variable to retrieve. Should always be 'body'.       
     * @return object
     */
    public function __get($variable_name) {
        assert('$variable_name == "body"');

        if(!isset($this->body)) {
            $this->body = new Body();
        }
    
        return $this->body;
    }

    /**
     * Retrieves the name of the page.
     *     
     * @return string
     */
    public function getPageName() {
        return $this->name;
    }
    
    /**
     * Retrieves the assets path;
     *     
     * @return string
     */
    public function getAssetsPath() {
        return $this->assets_path;
    }
    
    /**
     * Sets the page doctype.
     *
     * @param string $html_type The version of html to use for the page. Can either be 'xhtml_1.0', 'html_4.01', or 'xhtml_1.1'.
     * @param string $mode (optional) The version mode. Can either be 'transitional', 'frameset', or 'strict'.     
     * @return void
     */
    public function setDoctype($html_type, $mode = NULL) {        
        switch($html_type) {
            case 'xhtml_1.0':
                $this->doctype = 'xhtml_1';
                break;
            case 'html_4.01':
                $this->doctype = 'xhtml_401';
                break;
            case 'xhtml_1.1':
                $this->doctype = 'xhtml_1_1';
                return;
                break;
            default:
                throw new \Exception("The specified html_type '{$html_type}' is not valid.");
                break;
        }
        
        switch($mode) {
            case 'transitional':
            case 'frameset':
            case 'strict':
                $this->doctype .= "_{$mode}";
                break;
            default:
                throw new \Exception("The specified html doctype mode '{$mode}' is not valid.");
                break;
        }
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
    protected function addMetaTag($index_name, $first_tag_name, $first_tag_value, $content) {
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
    private function setDirectory($directory_path) {
        assert('is_readable($directory_path)');

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
     * Sets the default css directories.
     *
     * @param array $css_directory_path The list of paths css files can reside in.
     * @return void
     */
    public function setCssDirectories($css_directory_paths) {
        assert('is_array($css_directory_paths) && !empty($css_directory_paths)');

        foreach($css_directory_paths as $css_directory_path) {        
            $this->css_base_paths[] = $this->setDirectory($css_directory_path);
        }
    }
    
    /**
     * Sets the default javascript directories.
     *
     * @param array $javascript_directory_path The list of paths javascript files can reside in.
     * @return void
     */
    public function setJavascriptDirectories($javascript_directory_paths) {
        assert('is_array($javascript_directory_paths) && !empty($javascript_directory_paths)');

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
    public function addCssFiles($css_files, $internal = true) {
        assert('is_array($css_files)');
    
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
    public function addJavascriptFiles($javascript_files, $internal = true) {
        if(!$this->enable_javascript) {
            return false;
        }
    
        assert('is_array($javascript_files)');
    
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
    private function renderMetaTags() {
        $meta_tag_html = '';
        
        if(Framework::$enable_cache) {
            $meta_tag_html = cache()->get($this->name, 'meta');
            
            if(!empty($meta_tag_html)) {
                return $meta_tag_html;
            }
        }
        
        if(!empty($this->meta_tags)) {        
            foreach($this->meta_tags as $meta_tag) {
                $meta_tag_html .= '<meta';
                
                foreach($meta_tag as $tag_attribute_name => $tag_attribute_value) {
                    $meta_tag_html .= " {$tag_attribute_name}=\"{$tag_attribute_value}\"";
                }
                    
                $meta_tag_html .= " />";
            }
            
            if(Framework::$enable_cache) {
                cache()->set($this->name, $meta_tag_html, 'meta');
            }
        }
        
        return $meta_tag_html;
    }
    
    /**
     * Renders the inline css and css file include tags of the page.
     *
     * @return string The rendered css.
     */
    private function renderCss() {
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
            if(framework()->getEnvironment() == 'production') {
                $css_file_cache_name = $this->name . implode('-', $this->internal_css_files);
            
                $css_hash_name = file_cache()->exists($css_file_cache_name, 'css/', 'gz');
            
                if($css_hash_name === false) {
                    $css_hash_name = file_cache()->set($css_file_cache_name, Minify::minifyCss($this->internal_css_files, "{$this->name}_css_temp"), 'css/', 'gz');
                }
                
                $css_http_path = Http::getBaseUrl() . "assets/css/{$css_hash_name}";
                
                $css_html .= "<link rel=\"stylesheet\" href=\"{$css_http_path}\" type=\"text/css\" />";
            }
            else {
                $base_url = Http::getBaseUrl() . '/assets/css/?file=';
            
                foreach($this->internal_css_files as $css_file) {
                    $css_file = str_replace('./', '', $css_file);
                
                    $css_html .= "<link rel=\"stylesheet\" href=\"{$base_url}{$css_file}\" type=\"text/css\" />";
                }
            }
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
    private function renderJavascript() {
        if(!$this->enable_javascript) {
            return false;
        }
    
        $javascript_html = "";
        
        if(!empty($this->external_javascript_files)) {
            foreach($this->external_javascript_files as $javascript_file) {
                $javascript_html .= "<script type=\"text/javascript\" src=\"{$javascript_file}\"></script>";
            }
        }
        
        if($this->load_analytics) {
            $google_analytics_path = "{$this->assets_path}/javascript/google_analytics.js";
            
            if(is_readable($google_analytics_path)) {
                $this->addJavascriptFile($google_analytics_path, false);
            }
        }

        if(!empty($this->internal_javascript_files)) {
            if(framework()->getEnvironment() == 'production') {
                $javascript_file_cache_name = $this->name . implode('-', $this->internal_javascript_files);
            
                $javascript_hash_name = file_cache()->exists($javascript_file_cache_name, 'javascript/', 'gz');
                
                if($javascript_hash_name === false) {
                    $javascript_hash_name = file_cache()->set($javascript_file_cache_name, Minify::minifyJavascript($this->internal_javascript_files, "{$this->name}_javascript_temp"), 'javascript/', 'gz');
                }
                
                $javascript_http_path = Http::getBaseUrl() . "assets/javascript/{$javascript_hash_name}";
                
                $javascript_html .= "<script type=\"text/javascript\" src=\"{$javascript_http_path}\"></script>";
            }
            else {
                $base_url = Http::getBaseUrl() . '/assets/javascript/?file=';
            
                foreach($this->internal_javascript_files as $javascript_file) {
                    $javascript_html .= "<script type=\"text/javascript\" src=\"{$base_url}{$javascript_file}\"></script>";
                }
            }
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
    private function renderTitle() {
        return "<title>{$this->title}</title>";
    }
    
    /**
     * Renders the page head html tag.
     *
     * @return string The rendered head tag.
     */
    private function renderHeader() {
        $header_html = '<head>';

        //Render the title
        $header_html .= "<title>{$this->title}</title>";
        
        $header_html .= $this->renderMetaTags();
        $header_html .= $this->renderCss();
        $header_html .= $this->renderJavascript();

        $header_html .= '</head>';
        
        return $header_html;
    }
    
    /**
     * Attempts to display the cached html of this page from either the framework cache object or from the file cache.
     *
     * @return void
     */
    public function displayCache() {
        //If the current page html is already cached then output it and exit
        $page_cache = cache()->get($this->name, 'html');
    
        if(!empty($page_cache)) {
            echo $page_cache;

            exit;
        }
        else {
            //Attempt to retrieve the page from the file cache and subsequently store it in memory cache
            $page_cache = file_cache()->get($this->name, 'html/', 'gz');
            
            if(!empty($page_cache)) {            
                cache()->set($this->name, $page_cache, 'html');

                echo $page_cache;

                exit;
            }
        }
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
        
        $template_values = array_merge($template_values, $this->body->toTemplateArray());
        
        return $template_values;
    }
    
    /**
     * Renders the page and its elements to html.
     *
     * @return string
     */
    public function toHtml() {
        $page_html = "<?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">";
        
        //Add the html doctype to this page
        if(!empty($this->doctype)) {
            $page_html .= self::$html_doctypes[$this->doctype];
        }
        //Otherwise add the default HTML 4.01 Strict doctype
        else {
            $page_html .= self::$html_doctypes['html_401_strict'];
        }
        
        $page_html .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
        
        $page_html .= $this->renderHeader();
        
        $page_html .= $this->body->toHtml();
        
        $page_html .= "</html>";
        
        return $page_html;
    }
    
    /**
     * Renders the page into html and outputs it to a user.
     *
     * @return void
     */
    public function display() {
        $page_html = "";

        if(isset($this->template)) {
            if(isset($this->body)) {
                $this->template->setPlaceholderValues($this->toTemplateArray());
            }
            
            $page_html = $this->template->parseTemplate();
        }
        else {
            $page_html = $this->toHtml();
        }
        
        //If caching is enabled and this page html is not cached then do so
        if(Framework::$enable_cache && $this->cache_page) {
            $page_html = Minify::minifyHtml($page_html);
            
            file_cache()->set($this->name, $page_html, 'html/', 'gz');
        
            cache()->set($this->name, $page_html, 'html');
        }
        else {
            ob_end_flush();
        }
        
        echo $page_html;
    }
}