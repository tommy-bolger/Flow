<?php
/**
* A queue used to submit several cURL requests from the rest api in parallel asynchronously.
*
* Copyright (c) 2017, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/

namespace Framework\Api;

use \Framework\Utilities\RecordQueue;
use \Framework\Api\Rest;

class AsyncMultiRestQueue
extends RecordQueue {
    /**
    * Made into a dummy method in favor of addRequest().
    *     
    * @return void
    */
    public function addRecord($record) {}
    
    /**
    * Made into a dummy method in favor of addRequests().
    *     
    * @return void
    */
    public function addRecords(array &$records) {}

    /**
    * Adds a cURL request to the queue.
    *     
    * @param resource $request The cURL request to add to the queue.
    * @return void
    */
    public function addRequest($request) {
        parent::addRecord($request);
    }
    
    /**
    * Adds one or more cURL requests to the queue.
    *     
    * @param array $requests The cURL requests to add to the queue.
    * @return void
    */
    public function addRequests(array &$requests) {
        parent::addRecords($requests);
    }

    /**
    * Submits the stored requests, retrieves their responses, and executes the set callback with those responses.
    *     
    * @return void
    */
    public function commit() {
        if(empty($this->commit_callback)) {
            throw new Exception("Commit callback has not been set and is required.");
        }
    
        if(!empty($this->records)) {
            $multi_request = curl_multi_init();
        
            foreach($this->records as $request) {
                curl_multi_add_handle($multi_request, $request);
            }          
            
            $active = 0;

            do {
                $multi_request_response_code = curl_multi_exec($multi_request, $active);
            } 
            while($multi_request_response_code == CURLM_CALL_MULTI_PERFORM);

            while($active > 0 && $multi_request_response_code == CURLM_OK) {
                if(curl_multi_select($multi_request) == -1) {
                    usleep(1);
                }
                
                do {
                    $multi_request_response_code = curl_multi_exec($multi_request, $active);
                } 
                while($multi_request_response_code == CURLM_CALL_MULTI_PERFORM);
            }
            
            foreach($this->records as $request) {
                $response = curl_multi_getcontent($request);
                
                $responses[] = $response;
                
                curl_multi_remove_handle($multi_request, $request);
            }
            
            curl_multi_close($multi_request);
            
            $this->records = array();
            
            $commit_callback_arguments = $this->commit_callback_arguments;
        
            array_unshift($commit_callback_arguments, $responses);
        
            call_user_func_array($this->commit_callback, $commit_callback_arguments);
        }
    }
}