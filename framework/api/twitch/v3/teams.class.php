<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Teams
extends Twitch {
    /**
    * Returns a list of active teams.
    *
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param string $offset The offset for pagination. Defaults to 0.
    * @return json
    */
    public function getAll($limit = 25, $offset = 0) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        if(!($direction == 'asc' || $direction == 'desc')) {
            throw new Exception("Specified direction '{$direction}' must be either 'desc' or 'asc'.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
    
        return $this->makeRequest('get', "/teams/", $request_parameters);
    }

    /**
    * Retrieves data for a specified team.
    *
    * @param string $team The team name.
    * @return json.
    */
    public function get($team) {      
        return $this->makeRequest('get', "/teams/{$team}/");
    }
}