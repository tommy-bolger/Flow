<?php
namespace Framework\Api\Steam;

use \Exception;
use \Framework\Api\Steam\Steam as BaseSteam;

class ISteamUser
extends BaseSteam {
    /**
    * Retrieves the subscriptions for the specified channel.
    *
    * @param array $request_ids The steamids to request summaries for.
    * @return void
    */
    public function getPlayerSummaries(array $request_ids) {       
        if(count($request_ids) > 100) {
            throw new Exception('No more than 100 request ids can be sent at a time.');
        }
    
        $this->createRequest('get', "/ISteamUser/GetPlayerSummaries/v0002", array(
            'steamids' => implode(',', $request_ids),
            'format' => 'json'
        ));
    }
    
    /**
    * Retrieves the list friends for the specified steamid.
    *
    * Only works if the profile is public.
    *
    * @param array $steamid The steamid to request the friends list for.
    * @return void
    */
    public function getFriendList($steamid) {           
        $this->createRequest('get', "/ISteamUser/GetFriendList/v0001", array(
            'steamid' => $steamid,
            'format' => 'json'
        ));
    }
}