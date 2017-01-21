<?php
namespace Framework\Api\Steam;

use \Exception;
use \Framework\Api\Steam\Steam as BaseSteam;

class ISteamRemoteStorage
extends BaseSteam {
    /**
    * Retrieves a UGC file by its id.
    *
    * @param integer $appid appID of product.
    * @param integer $ugcid ID of UGC file to get info for.
    * @param integer $steamid (optional) If specified, only returns details if the file is owned by the SteamID specified. Defaults to NULL for none specified in the request.
    * @return json
    */
    public function getUGCFileDetails($appid, $ugcid, $steamid = NULL) {
        $request_parameters = array(
            'appid' => $appid,
            'ugcid' => $ugcid
        );
        
        if(!empty($steamid)) {
            $request_parameters[$steamid] = $steamid;
        }
    
        return $this->makeRequest('get', "/ISteamRemoteStorage/GetUGCFileDetails/v1", $request_parameters);
    }
}