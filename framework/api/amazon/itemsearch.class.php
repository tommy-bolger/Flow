<?php
namespace Framework\API\Amazon;

class ItemSearch
extends ProductAPI {
    public function __construct($region = '', $public_key = '', $private_key = '') {
        parent::__construct($region, $public_key, $private_key);
        
        $this->result_body_name = 'Items';
        
        //Set default parameters
        $this->query_parameters['Operation'] = 'ItemSearch';
    }
}