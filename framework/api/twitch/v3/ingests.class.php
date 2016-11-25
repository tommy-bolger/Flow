<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Ingests
extends Twitch {
    /**
    * Retrieves all ingests available.
    *
    * @return json
    */
    public function getAll() {           
        return $this->makeRequest('get', "/games/top");
    }
}