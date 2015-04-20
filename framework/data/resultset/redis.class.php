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

namespace Framework\Data\ResultSet;

class Redis 
extends Memory {       
    /**
     * Adds a filter criteria to the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.     
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {
        if(!empty($placeholder_values)) {
            throw new \Exception('$placeholder_values is not used by this function for this instance and must be empty.');
        }
        
        $this->filter_criteria[] = $criteria;
    }
 
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
        if($this->has_total_record_count) {
            $this->total_number_of_records = $this->cache->get('total_count', $this->name);
        }
        
        if(empty($this->page_number)) {
            throw new \Exception("Page number is required.");
        }
        
        if(empty($this->rows_per_page)) {
            throw new \Exception("Rows per page is required.");
        }

        $index_page_number = $this->page_number - 1;

        $start_range = $this->rows_per_page * $index_page_number;
        
        $end_range = $start_range + ($this->rows_per_page - 1);

        $keys_to_retrieve = $this->cache->lRange($this->name, $start_range, $end_range);
        
        $raw_data = array();
        
        $transaction = $this->cache->multi();
        
        foreach($keys_to_retrieve as &$key_to_retrieve) {
            $transaction->hGetAll($key_to_retrieve);
        }
        
        $transaction_data = $transaction->exec();
        
        if(!empty($transaction_data)) {
            $raw_data = $transaction_data;
        }

        return $raw_data;
    }
}