<?php
/**
* Allows the rendering of an html page and its child elements dynamically.
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
class Page {
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
    * @var string The file path to the page javascript.
    */
	private $javascript_base_path;
	
	/**
    * @var array A list of all inline css to be output on the page.
    */
	private $inline_css = array();
	
	/**
    * @var array A list of the css files included by the page.
    */
	private $css_files = array();
	
	/**
    * @var array A list of all inline javascript to be output on the page.
    */
	private $inline_javascript = array();
	
	/**
    * @var array A list of the javascript files included by the page.
    */
	private $javascript_files = array();
	
	/**
    * @var string The file path to the assets utilized by the page.
    */
	protected $assets_path;
	
	/**
    * @var boolean A flag that sets the page to be cached in memory and on the file system.
    */
	protected $cache_page = false;
	
	/**
    * @var boolean A flag that tells the page to automatically include analytics javascript.
    */
	protected $load_analytics = false;
	
	/**
    * @var string The name of the page. Should always be set to __CLASS__ for child objects.
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
            throw new Exception("The current page as not been instantiated and cannot be retrieved.");
        }
	
        return self::$page;
	}

    /**
	 * Initializes a new instance of Page.
	 *	 	 
	 * @return void
	 */
	public function __construct() {
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
            throw new Exception("Another page has already been initialized.");
        }
        
        //Set the assets location
        $this->assets_path = rtrim(config('framework')->getParameter('assets_path'), '/');
	}
	
	/**
	 * Catches calls to functions not in this class and throws an exception to prevent a fatal error.
	 *
	 * @param $function_name The name of the called function.
	 * @param $arguments The arguments of the called function.     	 
	 * @return void
	 */
	public function __call($function_name, $arguments) {
        throw new Exception("Function '{$function_name}' does not exist in this class.");
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
                throw new Exception("The specified html_type '{$html_type}' is not valid.");
                break;
        }
        
        switch($mode) {
            case 'transitional':
            case 'frameset':
            case 'strict':
                $this->doctype .= "_{$mode}";
                break;
            default:
                throw new Exception("The specified html doctype mode '{$mode}' is not valid.");
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
	public function setTemplate($template_file_path) {	
        $this->template = new Template($template_file_path);
	}
	
	/**
	 * Sets the default javascript directory.
	 *
	 * @param string $javascript_directory_path The javascript directory path.
	 * @return void
	 */
	public function setJavascriptDirectory($javascript_directory_path) {
        $this->javascript_base_path = $this->setDirectory($javascript_directory_path);
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
	 * @param boolean (optional) $relative A flag indicating if the css file path is relative to the current page theme directory path. Defaults to true.
	 * @return void
	 */
	public function addCssFile($css_file_path, $relative = true) {
        $css_file_full_path = '';
	
        if($relative) {
            assert('isset($this->theme_directory_path)');
        
            $css_file_path = ltrim($css_file_path, '/');
        
            $css_file_full_path = $this->theme_directory_path . "css/{$css_file_path}";
        }
        else {
            $css_file_full_path = $css_file_path;
        }
        
        $css_file_index = md5($css_file_full_path);
        
        $this->css_files[$css_file_index] = $css_file_full_path;
	}
	
	/**
	 * Adds several css files to the page.
	 *
	 * @param array $css_files The file paths of the css files to add to the page.
	 * @param boolean (optional) $relative A flag indicating if the css file paths are relative to the current page theme directory path. Defaults to true.
	 * @return void
	 */
	public function addCssFiles($css_files, $relative = true) {
        assert('is_array($css_files)');
	
        if(!empty($css_files)) {
            foreach($css_files as $css_file) {
                $this->addCssFile($css_file, $relative);
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
        $this->inline_javascript[] = $inline_javascript;
	}
	
	/**
	 * Adds a javascript file to be included on the page.
	 *
	 * @param string $javascript_file_path The file path to the javascript file.
	 * @param boolean (optional) $relative A flag indicating if the javascript file path is relative to the page javascript directory path. Defaults to true.
	 * @return void
	 */
	public function addJavascriptFile($javascript_file_path, $relative = true) {
        $javascript_file_full_path = '';
	
        if($relative) {
            $javascript_file_path = ltrim($javascript_file_path, '/');
        
            $javascript_file_full_path = $this->javascript_base_path . $javascript_file_path;
        }
        else {
            $javascript_file_full_path = $javascript_file_path;
        }
        
        $javascript_file_index = md5($javascript_file_full_path);
        
        $this->javascript_files[$javascript_file_index] = $javascript_file_full_path;
	}
	
	/**
	 * Adds several javascript files to the page.
	 *
	 * @param array $javascript_files The file paths of the javascript files to add to the page.
	 * @param boolean (optional) $relative A flag indicating if the javascript file paths are relative to the page javascript directory path. Defaults to true.
	 * @return void
	 */
	public function addJavascriptFiles($javascript_files, $relative = true) {
        assert('is_array($javascript_files)');
	
        if(!empty($javascript_files)) {
            foreach($javascript_files as $javascript_file) {
                $this->addJavascriptFile($javascript_file, $relative);
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
        
        $page_css = "{$this->theme_directory_path}css/{$this->name}.css";

        if(is_file($page_css)) {
            $this->addCssFile($page_css, false);
        }
	
        if(!empty($this->css_files)) {
            if(Framework::getEnvironment() == 'production') {
                $css_file_cache_name = $this->name . implode('-', $this->css_files);
            
                $css_hash_name = file_cache()->exists($css_file_cache_name, 'css/', 'gz');
            
                if($css_hash_name === false) {
                    $css_hash_name = file_cache()->set($css_file_cache_name, Minify::minifyCss($this->css_files, "{$this->name}_css_temp"), 'css/', 'gz');
                }
                
                $css_html .= "<link rel=\"stylesheet\" href=\"{$this->assets_path}/css/?{$css_hash_name}\" type=\"text/css\" />";
            }
            else {
                foreach($this->css_files as $css_file) {
                    $css_html .= "<link rel='stylesheet' href='{$css_file}' type='text/css' />";
                }
            }
        }
        
        if(!empty($this->inline_css)) {
            $css_html .= "\n<style type=\"text/css\">\n" . 
                implode("\n</style><style type=\"text/css\">\n", $this->inline_css) . "\n</style>";
        }
        
        return $css_html;
	}
	
	/**
	 * Renders the inline javascript and javascript file include tags of the page.
	 *
	 * @return string The rendered javascript.
	 */
	private function renderJavascript() {
        $javascript_html = "";
        
        if(!empty($this->name)) {
            $page_javascript = "{$this->javascript_base_path}{$this->name}.js";
            
            if(is_file($page_javascript)) {
                $this->addJavascriptFile($page_javascript, false);
            }
        }
        
        if($this->load_analytics) {
            $google_analytics_path = "{$this->assets_path}/javascript/google_analytics.js";
            
            if(is_readable($google_analytics_path)) {
                $this->addJavascriptFile($google_analytics_path, false);
            }
        }

        if(!empty($this->javascript_files)) {
            if(Framework::getEnvironment() == 'production') {
                $javascript_file_cache_name = $this->name . implode('-', $this->javascript_files);
            
                $javascript_hash_name = file_cache()->exists($javascript_file_cache_name, 'javascript/', 'gz');
                
                if($javascript_hash_name === false) {
                    $javascript_hash_name = file_cache()->set($javascript_file_cache_name, Minify::minifyJavascript($this->javascript_files, "{$this->name}_javascript_temp"), 'javascript/', 'gz');
                }
                
                $javascript_html .= "<script type=\"text/javascript\" src=\"{$this->assets_path}/javascript/?{$javascript_hash_name}\"></script>";
            }
            else {
                foreach($this->javascript_files as $javascript_file) {
                    $javascript_html .= "<script type=\"text/javascript\" src=\"{$javascript_file}\"></script>";
                }
            }
        }
        
        if(!empty($this->inline_javascript)) {
            $javascript_html .= "\n<script type=\"text/javascript\">\n" . 
                implode("\n</script><script type=\"text/javascript\">\n", $this->inline_javascript) . 
                "\n</script>";
        }
        
        return $javascript_html;
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
	 * Renders the page into html and outputs it to a user.
	 *
	 * @return void
	 */
	public function display() {
        $page_html = "";
        
        if(isset($this->template)) {
            if(isset($this->body)) {
                $page_html = $this->template->parseTemplate($this->body->toTemplateArray());
            }
            else {
                $page_html = $this->template->parseTemplate();
            }
        }
        else {
            $page_html = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        
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