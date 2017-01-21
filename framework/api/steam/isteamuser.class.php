<?php
namespace Framework\Api\Steam;

use \Exception;
use \Framework\Api\Steam\Steam as BaseSteam;

class ISteamUser
extends BaseSteam {
    /**
    * Retrieves the subscriptions for the specified channel.
    *
    * @param string $channel The channel name.
    * @param integer $limit Maximum number of objects in array. Default is 25. Maximum is 100.
    * @param string $offset The offset for pagination. Defaults to 0.
    * @param string $direction The direction to sort results by. Valid values are 'desc' and 'asc'. Default is 'desc'.
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