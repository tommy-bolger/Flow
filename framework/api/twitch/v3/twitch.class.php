<?php
namespace \Framework\Api\Twitch\V3;

use \Framework\Api\Rest;

class Twitch
extends Rest {
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
     * @var string $client_id The client id to send with all requests.
     */
    protected $client_id;
    
    /**
    * Initializes an instance of Rest.
    *     
    * @return void
    */
    public function __construct($client_id) {
        $this->base_url = 'https://api.twitch.tv/kraken';
    
        $this->client_id = $client_id;
    }
    
    /**
    * Generates and returns the initial request header.
    *     
    * @param string $request_type The http request type to make. Can either be 'get', 'post', 'put', or 'delete'.
    * @return array
    */
    protected function generateHeader($request_type) {
        $http_header = parent::generateHeader($request_type);
        
        $http_header[] = "Accept: application/vnd.twitchtv.v3+json";
        $http_header[] = "Client-ID: {$this->client_id}";
        
        if(!empty($this->api_key)) {
            $http_header[] = "Authorization: OAuth {$this->api_key}";
        }
        
        return $http_header;
    }
}