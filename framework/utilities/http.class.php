<?php
/**
* Provides functionality to perform various http tasks such as url compilation and redirection.
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
namespace Framework\Utilities;

use \Framework\Core\Framework;
use \Framework\Modules\ModulePage;

final class Http {
    /**
    * @var boolean Indicates if the query string of a generated url should be encrypted.
    */  
    private static $encrypt_urls = false;

    /**
    * @var string The base url of the current site. Acts as a cache so it is not generated more than once.
    */  
    private static $base_url;
    
    /**
    * @var array The query string parameters specified for the requested page. Acts as a cache so it is not retrieved more than once.
    */      
    private static $request_parameters;        
    
    /**
    * @var string The name of the default module.
    */  
    private static $default_module;
    
    /**
    * @var string The name of the module currently running.
    */ 
    private static $running_module;

    /**
    * Redirects a user to the specified page class name on the current site.
    * 
    * @param string $redirect_location The class name to redirect to.
    * @return void   
    */
    public static function redirect($redirect_location) {            
        switch(Framework::getInstance()->mode) {
            case 'page':
                header("Location: {$redirect_location}");
                break;
            case 'ajax':
                echo json_encode(array(
                    'redirect_location' => $redirect_location 
                ));
                break;
        }
        
        exit;
    }
    
    /**
    * Enables url query string encryption.
    * 
    * @return void
    */    
    public static function enableEncryptedUrls() {
        self::$encrypt_urls = true;
    }
    
    /**
    * Checks to see if SSL is enabled.
    * 
    * Compares the server HTTPS value with 'on' and returns the result.
    * 
    * @return boolean The boolean value indicating SSL status.
    */
    public static function usingSSL() {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
    }
    
    /**
    * Returns the base url of the current site.
    * 
    * @return string The base url of the site.
    */
    public static function getBaseUrl() {
        if(!isset(self::$base_url)) {
            self::$base_url = '';
            
            if(!self::usingSSL()) {
                self::$base_url .= 'http://';
            }
            else {
                self::$base_url .= 'https://';
            }
            
            self::$base_url .= $_SERVER["SERVER_NAME"];
            
            $port = $_SERVER["SERVER_PORT"];
            
            if($port != 80 && $port != 443) {
                self::$base_url .= ":{$port}";
            }

            $script_path = $_SERVER['SCRIPT_NAME'];
            
            if(!empty($script_path)) {
                self::$base_url .= rtrim(str_replace(array(
                    'index.php',
                    'ajax/'
                ), '', $script_path), '/');
            }

            self::$base_url .= '/';
        }
        
        return self::$base_url;
    }
    
    /**
    * Returns the url of the current page.
    * 
    * @param array $query_string_parameters (optional) The rest of the query string in ('name' => 'value') format.    
    * @param string $module_name (optional) The name of the module to include as an override in the url.      
    * @return string The page url.
    */
    public static function getPageUrl($query_string_parameters = array(), $module_name = '') {
        return Http::getCurrentLevelPageUrl(Framework::getInstance()->getPageClassName(), $query_string_parameters, $module_name);
    }
    
    /**
    * Returns the url of the current page with the full query string.
    * 
    * @return string
    */
    public static function getCurrentUrl() {
        if(!isset(self::$request_parameters)) {    
            self::$request_parameters = request()->get->getAll();
            
            if(isset(self::$request_parameters['subd'])) {
                unset(self::$request_parameters['subd']);        
            }
            
            if(isset(self::$request_parameters['page'])) {
                unset(self::$request_parameters['page']);
            }
        }
         
        return self::getInternalUrl('', Framework::getInstance()->getHttpSubPath(), Framework::getInstance()->getPageHttpName(), self::$request_parameters);
    }
    
    /**
    * Generates and returns a url.
    * 
    * @param string $base_url The base url.
    * @param array|string $query_string_parameters The query string parameters to add to the base url. Array format is parameter_name => parameter_value.
    * @return string The full url.
    */
    public static function generateUrl($base_url, $query_string_parameters, $cache_name = '') {
        assert('!empty($query_string_parameters)');
        
        $generated_url = $base_url;
        
        if(!empty($query_string_parameters)) {        
            if(strpos($generated_url, '?') !== false) {
                $generated_url .= '&';
            }
            else {
                $generated_url .= '?';
            }
            
            if(is_array($query_string_parameters)) {
                $generated_url .= http_build_query($query_string_parameters);
            }
            else {
                $generated_url .= $query_string_parameters;
            }
        }
        
        return $generated_url;
    }
    
    /**
    * Generates and a query string from an array of values.
    * 
    * @param array $query_string_parameters The query string parameters to add to url. Format is parameter_name => parameter_value.
    * @return string
    */
    public static function generateQueryString($query_string_parameters) {
        $query_string = http_build_query($query_string_parameters);
            
        if(self::$encrypt_urls) {
            $query_string = 'e=' . rtrim(strtr(Encryption::encrypt($query_string, array('encrypted_url')), '+/', '-_'), '=');
        }
    
        return $query_string;
    }
    
    /**
    * Generates and returns an internal url.
    * 
    * @param string $module_name (optional)
    * @param array $subdirectory_path (optional)
    * @param string $page (optional)        
    * @param array $query_string_parameters (optional) The query string parameters to add to url. Format is parameter_name => parameter_value.
    * @return string
    */
    public static function getInternalUrl($module_name = '', $subdirectory_path = array(), $page = '', $query_string_parameters = array()) {
        assert('is_array($subdirectory_path) && is_array($query_string_parameters)');
        
        if(!isset(self::$default_module)) {
            self::$default_module = Framework::getInstance()->configuration->default_module;
        }
        
        if(!isset(self::$running_module)) {
            self::$running_module = ModulePage::getRunningModule();
        }

        if(empty($module_name)) {
            $module_name = self::$running_module;
        }
        
        $url = self::getBaseUrl();

        $page_path = array();

        if(!empty($subdirectory_path[0]) && $subdirectory_path[0] == $module_name) {        
            $module_name = '';
        }
        elseif(!empty($subdirectory_path[1]) && $subdirectory_path[1] == 'admin') {
            $module_name = '';
        }

        if(!empty($module_name)) {            
            array_unshift($subdirectory_path, $module_name);
        }                
           
        if(!empty($subdirectory_path)) {                        
            $page_path['subd'] = implode('/', $subdirectory_path);
        }
        
        if(!empty($page)) {
            $page_path['page'] = $page; 
        }
        
        if(!empty($page_path)) {
            if(Framework::getInstance()->environment == 'development') {
                $url .= '?' . http_build_query($page_path);
                
                //Decode encoded '/' characters
                $url = str_replace('%2F', '/', $url);
            }
            else {
                $url .= implode('/', $page_path);
                
                if(!isset($page_path['page'])) {
                    $url .= '/';
                }
            }
        }

        if(!empty($query_string_parameters)) {
            if(strpos($url, '?') !== false) {
                $url .= '&';
            }
            else {
                $url .= '?';
            }
            
            $url .= self::generateQueryString($query_string_parameters);
        }

        return $url;
    }
    
    /**
    * Generates and retrieves a url of a page that resides in the top level of a module.
    * 
    * @param string $page_name (optional) The name of the page.
    * @param array $query_string_parameters (optional) The rest of the query string in ('name' => 'value') format.
    * @param string $module_name (optional) The name of the module to include as an override in the url.    
    * @return string
    */
    public static function getTopLevelPageUrl($page_name = '', $query_string_parameters = array(), $module_name = '') {
        return self::getInternalUrl($module_name, array(), $page_name, $query_string_parameters);
    }
    
    /**
    * Generates and retrieves a url of a page that includes the current module and a subdirectory level one level higher than the current.
    * 
    * @param string $page_name (optional) The name of the page.
    * @param array $query_string_parameters (optional) The rest of the query string in ('name' => 'value') format.
    * @param string $module_name (optional) The name of the module to include as an override in the url.
    * @return string
    */
    public static function getHigherLevelPageUrl($page_name = '', $query_string_parameters = array(), $module_name = '') {    
        $subdirectory_path = Framework::getInstance()->getHttpSubPath();
        array_pop($subdirectory_path);

        return self::getInternalUrl($module_name, $subdirectory_path, $page_name, $query_string_parameters);
    }
    
    /**
    * Generates and retrieves a url of a page that includes the current module and subdirectory level.
    * 
    * @param string $page_name (optional) The name of the page.
    * @param array $query_string_parameters (optional) The rest of the query string in ('name' => 'value') format.
    * @param string $module_name (optional) The name of the module to include as an override in the url.
    * @return string
    */
    public static function getCurrentLevelPageUrl($page_name = '', $query_string_parameters = array(), $module_name = '') {
        return self::getInternalUrl($module_name, Framework::getInstance()->getHttpSubPath(), $page_name, $query_string_parameters);
    }
    
    /**
    * Generates and retrieves a url of a page that includes the current module and a subdirectory level lower than the current.
    * 
    * @param array $subdirectories The subdirectories under the current level.    
    * @param string $page_name (optional) The name of the page.
    * @param array $query_string_parameters (optional) The rest of the query string in ('name' => 'value') format.    
    * @param string $module_name (optional) The name of the module to include as an override in the url.
    * @return string
    */
    public static function getLowerLevelPageUrl($subdirectories, $page_name = '', $query_string_parameters = array(), $module_name = '') {
        assert('is_array($subdirectories)');
    
        $subdirectory_path = array_merge(Framework::getInstance()->getHttpSubPath(), $subdirectories);

        return self::getInternalUrl($module_name, $subdirectory_path, $page_name, $query_string_parameters);
    }
}