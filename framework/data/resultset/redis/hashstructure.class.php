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

class HashStructure 
extends Memory {
    /**
    * @var string The name of the record that contains the names all entries for this resultset.
    */
    protected $entries_name;
    
    /**
    * @var string The name of the record that contains the values used to look entries up for filtering.
    */
    protected $filter_name;
    
    /**
     * Sets the name of the entries record for this resultset.
     * 
     * @param string $entries_name The entries record name.
     * @return void
     */
    public function setEntriesName($entries_name) {
        $this->entries_name = $entries_name;
    }
    
    /**
     * Sets the name of the filter record for this resultset.
     * 
     * @param string $filter_name The filter record name.
     * @return void
     */
    public function setFilterName($filter_name) {
        $this->filter_name = $filter_name;
    }

    /**
     * Adds a filter criteria to the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.     
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {        
        assert('!empty($criteria)');
        assert('empty($this->filter_criteria)');
        assert('empty($placeholder_values) || count($placeholder_values) == 1');
        
        $this->filter_criteria[] = $criteria;
        
        $this->filter_placeholder_values += $placeholder_values;
    }
    
    /**
     * Retrieves keys to retrieve data for based on the filter.
     * 
     * @param string $start_range The record index to begin retrieving from.
     * @param array $end_range The record index to end retrieving from.     
     * @return void
     */
    protected function getFilteredKeys($start_range, $end_range) {
        $iterator = NULL;
        $keys_to_retrieve = array();
        
        $current_offset = 0;
        
        $criteria_value_letters = str_split($this->filter_placeholder_values[0]);
        
        //Create the search pattern string
        $criteria_placeholder_value = '';
        
        foreach($criteria_value_letters as $criteria_value_letter) {
            if(ctype_alpha($criteria_value_letter)) {
                $upper_case_letter = strtoupper($criteria_value_letter);
                $lower_case_letter = strtolower($criteria_value_letter);
                
                $criteria_placeholder_value .= "[{$upper_case_letter}{$lower_case_letter}]";
            }
            else {
                $criteria_placeholder_value .= $criteria_value_letter;
            }
        }

        $criteria = str_replace('?', $criteria_placeholder_value, $this->filter_criteria[0]);
        
        $total_number_of_records = 0;

        //Scan the sorted set containing the filter keys matching the criteria pattern
        $redis_connection_object = $this->cache->getConnectionObject();
        
        while($matches = $redis_connection_object->hScan($this->filter_name, $iterator, $criteria, $this->rows_per_page)) {
            foreach($matches as $search_field => $key) {   
                if($current_offset >= $start_range && $current_offset <= $end_range) {
                    $keys_to_retrieve[] = $key;
                }
            
                $total_number_of_records += 1;
                $current_offset += 1;
            }
        }
        
        $this->total_number_of_records = $total_number_of_records;
        
        //Set the default sorting
        if(!empty($keys_to_retrieve)) {
            $key_order_values = array();
        
            $transaction = $redis_connection_object->multi();
        
            foreach($keys_to_retrieve as &$key_to_retrieve) {
                $transaction->zScore($this->entries_name, $key_to_retrieve);
            }
            
            $key_default_order_values = $transaction->exec();

            if(!empty($key_default_order_values)) {
                $default_sorted_keys = array_combine($keys_to_retrieve, $key_default_order_values);
                
                asort($default_sorted_keys);
                
                $keys_to_retrieve = array_keys($default_sorted_keys);
            }
        }
        
        return $keys_to_retrieve;
    }
 
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
        if($this->has_total_record_count) {
            $this->total_number_of_records = $this->cache->zCard($this->entries_name);
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
        
        $keys_to_retrieve = array();

        if(empty($this->filter_criteria) || empty($this->filter_placeholder_values[0])) {
            $keys_to_retrieve = $this->cache->zRange($this->entries_name, $start_range, $end_range);
        }
        else {
            $keys_to_retrieve = $this->getFilteredKeys($start_range, $end_range);
        }  
        
        $raw_data = array();
        
        $transaction_data = $this->cache->hGetAllMulti($keys_to_retrieve);

        if(!empty($transaction_data)) {
            $raw_data = $transaction_data;
        }

        return $raw_data;
    }
}