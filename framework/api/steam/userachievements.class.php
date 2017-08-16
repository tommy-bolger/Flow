<?php
namespace Framework\Api\Steam;

use \Exception;
use \SimpleXMLElement;
use \Framework\Data\XMLWrite;
use \Framework\Api\Steam\Steam as BaseSteam;

class UserAchievements
extends BaseSteam {
    /**
    * Indicates if the response xml is valid.
    *     
    * @return boolean
    */
    public static function responseIsValid($response) {
        $response_is_valid = false;
        
        if(strpos($response, 'steamID64') !== false) {
            $response_is_valid = true;
        }
        
        return $response_is_valid;
    }
    
    /**
    * Returns the object structure of an XML string
    *     
    * @param string $unparsed_xml The unparsed XML string.
    * @return object
    */
    public static function getParsedXml($unparsed_xml) {
        $parsed_xml = NULL;
    
        if(!empty($unparsed_xml)) {
            $parsed_xml = XMLWrite::convertXmlToObject(new SimpleXMLElement($unparsed_xml));
        }
        
        return $parsed_xml;
    }

    /**
    * Initializes an instance of this API client.
    *     
    * @return void
    */
    public function __construct() {
        parent::__construct();
    
        $this->base_url = 'http://steamcommunity.com';
    }
    
    /**
    * Returns the parsed response of the submitted request.
    *     
    * @return mixed
    */
    public function getParsedResponse() {
        if(!isset($this->parsed_response)) {
            if(static::responseIsValid($this->response)) {            
                $this->parsed_response = static::getParsedXml($this->response);
            }
        }

        return $this->parsed_response;
    }

    /**
    * Retrieves a user's stats for a specified application.
    *
    * @param integer $steamid The Steam ID of the user.
    * @param integer $app_id The ID of the application.
    * @return json
    */
    public function getUserAchievements($steamid, $app_id) {
        $this->disableErrorChecking();
    
        $this->createRequest('get', "/profiles/{$steamid}/stats/{$app_id}/achievements", array(
            'xml' => 1
        ));
    }
}