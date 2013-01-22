<?php
namespace Framework\API\Amazon;

class ProductAPI {
    private $region;

    private $public_key;
    
    private $private_key;
    
    protected $query_parameters = array();
    
    protected $result_body_name;
    
    public function __construct($region = '', $public_key = '', $private_key = '') {
        $framework = Framework::getInstance();
    
        if(empty($region)) {
            $region = $framework->configuration->aws_region;
        }
    
        switch($region) {
            case 'ca':
            case 'fr':
            case 'de':
            case 'jp':
            case 'co.uk':
            case 'com':
                $this->region = $region;
                break;
            default:
                throw new \Exception("Region '{$region}' is not a valid amazon region.");
                break;
        }
        
        if(empty($public_key)) {
            $public_key = $framework->configuration->aws_public_key;
        }
        
        $this->public_key = $public_key;
        
        if(empty($private_key)) {
            $private_key = $framework->configuration->aws_private_key;
        }
        
        $this->private_key = $private_key;
        
        $this->query_parameters["Service"]  = "AWSECommerceService";
        $this->query_parameters["AWSAccessKeyId"] = $public_key;
        $this->query_parameters["Version"] = "2010-11-01";
    }
    
    public function setResultBodyName($result_body_name) {
        $this->result_body_name = $result_body_name;
    }
    
    public function __get($parameter_name) {
        if(isset($this->query_parameters[$parameter_name])) {
            return $this->query_parameters[$parameter_name];
        }
        
        return "";
    }
    
    public function __isset($parameter_name) {
        return isset($this->query_parameters[$parameter_name]);
    }
    
    public function __set($parameter_name, $parameter_value) {
        $this->query_parameters[$parameter_name] = $parameter_value;
    }
    
    protected function transformParameters() {}
    
    private function getSignedRequest() {        
        $method = "GET";
        $host = "ecs.amazonaws.{$this->region}";
        $uri = "/onca/xml";
     
        //Sort the parameters alphabetically since Amazon does the same for authentication
        ksort($this->query_parameters);
     
        $canonicalized_query = http_build_query($this->query_parameters);

        $string_to_sign = "{$method}\n{$host}\n{$uri}\n{$canonicalized_query}";
     
        /* calculate the signature using HMAC, SHA256 and base64-encoding */
        $signature = urlencode(base64_encode(hash_hmac("sha256", $string_to_sign, $this->private_key, true)));

        /* create request */
        return "http://{$host}{$uri}?{$canonicalized_query}&Signature={$signature}";
    }
    
    public function execute() {
        $this->transformParameters();
    
        $this->query_parameters["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
    
        $request = $this->getSignedRequest();

        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_URL, $request);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, 0);
     
        $xml_response = curl_exec($curl_request);
        
        if(empty($xml_response)) {
            throw new \Exception("Could not connect to Amazon.");
        }
        
        if(strpos($xml_response, 'AWS.InvalidOperationParameter') !== false) {
            throw new \Exception($xml_response);
        }
        
        $parsed_response = new SimpleXMLElement($xml_response);
        
        if(!empty($parsed_response->Error)) {
            $response_errors = "The submitted request encountered the following errors:\n";
        
            foreach($parsed_response->Error as $response_error) {
                $response_errors .= "Code: {$response_error->Code}, Message: {$response_error->Message}\n";
            }
            
            throw new \Exception($response_errors);
        }
        
        $result_body_name = $this->result_body_name;
        
        if(!empty($parsed_response->$result_body_name->Request->Errors)) {
            $response_errors = "The submitted request encountered the following errors:\n";
        
            foreach($parsed_response->$result_body_name->Request->Errors->Error as $response_error) {
                $response_errors .= "Code: {$response_error->Code}, Message: {$response_error->Message}\n";
            }
            
            throw new \Exception($response_errors);
        }
        
        return $parsed_response;
    }
}