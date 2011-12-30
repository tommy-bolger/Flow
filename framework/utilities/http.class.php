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

final class Http {
    /**
    * @var string The base url of the current site. Acts as a cache so it is not generated more than once.
    */  
    private static $base_url;
    
    /**
    * @var string The base url of the site with the '?page=' parameter appended. Acts as a cache so it is not generated more than once.
    */  
    private static $current_base_url;
    
    /**
    * @var string The url of the current page. Acts as a cache so it is not generated more than once.
    */  
    private static $page_url;

    /**
    * Redirects a user to the specified page class name on the current site.
    * 
    * @param string $redirect_location The class name to redirect to.
    * @return void   
    */
    public static function redirect($redirect_location) {
        header("Location: {$redirect_location}");
    }
    
    /**
    * Checks to see if SSL is enabled.
    * 
    * Compares the server HTTPS value with 'on' and returns the result.
    * 
    * @return boolean The boolean value indicating SSL status.
    */
    public static function usingSSL() {
        return environment('HTTPS') == 'on';
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
            
            self::$base_url .= environment("SERVER_NAME");
            
            $port = environment("SERVER_PORT");
            
            if($port != 80 && $port != 443) {
                self::$base_url .= ":{$port}";
            }

            $script_path = $_SERVER['SCRIPT_NAME'];
            
            if(!empty($script_path)) {
                 self::$base_url .= rtrim(str_replace('index.php', '', $script_path), '/');
            }
            
            self::$base_url .= '/';
        }
        
        return self::$base_url;
    }
    
    /**
    * Returns the base url of the current site with '?page=' appended.
    * 
    * @return string The page base url.
    */
    public static function getPageBaseUrl($module_name = NULL, $subdirectories = array()) {
        if(empty($module_name)) {
            $module_name = framework()->getModuleName();
        }
        
        $subdirectory_path = '';
        
        if(!empty($subdirectories)) {
            $subdirectory_path = '&' . http_build_query($subdirectories);
        }
        
        return self::getBaseUrl() . "?module={$module_name}{$subdirectory_path}&page=";
    }
    
    /**
    * Returns the base url of the current page with '?page=' appended.
    * 
    * @return string The page base url.
    */
    public static function getCurrentBaseUrl() {
        if(!isset(self::$current_base_url)) {
            $framework = framework();
            
            $module_name = $framework->getModuleName();
            
            $subdirectories = $framework->getSubdirectories();
            
            self::$current_base_url = self::getPageBaseUrl($module_name, $subdirectories);
        }
    
        return self::$current_base_url;
    }
    
    /**
    * Returns the url of the current page.
    * 
    * @return string The page url.
    */
    public static function getPageUrl($page_name = NULL, $module_name = NULL) {
        if(!isset(self::$page_url)) {
            $framework = framework();
        
            self::$page_url = self::getCurrentBaseUrl() . $framework->getPageClassName();
        }
        
        return self::$page_url;
    }
    
    /**
    * Returns the url of the current page with the full query string.
    * 
    * @return string
    */
    public static function getCurrentUrl() {
        return self::getBaseUrl() . '?' . environment('QUERY_STRING');
    }
    
    /**
    * Generates and returns a url.
    * 
    * @param string $base_url The base url.
    * @param array $query_string_parameters The query string parameters to add to the base url. Format is parameter_name => parameter_value.
    * @return string The full url.
    */
    public static function generateUrl($base_url, $query_string_parameters, $cache_name = '') {
        assert('!empty($query_string_parameters) && is_array($query_string_parameters)');
        
        $generated_url = $base_url;
        
        if(!empty($query_string_parameters)) {        
            if(strpos($generated_url, '?') !== false) {
                $generated_url .= '&';
            }
            else {
                $generated_url .= '?';
            }
            
            $generated_url .= http_build_query($query_string_parameters);
        }
        
        return $generated_url;
    }
}