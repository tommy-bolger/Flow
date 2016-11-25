<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Users
extends Twitch {
    /**
    * Returns information on a user.
    *
    * @param string $user The user to retrieve information for.
    * @return json
    */
    public function get($user) {           
        return $this->makeRequest('get', "/users/{$user}");
    }
    
    /**
    * Returns a list of emoticons that the currently authenitcated user is authorized to use.
    *
    * Authentication is required. Required scope: user_subscriptions   
    *
    * @param string $user The username.
    * @return json
    */
    public function getAuthenticatedEmotes($user) {           
        return $this->makeRequest('get', "/users/{$user}/emotes");
    }
    
    /**
    * Returns the currently authenticated user's information. If the user's registered Email Address is not verified, null will be returned.
    *
    * Authentication is required. Required scope: user_read   
    *
    * @param string $user The username.
    * @return json
    */
    public function getAuthenticated($user) {           
        return $this->makeRequest('get', "/user");
    }
    
    /**
    * Returns a list of streams that the currently authenticated user is following.
    *
    * Authentication is required. Required scope: user_read   
    *
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @param string $stream_type Only shows streams from a certain type. Valid values are all, playlist, and live.
    * @return json
    */
    public function getFollowed($limit = 25, $offset = 0, $stream_type = NULL) {
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
        
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
    
        return $this->makeRequest('get', "/streams/followed" $request_parameters);
    }
    
    /**
    * Returns a list of videos from channels that the authenticated user is following.
    *
    * Authentication is required. Required scope: user_read   
    *
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param integer $offset The offset for pagination. Defaults to 0.
    * @param string $broadcast_type Only shows videos of a certain type. Valid values are all, archive, and highlight. Default is all.
    * @return json
    */
    public function getFollowedVideos($limit = 25, $offset = 0, $broadcast_type = 'all') {
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
        
        if(isset($broadcast_type)) {
            switch($broadcast_type) {
                case 'all':
                case 'archive':
                case 'broadcast_type':
                    break;
                default:
                    throw new Exception("Specified stream_type of '{$broadcast_type}' is invalid. Valid values are all, archive, and highlight.");
                    break;
            }
        
            $request_parameters['broadcast_type'] = $broadcast_type;
        }
    
        return $this->makeRequest('get', "/videos/followed" $request_parameters);
    }
}