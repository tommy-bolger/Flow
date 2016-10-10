<?php
/**
* Configures, retrieves, and stores data from a result set based on cached data in redis.
* Copyright (c) 2015, Tommy Bolger
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

namespace Framework\Data\ResultSet\Redis;

use \Framework\Data\ResultSet\Memory;

class ListStructure 
extends HashStructure {    
    /**
    * @var callback The callback to the function that generates an entry name.
    */
    protected $entry_name_callback;
    
    /**
    * @var string The name of the entry that contains the field names.
    */
    protected $entry_properties_name;
    
    /**
    * @var array The arguments to pass into the callback function specified by entry_name_callback.
    */
    protected $entry_name_callback_arguments = array();
    
    /**
     * Sets the callback function used to generate an entry name.
     * 
     * @param callback $callback The callback to call.
     * @param array $arguments The fixed arguments to pass into the callback function. 
     * @return void
     */
    public function setEntryNameCallback($callback, array $arguments = array()) {
        $this->entry_name_callback = $callback;
        
        $this->entry_name_callback_arguments = $arguments;
    }
    
    /**
     * Sets the entry properties name.
     * 
     * @param string $entry_properties_name The entry properties name.
     * @return void
     */
    public function setEntryPropertiesName($entry_properties_name) {
        $this->entry_properties_name = $entry_properties_name;
    }
 
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {        
        $this->total_number_of_records = $this->cache->get($this->entries_name);
        
        if(empty($this->page_number)) {
            throw new \Exception("Page number is required.");
        }
        
        if(empty($this->rows_per_page)) {
            throw new \Exception("Rows per page is required.");
        }

        $start_number = 1;
        
        if($this->page_number > 1) {
            $start_number = $this->rows_per_page * $this->page_number;
        }
        
        $end_number = $start_number + $this->rows_per_page;
        
        $keys_to_retrieve = array();

        if(empty($this->filter_criteria) || empty($this->filter_placeholder_values[0])) {
            for($entry_number = $start_number; $entry_number <= $end_number; $entry_number++) {
                $arguments = $this->entry_name_callback_arguments;
                
                $arguments[] = $entry_number;
            
                $keys_to_retrieve[] = call_user_func_array($this->entry_name_callback, $arguments);
            }
        }
        else {
            $keys_to_retrieve = $this->getFilteredKeys($start_range, $end_range);
        }  
        
        $raw_data = array();
        
        $transaction_data = $this->cache->lRangeMulti($keys_to_retrieve);

        if(!empty($transaction_data)) {
            $raw_data = $transaction_data;
        }
        
        if(!empty($this->entry_properties_name)) {
            $entry_properties = $this->cache->lRange($this->entry_properties_name, 0, -1);
            
            $combined_data = array();
            
            if(!empty($entry_properties)) {
                foreach($raw_data as $data_row) {
                    if(!empty($data_row)) {
                        $combined_data[] = array_combine($entry_properties, $data_row);
                    }
                }
            }
            
            $raw_data = $combined_data;
        }

        return $raw_data;
    }
}