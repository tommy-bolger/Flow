<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Channels
extends Twitch {
    /**
    * Retrieves information for a specified channel.
    *
    * @param string $channel The name of the channel.
    * @return json
    */
    public function get($channel) {    
        return $this->makeRequest('get', "/channels/{$channel}");
    }
    
    /**
    * Retrieves information for the currently authenticated user. This includes the stream key.
    *     
    * Authentication is required. Required scope: channel_read
    *
    * @return json
    */
    public function getAuthenticated() {    
        $this->requireAuthentication();
    
        return $this->makeRequest('get', "/channel");
    }
    
    /**
    * Retrieves the users that are editors of the specified channel.
    *     
    * Authentication is required. Required scope: channel_read
    *
    * @param string $channel The name of the channel.
    * @return json
    */
    public function getEditors($channel) {
        $this->requireAuthentication();
    
        return $this->makeRequest('get', "/channels/{$channel}/editors");
    }
    
    /**
    * Updates a channel's properties.
    *     
    * Authentication is required.  Required scope: channel_editor
    *
    * @param string $channel The name of the channel.
    * @param string $status The channel's title.
    * @param string $game Game category to be classified as.
    * @param string $delay Channel delay in seconds. Requires the channel owner's OAuth token.
    * @param boolean $channel_feed_enabled Whether the channel's feed is enabled. Requires the channel owner's OAuth token.
    * @return json
    */
    public function update($channel, $status = NULL, $game = NULL, $delay = NULL, $channel_feed_enabled = NULL) {
        $this->requireAuthentication();
        
        $request_parameters = array();
        
        if(isset($status)) {
            $request_parameters['status'] = $status;
        }
        
        if(isset($game)) {
            $request_parameters['game'] = $game;
        }
        
        if(isset($delay)) {
            $request_parameters['delay'] = $delay;
        }
        
        if(isset($channel_feed_enabled)) {
            $request_parameters['channel_feed_enabled'] = $channel_feed_enabled;
        }
    
        return $this->makeRequest('put', "/channels/{$channel}", $request_parameters);
    }
    
    /**
    * Reset's a channel's stream key.
    *     
    * Authentication is required. Required scope: channel_stream
    *
    * @param string $channel The name of the channel.
    * @return json
    */
    public function resetStreamKey($channel) {
        $this->requireAuthentication();
    
        return $this->makeRequest('delete', "/channels/{$channel}/stream_key");
    }
    
    /**
    * Starts a commercial for a channel.
    *     
    * Authentication is required. Required scope: channel_commercial
    *
    * @param integer $length The length of the channel. Can only be 30, 60, 90, 120, 150, or 180. Defaults to 30.
    * @return json
    */
    public function startCommercial($channel, $length = 30) {
        $this->requireAuthentication();
        
        $request_parameters = array();
        
        switch($length) {
            case 30:
            case 60:
            case 90:
            case 120:
            case 150:
            case 180:
                break;
            default:
                throw new Exception("Specified length '{$length}' is invalid. It can only be 30, 60, 90, 120, 150, or 180.");
                break;
        }
        
        $request_parameters = array(
            'length' => $length
        );
    
        return $this->makeRequest('post', "/channels/{$channel}/commercial", $request_parameters);
    }
}