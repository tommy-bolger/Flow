<?php
namespace Framework\Api\Steam;

use \Exception;
use \Framework\Api\Steam\Steam as BaseSteam;

class ISteamUserStats
extends BaseSteam {
    /**
    * Retrieves a user's stats for a specified application.
    *
    * @param integer $steamid The Steam ID of the user.
    * @param integer $app_id The ID of the application.
    * @return json
    */
    public function getUserStatsForGame($steamid, $app_id) {           
        return $this->makeRequest('get', "/ISteamUserStats/GetUserStatsForGame/v0002", array(
            'steamid' => $steamid,
            'appid' => $app_id,
            'format' => 'json'
        ));
    }
    
    /**
    * Retrieves a game's schema given its application ID.
    *
    * @param integer $app_id The ID of the application.
    * @return json
    */
    public function getSchemaForGame($app_id) {
        return $this->makeRequest('get', "/ISteamUserStats/GetSchemaForGame/v2", array(
            'appid' => $app_id,
            'format' => 'json'
        ));
    }
}