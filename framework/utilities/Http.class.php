<?php
/**
* Provides functionality to perform various http tasks such as url compilation and redirection.
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
final class Http {
    /**
    * @var string The base url of the current site. Acts as a cache so it is not generated more than once.
    */  
    private static $base_url;
    
    /**
    * @var string The base url of the site with the '?page=' parameter appended. Acts as a cache so it is not generated more than once.
    */  
    private static $page_base_url;
    
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
        header("Location: ?page={$redirect_location}");
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
            
            self::$base_url .= '/';
        }
        
        return self::$base_url;
    }
    
    /**
    * Returns the base url of the current site with '?page=' appended.
    * 
    * @return string The page base url.
    */
    public static function getPageBaseUrl() {
        if(!isset(self::$page_base_url)) {
            self::$page_base_url = self::getBaseUrl() . '?page=';
        }
        
        return self::$page_base_url;
    }
    
    /**
    * Returns the url of the current page.
    * 
    * @return string The page url.
    */
    public static function getPageUrl() {
        if(!isset(self::$page_url)) {
            self::$page_url = self::getPageBaseUrl() . page()->getPageName();
        }
        
        return self::$page_url;
    }
    
    /**
    * Generates and returns a url.
    * 
    * @param string $base_url The base url.
    * @param array $query_string_parameters The query string parameters to add to the base url. Format is parameter_name => parameter_value.
    * @return string The full url.
    */
    public static function generateUrl($base_url, $query_string_parameters) {
        assert('!empty($query_string_parameters) && is_array($query_string_parameters)');
    }
}