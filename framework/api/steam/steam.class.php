<?php
namespace Framework\Api\Steam;

use \Exception;
use \Framework\Api\Rest;

class Steam
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
    * Initializes an instance of Rest.
    *     
    * @return void
    */
    public function __construct() {
        $this->base_url = 'http://api.steampowered.com';
    }
}