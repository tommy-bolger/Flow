<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Subscriptions
extends Twitch {
    /**
    * Retrieves the subscriptions for the specified channel.
    *
    * Authentication is required. Required scope: channel_subscriptions
    *
    * @param string $channel The channel name.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param string $offset The offset for pagination. Defaults to 0.
    * @param string $direction The direction to sort results by. Valid values are 'desc' and 'asc'. Default is 'desc'.
    * @return json
    */
    public function getChannelSubscriptions($channel, $limit = 25, $offset = 0, $direction = 'desc') {       
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        if(!($direction == 'asc' || $direction == 'desc')) {
            throw new Exception("Specified direction '{$direction}' must be either 'desc' or 'asc'.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset,
            'direction' => $direction
        );
    
        return $this->makeRequest('get', "/channels/{$channel}/subscriptions", $request_parameters);
    }

    /**
    * Returns a subscription object which includes the user if that user is subscribed. Requires authentication for the channel.
    *
    * Authentication is required. Required scope: channel_check_subscription
    *
    * @param string $channel The channel name.
    * @param string $user The username.
    * @return json.
    */
    public function userFollowsChannel($channel, $user) {      
        return $this->makeRequest('get', "/channels/{$channel}/subscriptions/{$user}");
    }

    /**
    * Returns a channel object that user subscribes to. Requires authentication for the specified user.
    *
    * Authentication is required. Required scope: user_subscriptions
    *
    * @param string $user The username.
    * @param string $channel The channel name.
    * @return json.
    */
    public function userFollowsChannel($channel, $user) {      
        return $this->makeRequest('get', "/users/{$user}/subscriptions/{$channel}");
    }
}