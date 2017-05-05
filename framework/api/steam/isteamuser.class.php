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
    * @return json
    */
    public function getPlayerSummaries(array $request_ids) {       
        if(count($request_ids) > 100) {
            throw new Exception('No more than 100 request ids can be sent at a time.');
        }
    
        return $this->makeRequest('get', "/ISteamUser/GetPlayerSummaries/v0002", array(
            'steamids' => implode(',', $request_ids),
            'format' => 'json'
        ));
    }
}