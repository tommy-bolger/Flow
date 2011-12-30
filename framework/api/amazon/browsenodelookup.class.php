<?php
namespace Framework\API\Amazon;

class BrowseNodeLookup
extends ProductAPI {
    private $response_groups = array();

    public function __construct($browse_node_id, $response_groups = array()) {
        parent::__construct();
        
        $this->result_body_name = 'BrowseNodes';
        
        //Set default parameters
        $this->query_parameters['Operation'] = 'BrowseNodeLookup';
        $this->query_parameters['BrowseNodeId'] = $browse_node_id;
        
        if(!empty($response_groups)) {
            $this->setResponseGroups($response_groups);
        }
    }
    
    public function setResponseGroup($response_group) {
        assert('!empty($response_group)');
        
        $this->response_groups[] = $response_group;
    }
    
    public function setResponseGroups($response_groups) {
        assert('is_array($response_groups) && !empty($response_group)');
        
        $this->response_groups = $response_groups;
    }
    
    protected function transformParameters() {
        if(!empty($this->response_groups)) {
            $this->query_parameters['ResponseGroup'] = implode(',', $this->response_groups);
        }
    }
}