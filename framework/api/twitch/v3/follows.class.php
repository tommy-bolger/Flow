<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Follows
extends Twitch {
    /**
    * Retrieves the followers for a channel.
    *
    * @param string $channel The name of the channel.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param string $cursor Cursor value to begin next page.
    * @param string $direction The direction to sort results by. Valid values are 'desc' and 'asc'. Default is 'desc'.
    * @return json
    */
    public function getFollowers($channel, $limit = 25, $cursor = NULL, $direction = 'desc') {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        if(!($direction == 'asc' || $direction == 'desc')) {
            throw new Exception("Specified direction '{$direction}' must be either 'desc' or 'asc'.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'direction' => $direction
        );
        
        if(!empty($cursor)) {
            $request_parameters['cursor'] = $cursor;
        }
    
        return $this->makeRequest('get', "/channels/{$channel}/follows", $request_parameters);
    }

    /**
    * Retrieves the channels that a user follows.
    *
    * @param string $user The username to retrieve follows for.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param string $offset The offset for pagination. Defaults to 0.
    * @param string $direction The direction to sort results by. Valid values are 'desc' and 'asc'. Default is 'desc'.
    * @param string $sortby The key to sort by. Valid values are 'created_at', 'last_broadcast', and 'login'.
    * @return json
    */
    public function getFollowedChannels($user, $limit = 25, $offset = 0, $direction = 'desc', $sortby = 'created_at') {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        if(!($direction == 'asc' || $direction == 'desc')) {
            throw new Exception("Specified direction '{$direction}' must be either 'desc' or 'asc'.");
        }
        
        switch($sortby) {
            case 'created_at':
            case 'last_broadcast':
            case 'login':
                break;
            default:
                throw new Exception("Specified sortby '{$sortby}' is invalid. Valid values are 'created_at', 'last_broadcast', and 'login'.");
                break;
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset,
            'direction' => $direction,
            'sortby' => $sortby
        );
    
        return $this->makeRequest('get', "/users/{$user}/follows/channels", $request_parameters);
    }
    
    /**
    * Checks if a user follows a channel.
    *
    * @param string $user The username.
    * @param string $target The target channel.
    * @return boolean True for yes, false for no.
    */
    public function userFollowsChannel($user, $target) {  
        $this->disableErrorChecking();
    
        $response = $this->makeRequest('delete', "/users/{$user}/follows/channels/{$target}");
        
        $this->enableErrorChecking();
        
        $user_follows_channel = false;
        
        if(!empty($response)) {
            $user_follows_channel = true;
        }
        else {
            $response_code = $this->getResponseCode($this->last_request);
            
            if($response_code != 401) {
                $this->checkForErrors($this->last_request, $this->last_response);
            }
        }
        
        return $user_follows_channel;
    }
    
    /**
    * Follows a channel for the currently authenticated user.
    *     
    * Authentication is required. Required scope: user_follows_edit
    *
    * @param string $user The user that's following.
    * @param string $target The channel to follow.
    * @param boolean $notifications Toggles whether the following user should receive push and email notifications.
    * @return json
    */
    public function followChannel($user, $target, $notifications = false) {
        $this->requireAuthentication();
        
        $request_parameters = array(
            'notifications' => $notifications
        );
    
        return $this->makeRequest('put', "/users/{$user}/follows/channels/{$target}", $request_parameters);
    }
    
    /**
    * Unfollows a channel for the currently authenticated user.
    *     
    * Authentication is required. Required scope: user_follows_edit
    *
    * @param string $user The user that's unfollowing.
    * @param string $target The channel to unfollow.
    * @return json
    */
    public function unfollowChannel($user, $target) {
        $this->requireAuthentication();
    
        $this->makeRequest('delete', "/users/{$user}/follows/channels/{$target}");
    }
}