<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Games
extends Twitch {
    /**
    * Retrieves the top games being played.
    *
    * @param integer $limit Maximum number of objects in array. Default is 100. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @return json
    */
    public function getTop($limit = 25, $offset = 0) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
    
        return $this->makeRequest('get', "/games/top", $request_parameters);
    }
}