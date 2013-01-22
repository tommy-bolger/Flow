<?php
/**
* Allows the rendering and validation of a captcha field utilizing reCaptcha.
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

namespace Framework\Html\Form\Fields;

use \Framework\Core\Framework;
use \Framework\Html\Form\FieldObjects\Field;

class Captcha
extends Field {
    /**
    * @var string The name of the javascript object of this field.
    */
    protected $javascript_object_name = 'Captcha';

    /**
    * @const string The host name of the reCaptcha API url.
    */
    const API_HOST = "www.google.com";
    
    /**
    * @const string The url path of the question request reCaptcha API url.
    */
    const API_PATH = "/recaptcha/api";        
    
    /**
    * @var string The url path of the answer verify reCaptcha API url.
    */
    const API_VERIFY_PATH = "/recaptcha/api/verify";        
    
    /**
    * @var string The full url of the question request reCaptcha API/
    */
    private $api_url;
    
    /**
    * @var string The reCaptcha API private key.
    */
    private $api_private_key;
    
    /**
    * @var string The reCaptcha API public key.
    */
    private $api_public_key;
    
    /**
    * @var string The reCaptcha response error code.
    */
    private $api_error_code;
    
    /**
    * @var object The field storing the recaptcha_challenge_field value.
    */
    private $challenge_field;

    /**
    * @var object The hidden field storing the recaptcha_response_field value.
    */
    private $response_field;

    /**
     * Initializes a new instance of Captcha.
     *
     * @param string $textarea_label (optional) The label for the textarea.     
     * @return void
     */
    public function __construct($field_label = '') {
        parent::__construct(NULL, "recaptcha", $field_label);
        
        $framework = Framework::getInstance();
        
        $this->api_url = $framework->configuration->recaptcha_api_method . '://' . self::API_HOST . self::API_PATH;
        $this->api_private_key = $framework->configuration->recaptcha_private_key;
        $this->api_public_key = $framework->configuration->recaptcha_public_key;
        
        if(empty($this->api_private_key)) {
            throw new \Exception("To use the captcha field a public and private reCAPTCHA API key from https://www.google.com/recaptcha/admin/create must be set in the configuration.");
        }
        
        $this->challenge_field = new Textarea("recaptcha_challenge_field");
        $this->challenge_field->setWidth(40);
        $this->challenge_field->setHeight(3);
        
        $this->response_field = new Hidden('recaptcha_response_field', 'manual_challenge');
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
            
        $this->addCssFile('framework/Captcha.css');
                
        $this->addJavascriptFile('form/fields/Captcha.js');        
        $this->addInlineJavascript("
            var RecaptchaOptions = {
                theme : 'clean'
            };
        ");
    }
    
    /**
     * Sets the width (cols) of the textarea.
     *      
     * @param integer $width The width of the textarea.
     * @return void
     */
    public function setWidth($width) {}
    
    /**
     * Sets the height (rows) of the textarea.
     *      
     * @param integer $height The height of the textarea.
     * @return void
     */
    public function setHeight($height) {}
    
    /**
     * Sets the submitted field value.
     *      
     * @param mixed $field_value The submitted value.
     * @return void
     */
    public function setValue($field_value) {
        $this->challenge_field->setValue(request()->post->recaptcha_challenge_field);
        
        $this->response_field->setValue(request()->post->recaptcha_response_field);
    }
    
    /**
     * Submits an answer to reCaptcha and returns its result.
     *      
     * @return array The answer from reCaptcha. Included elements are is_valid (boolean) and error (string).
     */
    private function submitAnswer() {
        $submit_data = array(
            'privatekey' => $this->api_private_key,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
            'challenge' => $this->challenge_field->getValue(),
            'response' => $this->response_field->getValue()
        );      
            
        $curl = curl_init("http://" . self::API_HOST . ':80' . self::API_VERIFY_PATH);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'reCAPTCHA/PHP');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $submit_data);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Host: " . self::API_HOST));

        $response = curl_exec($curl);
        
        if(empty($response)) {
            throw new \Exception("Connection to reCaptcha verify api has failed.");
        }

        $response_split = explode("\r\n\r\n", $response, 2);
        
        $answer = explode("\n", $response_split[1]);
        
        $complete_answer = array(
            'is_valid' => filter_var(trim($answer[0]), FILTER_VALIDATE_BOOLEAN),
            'error' => ''
        );
        
        if(!empty($answer[1])) {
            $complete_answer['error'] = $answer[1];
        }

        return $complete_answer; 
    }
    
    /**
     * Validates the field's submitted value.
     *      
     * @return boolean
     */
    protected function validate() {        
        $error_message = "The value of {$this->label} didn't match the picture.";
        
        $response_value = $this->response_field->getValue();
        $challenge_value = $this->challenge_field->getValue();
        
        if(empty($challenge_value) || empty($response_value)) {
            $this->api_error_code = 'incorrect-captcha-sol';
            
            $this->setErrorMessage($error_message);
            
            return false;                                
        }
        
        $answer = $this->submitAnswer();
        
        if(!$answer['is_valid']) {
            $this->api_error_code = $answer['error'];
            
            $this->setErrorMessage($error_message);
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Renders and retrieves the field's html.
     *      
     * @return string
     */
    public function getFieldHtml() {
        $error_string = '';
        
        if(!empty($this->api_error_code)) {
            $error_string = "&error={$this->api_error_code}";        
        }                        
            
        return "
            <input type=\"hidden\"{$this->renderAttributes()} />
            <script type=\"text/javascript\" src=\"{$this->api_url}/challenge?k={$this->api_public_key}{$error_string}\"></script>
            <noscript>
          		<iframe src=\"{$this->api_url}/noscript?k={$this->api_public_key}{$error_string}\" height=\"300\" width=\"500\" frameborder=\"0\"></iframe><br/>
          		{$this->challenge_field->getFieldHtml()}
          		{$this->response_field->getFieldHtml()}
        	</noscript>
        ";
    }
}