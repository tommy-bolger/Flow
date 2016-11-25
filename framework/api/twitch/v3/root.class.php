<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Ingests
extends Twitch {
    /**
    * Gets basic information about the API and authentication status. If you are authenticated, the response includes the status of your token and links to other related resources.
    *
    * @return json
    */
    public function getInfo() {           
        return $this->makeRequest('get', "/");
    }
}