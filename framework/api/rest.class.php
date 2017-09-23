<?php
/**
* The base class for all Rest API clients in the framework.
*
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

namespace Framework\Api;

use \Exception;

class Rest {    
    /**
     * @var array $http_success_codes The list of all success codes returned by the Rest service.
     */
    protected static $http_success_codes = array(
        200 => 200,
        201 => 201,
        204 => 204
    );
    
    /**
     * @var array $http_error_codes The list of all error codes returned by the Rest service and their meanings.
     */
    protected static $http_error_codes = array(
        999 => array(
            'message' => 'Example message',
            'description' => "Example description."
        )
    );
    
    /**
     * @var string $base_url The base url for all requests to the service.
     */
    protected $base_url;
    
    /**
     * @var string $auth_key The key to use for requests that require authorization.
     */
    protected $api_key;
    
    /**
     * @var string $api_key_name The name of the api key that the Rest service uses.
     */
    protected $api_key_name;
    
    /**
     * @var resource $request The request created to submit to the Rest service.
     */
    protected $request;
    
    /**
     * @var mixed $response The response of the submitted request to the Rest service.
     */
    protected $response;
    
    /**
     * @var mixed $parsed_response The parsed response of the submitted request to the Rest service.
     */
    protected $parsed_response;
    
    /**
     * @var boolean $error_checking Indicates if responses are checked for errors.
     */
    protected $error_checking = true;
    
    /**
    * Initializes an instance of Rest.
    *     
    * @return void
    */
    public function __construct() {}
    
    /**
    * Sets the api key for requests that require authentication.
    *     
    * @param string $api_key The api key.
    * @param string $api_key_name The name of the api key defined by the service to specify in the url of GET services.  
    * @return void
    */
    public function setApiKey($api_key, $api_key_name = '') {
        $this->api_key = $api_key;
        
        if(!empty($api_key_name)) {
            $this->api_key_name = $api_key_name;
        }
    }
    
    /**
    * Checks to see if an api key has been set for this authenticated request and throws an exception if false.
    *     
    * @param string $request_method The api method enforcing authentication.
    * @return void
    */
    protected function requireAuthentication($request_method) {
        if(empty($this->api_key)) {
            throw new Exception("'{$request_method}' requires authentication. Please set an api key via setApiKey().");
        }
    }
    
    /**
    * Enables error checking for responses.
    *     
    * @return void
    */
    protected function enableErrorChecking() {
        $this->error_checking = true;
    }
    
    /**
    * Disables error checking for responses.
    *     
    * @return void
    */
    protected function disableErrorChecking() {
        $this->error_checking = false;
    }
    
    /**
    * Indicates if error checking has been enabled.
    *     
    * @return boolean
    */
    protected function hasErrorChecking() {
        return $this->error_checking;
    }
    
    /**
    * Returns the created request.
    *     
    * @return resource The cURL resource of the last request.
    */
    public function getRequest() {
        return $this->request;
    }
    
    /**
    * Returns the response of the submitted request.
    *     
    * @return mixed
    */
    public function getResponse() {
        return $this->response;
    }
    
    /**
    * Returns the parsed response of the submitted request.
    *     
    * @return mixed
    */
    public function getParsedResponse() {
        if(!isset($this->parsed_response)) {
            $this->parsed_response = json_decode($this->response);
        }

        return $this->parsed_response;
    }
    
    /**
    * Retrieves the response code of the last request submitted;
    *     
    * @return integer
    */
    public function getResponseCode($request) {
        return curl_getinfo($request, CURLINFO_HTTP_CODE);
    }
    
    /**
    * Checks a request response for errors.
    *     
    * @return void
    */
    protected function checkForErrors($request, $response) {           
        $http_response_code = $this->getResponseCode($request);
    
        if(empty(static::$http_success_codes[$http_response_code])) {
            $error_message = '';
            $error_description = '';
            
            if(!empty(static::$http_error_codes[$http_response_code])) {                
                if(!empty($response)) {
                    $error_message = $response->error_code;
                    $error_description = $response->message;
                }
                else {
                    $error_message = static::$http_error_codes[$http_response_code]['message'];
                    $error_description = static::$http_error_codes[$http_response_code]['description'];
                }
            
                throw new Exception("Response from the service API returned with an error of response code '{$http_response_code}', message '{$error_message}', and description '{$error_description}'.");
            }
            else {
                $error_code = curl_errno($request);
        
                if(!empty($error_code)) {
                    $error_message = curl_strerror($error_code);
                    
                    throw new Exception("HTTP request to service API encountered an error with number '{$error_code}' and message '{$error_message}'.");
                }
                else {
                    throw new Exception('Response from service API returned with an unknown error.');
                }
            }
        }
    }
    
    /**
    * Generates and returns the initial request header.
    *     
    * @param string $request_type The http request type to make. Can either be 'get', 'post', 'put', or 'delete'.
    * @return array
    */
    protected function generateHeader($request_type) {
        $http_header = array(
            'Content-Type: application/json'
        );
        
        if(!empty($this->api_key)) {
            $http_header[] = "Authorization: {$this->api_key}";
        }
        
        return $http_header;
    }
    
    /**
    * Creates a cURL request to the service API.
    *     
    * @param string $request_type The http request type to make. Can either be 'get', 'post', 'put', or 'delete'.
    * @param string $method_name The name of the method being requested to the service API.
    * @param array $request_parameters (optional) The query string parameters being submitted with the request. Defaults to an empty array.
    * @param string $url_append (optional) Anything that needs to be appended to the request url before request_parameters are. Defaults to an empty string.   
    * @return void
    */
    public function createRequest($request_type, $method_name, array $request_parameters = array(), $url_append = '') {
        $request_url = "{$this->base_url}/{$method_name}";
        
        if(!empty($url_append)) {
            $request_url .= "/{$url_append}/";
        }
        
        $this->request = curl_init();
        
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);
        
        $http_header = $this->generateHeader($request_type);
        
        $request_parameters;

        switch($request_type) {
            case 'get':
                if(!empty($this->api_key_name)) {
                    $request_parameters[$this->api_key_name] = $this->api_key;
                }
                
                $request_url .= '?' . http_build_query($request_parameters);
                break;
            case 'post':
                curl_setopt($this->request, CURLOPT_POST, 1);
                
                $request_body = json_encode($request_parameters);

                curl_setopt($this->request, CURLOPT_POSTFIELDS, $request_body);
                
                $http_header[] = 'Content-Length: ' . strlen($request_body);
                break;
            case 'delete':                
                curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            case 'put':                
                curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            default:
                throw new Exception("Request type '{$request_type}' is invalid. It must be 'get', 'post', 'put', or 'delete'.");
                break;
        }
                
        curl_setopt($this->request, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($this->request, CURLOPT_URL, $request_url);
    }
    
    /**
    * Submits the last request to a Rest service, checks for errors, and returns the parsed response.
    *     
    * @return void
    */
    public function submitRequest() {
        $this->parsed_response = NULL;
    
        $raw_response = curl_exec($this->request);
    
        $this->response = mb_convert_encoding($raw_response, 'UTF-8', 'auto');
        
        if(!empty($this->error_checking)) {
            $this->checkForErrors($this->request, $this->getParsedResponse());
        }
    }
    
    /**
    * Closes the current request.
    *     
    * @return void
    */
    public function closeRequest() {
        curl_close($this->request);
    }
}