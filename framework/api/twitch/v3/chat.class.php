<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Chat
extends Twitch {
    /**
    * Returns a links object to all other chat endpoints.
    *
    * @param string $channel The name of the channel.
    * @return json
    */
    public function getLinks($channel) {    
        return $this->makeRequest('get', "/chat/{$channel}");
    }
    
    /**
    * Returns a list of all emoticon objects for Twitch.
    *
    * @return json
    */
    public function getEmoticons() {    
        return $this->makeRequest('get', "/chat/emoticons");
    }
    
    /**
    * Returns a list of all emoticon objects for Twitch.
    *
    * @param string $emotesets (optional) Emotes from a comma separated list of emote sets.
    * @return json
    */
    public function getEmoticonImages($emotesets = NULL) {
        $request_parameters = array();
        
        if(!empty($emotesets)) {
            $request_parameters['emotesets'] = $emotesets;
        }
    
        return $this->makeRequest('get', "/chat/emoticon_images", $request_parameters);
    }
    
    /**
    * Returns a list of chat badges that can be used in the specified channel's chat.
    *
    * @param string $channel The name of the channel.
    * @return json
    */
    public function getChannelBadges($channel) {    
        return $this->makeRequest('get', "/chat/{$channel}/badges");
    }
}