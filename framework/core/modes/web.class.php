<?php
/**
* A parent class that handles common functionality for http requests.
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

use \Framework\Core\Framework;

require_once(dirname(__DIR__) . '/framework.class.php');

class Web
extends Framework {
    protected $request_method;

    /**
     * Initializes an instance of the framework in web mode.
     *
     * @return void
     */
    public function __construct($mode = 'web') {
        header('Server: Web Server');
        header('X-Powered-By: Flow CMS by Tommy Bolger');
        
        parent::__construct($mode);
        
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        
        if($this->configuration->environment == 'maintenance') {
            /*
                Send a 503 error code to tell clients that the request cannot be completed due to maintenance.
                The below header code was adapted from here: https://yoast.com/http-503-site-maintenance-seo/
            */
            $protocol = "HTTP/1.0";
        
            if("HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"]) {
                $protocol = "HTTP/1.1";
            }
            
            header("{$protocol} 503 Service Unavailable", true, 503);
            header("Retry-After: 7200");
        
            $this->runMaintenance();
            
            exit;
        }
    }
    
    /**
     * Executes the maintenance mode for the current mode. 
     *
     * @return void
     */
    protected function runMaintenance() {}
    
    /**
     * Retrieves the parsed request uri.
     *
     * @return string
     */
    public function getParsedUri() {
        $unparsed_uri = $_SERVER['REQUEST_URI'];
        
        if(strpos($unparsed_uri, '?') !== false) {
            $unparsed_uri_split = explode('?', $unparsed_uri);
            
            $unparsed_uri = $unparsed_uri_split[0];
        }
        
        if(strpos($unparsed_uri, '&') !== false) {
            $unparsed_uri_split = explode('&', $unparsed_uri);
            
            $unparsed_uri = $unparsed_uri_split[0];
        }
        
        if(strpos($unparsed_uri, '#') !== false) {
            $unparsed_uri_split = explode('#', $unparsed_uri);
            
            $unparsed_uri = $unparsed_uri_split[0];
        }
        
        $unparsed_uri = str_replace(array(
            'index.php',
            '//'
        ), array(
            '',
            '/'
        ), $unparsed_uri);
        
        return $unparsed_uri;
    }
    
    /**
     * Retrieves the action name based on the request method.
     *
     * @return string
     */
    protected function getActionName() {
        $action = 'action';
    
        switch($this->request_method) {
            case 'GET':
                $action .= 'Get';
                break;
            case 'POST':
                $action .= 'Post';
                break;
            case 'DELETE':
                $action .= 'Delete';
                break;
            case 'PUT':
                $action .= 'Put';
                break;
            default:
                throw new Exception("Request method '{$this->request_method}' is invalid. Valid values are GET, POST, DELETE, and PUT.");
                break;
        }
        
        return $action;
    }
    
    /**
     * Retrieves the request method.
     *
     * @return string
     */
    public function getRequestMethod() {
        return $this->request_method;
    }
}