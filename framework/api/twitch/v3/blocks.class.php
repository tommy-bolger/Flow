<?php
namespace \Framework\Api\Twitch\V3;

use \Exception;

class Blocks
extends Twitch {
    /**
    * Retrieves the list of blocked users for the specified user.
    *     
    * Authentication is required. Required scope: user_blocks_read
    *
    * @param string $user The user to retrieve the block list for.
    * @param integer $limit Maximum number of objects returned. Default is 25. Maximum is 100.
    * @param integer $offset Object offset for pagination. Default is 0.
    * @return json
    */
    public function getAll($user, $limit = 25, $offset = 0) {
        $this->requireAuthentication();
        
        if(!($limit >= 1 && $limit <= 100)) {
            throw new Exception("Specified limit '{$limit}' must be a value between 1 and 100.");
        }
        
        $request_parameters = array(
            'limit' => $limit,
            'offset' => $offset
        );
    
        return $this->makeRequest('get', "/users/{$user}/blocks", $request_parameters);
    }
    
    /**
    * Adds a target to a user's block list.
    *     
    * Authentication is required. Required scope: user_blocks_edit
    *
    * @param string $user The user to add a block for.
    * @param string $target The user to block.
    * @return json
    */
    public function add($user, $target) {
        $this->requireAuthentication();
    
        return $this->makeRequest('put', "/users/{$user}/blocks/{$target}");
    }
    
    /**
    * Delete a target from a user's block list.
    *     
    * Authentication is required. Required scope: user_blocks_edit
    *
    * @param string $user The user to remove a block for.
    * @param string $target The user to unblock.
    * @return void
    */
    public function delete($user, $target) {
        $this->requireAuthentication();
    
        $this->makeRequest('delete', "/users/{$user}/blocks/{$target}");
    }
}