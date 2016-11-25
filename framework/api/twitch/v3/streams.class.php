<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Streams
extends Twitch {
    /**
    * Returns information on a stream if it's live.
    *
    * @return json
    */
    public function getChannelStreams($channel) {           
        return $this->makeRequest('get', "/streams/{$channel}/");
    }
    
    /**
    * Returns a list of streams based on parameters specified.
    *
    * @param integer $limit Maximum number of objects in array. Default is 100. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @param string $game Streams categorized under the specified game.
    * @param string $channel Streams from a comma separated list of channels.
    * @param string $stream_type Only shows streams from a certain type. Valid values are all, playlist, and live.
    * @param string $language Only shows streams of a certain language. Permitted values are locale ID strings, e.g. `en`, `fi`, `es-mx`.
    * @return json
    */
    public function getStreams($limit = 25, $offset = 0, $game = NULL, $channel = NULL, $stream_type = NULL, $language = NULL) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
        
        if(isset($game)) {
            $request_parameters['game'] = $game;
        }
        
        if(isset($channel)) {
            $request_parameters['channel'] = $channel;
        }
        
        if(isset($stream_type)) {
            switch($stream_type) {
                case 'all':
                case 'playlist':
                case 'live':
                    break;
                default:
                    throw new Exception("Specified stream_type of '{$stream_type}' is invalid. Valid values are all, playlist, and live.");
                    break;
            }
        
            $request_parameters['stream_type'] = $stream_type;
        }
        
        if(isset($language)) {
            $request_parameters['language'] = $language;
        }
    
        return $this->makeRequest('get', "/streams", $request_parameters);
    }    
    
    /**
    * Returns a list of featured (promoted) streams.
    *
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @return json
    */
    public function getFeatured($limit = 25, $offset = 0) {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
    
        return $this->makeRequest('get', "/streams/featured", $request_parameters);
    }
    
    /**
    * Returns a summary of current streams.
    *
    * @param string $game Only show stats for the set game.
    * @return json
    */
    public function getSummary($game = NULL) {               
        $request_parameters = array();
        
        if(isset($game)) {
            $request_parameters['game'] = $game;
        }
    
        return $this->makeRequest('get', "/streams/summary", $request_parameters);
    }
}