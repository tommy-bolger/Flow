<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Search
extends Twitch {
    /**
    * Returns a list of channels matching the search query.
    *
    * @param string $query The search query.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @return json
    */
    public function queryChannels($query, $limit = 25, $offset = 0) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset
        );
    
        return $this->makeRequest('get', "/search/channels", $request_parameters);
    }
    
    /**
    * Returns a list of streams matching the search query.
    *
    * @param string $query The search query.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @param boolean (optional) $hls If set to true, only returns streams using HLS. If set to false, only returns streams that are non-HLS.
    * @return json
    */
    public function queryStreams($query, $limit = 25, $offset = 0, $hls = NULL) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset
        );
        
        if(isset($hls)) {
            $request_parameters['hls'] = $hls;
        }
    
        return $this->makeRequest('get', "/search/streams", $request_parameters);
    }
    
    /**
    * Returns a list of games matching the search query.
    *
    * @param string $query The search query.
    * @param boolean (optional) $live If true, only returns games that are live on at least one channel.
    * @return json
    */
    public function queryGames($query, $live = NULL) {               
        $request_parameters = array(
            'query' => $query,
            'type' => 'suggest'
        );
        
        if(isset($live)) {
            $request_parameters['live'] = $live;
        }
    
        return $this->makeRequest('get', "/search/games", $request_parameters);
    }
}