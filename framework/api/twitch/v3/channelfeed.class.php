<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class ChannelFeed
extends Twitch {
    /**
    * Retrieves the feed posts for a specified channel.
    *
    * Authentication is required. Required scope: channel_feed_read
    *
    * @param string $channel The name of the channel.
    * @param integer $limit Maximum number of objects in array. Default is 10. Maximum is 100.
    * @param string $cursor Cursor value to begin next page.
    * @return json
    */
    public function getPosts($channel, $limit = 10, $cursor = NULL) {
        $this->requireAuthentication();
        
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit
        );
        
        if(!empty($cursor)) {
            $request_parameters['cursor'] = $cursor;
        }
    
        return $this->makeRequest('get', "/feed/{$channel}/posts", $request_parameters);
    }
    
    /**
    * Creates a post on a channel's feed.
    *
    * Authentication is required. Required scope: channel_feed_edit
    *
    * @param string $channel The name of the channel.
    * @param string $content Content of the post.
    * @param boolean $share When set to true, shares the post, with a link to the post URL, on the channel's Twitter if it's connected. Defaults to false.
    * @return json
    */
    public function addPost($channel, $content, $share = false) {
        $this->requireAuthentication();
        
        $request_parameters = array(
            'content' => $content,
            'share' => $share
        );
    
        return $this->makeRequest('post', "/feed/{$channel}/posts", $request_parameters);
    }
    
    /**
    * Retrieves a channel feed post.
    *     
    * Authentication is optional. Required scope: channel_feed_read
    *
    * If authentication is provided, the user_ids array in the reaction body contains the requesting user's user_id if they have reacted to the post.
    *
    * @param string $channel The name of the channel.
    * @param integer $id The id of the post.
    *
    * @return json
    */
    public function getPost($channel, $id) {        
        return $this->makeRequest('get', "/feed/{$channel}/posts/{$id}");
    }
    
    /**
    * Deletes a channel feed post.
    *     
    * Authentication is required. Required scope: channel_feed_edit
    *
    * @param string $channel The name of the channel.
    * @param integer $id The id of the post.
    *
    * @return json
    */
    public function deletePost($channel, $id) {        
        return $this->makeRequest('delete', "/feed/{$channel}/posts/{$id}");
    }
    
    /**
    * Creates a reaction to a channel feed post.
    *
    * Authentication is required. Required scope: channel_feed_edit
    *
    * @param string $channel The name of the channel.
    * @param integer $id The id of the post.
    * @param string $emote_id Single emote id (ex: "25" => Kappa) or the string "endorse"
    * @return json
    */
    public function addReaction($channel, $id, $emote_id) {
        $this->requireAuthentication();
        
        $request_parameters = array(
            'emote_id' => $emote_id
        );
    
        return $this->makeRequest('post', "/feed/{$channel}/posts/{$id}/reactions", $request_parameters);
    }
    
    /**
    * Deletes a reaction by the requesting user on the target post.
    *
    * Authentication is required. Required scope: channel_feed_edit
    *
    * @param string $channel The name of the channel.
    * @param integer $id The id of the post.
    * @param string $emote_id Single emote id (ex: "25" => Kappa) or the string "endorse"
    * @return json
    */
    public function deleteReaction($channel, $id, $emote_id) {
        $this->requireAuthentication();
        
        $request_parameters = array(
            'emote_id' => $emote_id
        );
    
        return $this->makeRequest('delete', "/feed/{$channel}/posts/{$id}/reactions", $request_parameters);
    }
}