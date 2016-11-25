<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Videos
extends Twitch {
    /**
    * Retrieves a video by its id.
    *
    * @param string $id The id of the video.
    * @return json
    */
    public function get($id) {           
        return $this->makeRequest('get', "/videos/{$id}");
    }
    
    /**
    * Returns a list of streams that the currently authenticated user is following.
    *
    * @param integer $limit Maximum number of objects in array. Default is 10. Maximum is 100.
    * @param integer $offset Returns videos only from the specified game.
    * @param string $game Only shows streams from a certain type. 
    * @param string $period Returns only videos created in time period. Valid values are week, month, or all. Default is week.
    * @return json
    */
    public function getMostPopular($limit = 10, $offset = 0, $game = NULL, $period = 'week') {
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
        
        if(isset($period)) {
            switch($period) {
                case 'week':
                case 'month':
                case 'all':
                    break;
                default:
                    throw new Exception("Specified stream_type of '{$period}' is invalid. Valid values are week, month, and all.");
                    break;
            }
        
            $request_parameters['period'] = $period;
        }
    
        return $this->makeRequest('get', "/videos/top" $request_parameters);
    }
    
    /**
    * Returns a list of videos ordered by time of creation, starting with the most recent from the specified channel.
    *
    * @param integer $limit Maximum number of objects in array. Default is 10. Maximum is 100.
    * @param integer $offset Returns videos only from the specified game.
    * @param boolean $broadcasts Returns only broadcasts when true. Otherwise only highlights are returned. Default is false.
    * @param string $hls Returns only HLS VoDs when true. Otherwise only non-HLS VoDs are returned. Default is false.
    * @return json
    */
    public function getChannelVideos($channel, $limit = 10, $offset = 0, $broadcasts = false, $hls = false) {
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
            'broadcasts' => $broadcasts,
            'hls' => $hls
        );
    
        return $this->makeRequest('get', "/channels/{$channel}/videos" $request_parameters);
    }
}