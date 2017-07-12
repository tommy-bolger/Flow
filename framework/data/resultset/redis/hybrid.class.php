<?php
/**
* Retrieves a resultset of filtered and sorted data from redis with an optional local cache.
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

namespace Framework\Data\ResultSet\Redis;

use \Exception;
use \Framework\Data\Resultset\SQL as SQLResultset;
use \Framework\Data\Database\QueryGenerator;
use \Framework\Data\Resultset\Resultset;

class Hybrid 
extends ResultSet {
    /**
    * @var object|array Either a single cache object, or an array of cache objects, to retrieve remote data from.
    */
    protected $remote_cache;
    
    /**
    * @var object The cache object to retrieve local data from.
    */
    protected $local_cache;
    
    /**
    * @var boolean Indicates if the base data retrieved needs each row to be decompressed and json decoded.
    */
    protected $decode_records = true;
    
    /**
    * @var string The name of the value stored in $this->remote_cache where the base data is stored in.
    */
    protected $cache_resultset_name;
    
    /**
    * @var SQLResultset The SQL resultset where the base data for this resultset will come from.
    */
    protected $sql_resultset;
    
    /**
    * @var string The name of the field that will be used to retrieved a page of filtered data in $this->sql_resultset.
    */
    protected $page_filter_field;
    
    /**
    * @var string The name of the index to use for filtering.
    */
    protected $index_name = array();
    
    /**
    * @var string The value that data is partitioned by.
    */
    protected $partition_name;
    
    /**
    * @var string The key of the search index to filter by.
    */
    protected $search_index_key;
    
    /**
    * @var string The value to search the search index with.
    */
    protected $search_value;
    
    /**
    * @var array The cache keys to join the filtered dataset with.
    */
    protected $joins = array();
    
    /**
    * @var array The initial, unprocessed dataset after it has been filtered and paginated.
    */
    protected $raw_data = array();
    
    /**
     * Initializes a new instance of a ResultSet.
     *
     * @param string $name The name of the resultset.  
     * @param object|array $remote_cache An instance, or an array of instances, of the cache object to pull remote data from.
     * @param object $local_cache (optional) The instance of the cache object to pull locally stored data from. Defaults to NULL.
     * @return void
     */
    public function __construct($name, $remote_cache, $local_cache = NULL) {
        parent::__construct($name);
        
        $this->remote_cache = $remote_cache;
        
        $this->local_cache = $local_cache;
    }
    
    public function setPageNumber($page_number) {
        throw new Exception('setPageNumber() is not a supported method in this object.');
    }
    
    /**
     * Disables the decompression and json decoding of base data rows.
     *      
     * @return void
     */
    public function disableDecodeRecords() {
        $this->decode_records = false;
    }
    
    /**
     * Sets one or multiple remote cache objects.
     *      
     * @param object|array $remote_cache
     * @return void
     */
    public function setRemoteCache($remote_cache) {
        $this->remote_cache = $remote_cache;
    }
    
    /**
     * Sets the local cache object.
     *      
     * @param object $local_cache
     * @return void
     */
    public function setLocalCache($local_cache) {
        $this->local_cache = $local_cache;
    }
    
    /**
     * Sets the page number of the result set.
     *      
     * @param string $index_key_segment
     * @return void
     */
    public function setIndexName($index_name) {
        $this->index_name = $index_name;
    }
    
    /**
     * Sets the name of the value stored in $this->remote_cache where the base data will come from.
     *      
     * @param string $cache_resultset_name A string for a value stored in $this->remote_cache.
     * @return void
     */
    public function setCacheResultsetName($cache_resultset_name) {
        $this->cache_resultset_name = $cache_resultset_name;
    }
    
    /**
     * Sets the SQL resultset where the base data for this resultset will come from.
     *      
     * @param SQLResultset $sql_resultset The SQL resultset instance where the base data will be retrieved from.
     * @param string $page_filter_field The name of the field that will be used to retrieved a page of filtered data.
     * @return void
     */
    public function setSqlResultset(SQLResultset $sql_resultset, $page_filter_field) {
        $sql_resultset->disableTotalRecordCount();
    
        $this->sql_resultset = $sql_resultset;
        $this->page_filter_field = $page_filter_field;
    }
    
    /**
     * Retrieves the instance of SQLResultset that this instance is using for base data.
     *      
     * @return SQLResultset
     */
    public function getSqlResultset() {
        return $this->sql_resultset;
    }
    
    /**
     * Sets the value that data is partitioned by.
     *      
     * @param string $partition_name
     * @return void
     */
    public function setPartitionName($partition_name) {
        $this->partition_name = $partition_name;
    }
    
    /**
     * Sets the key of the search index.
     *      
     * @param string $search_index_key
     * @return void
     */
    public function setSearch($search_index_key, $search_value) {
        $this->search_index_key = $search_index_key;
        
        $this->search_value = $search_value;
    }
    
    /**
     * Adds a key to join this resultset to.
     *      
     * @param string $join_key The key to join this resultset to.
     * @param string|array $join_field Either the string name of the field or an array of nested keys to the field in each row whose value will be used for the join.
     * @param string|array $destination_field Either the string name of the field or an array of nested keys to the field each row that joined data will be added to.
     * @param integer $join_group_number The group number that this join is contained it. This allows joins to be made on data retrieved from a previous join.
     * @param boolean $decode_record (optional) Indicates if the record needs to be decompressed and json decoded. Defaults to true.
     * @return void
     */
    public function addJoin($join_key, $join_field, $destination_field, $join_group_number = 1, $decode_record = true) {
        $this->joins[$join_group_number][] = array(
            'key' => $join_key,
            'join_field' => $join_field,
            'destination_field' => $destination_field,
            'decode_record' => $decode_record
        );
    }
    
    /**
     * Retrieves the filtered index.
     *      
     * @return array The filtered index.
     */
    protected function getFilteredIndex() {
        if(empty($this->index_name)) {
            throw new Exception('index_name must be set via one or more calls to setIndexName()');
        }
        
        $filtered_index = array();
        
        $remote_cache_key = $this->index_name;
        $local_cache_key = $this->index_name;

        if(!empty($this->partition_name)) {
            $local_cache_key .= ":{$this->partition_name}";
        }
        
        $filtered_index_key = md5("{$local_cache_key}:{$this->search_index_key}:{$this->search_value}");
        
        if(isset($this->local_cache)) {
            $local_filtered_index = $this->local_cache->get($filtered_index_key);
            
            if(!empty($local_filtered_index)) {
                $filtered_index = $local_filtered_index;
            }
            
            unset($local_filtered_index);
        }

        if(empty($filtered_index)) {
            $index_data = array();
            
            if(isset($this->local_cache)) {
                $index_data = $this->local_cache->get($local_cache_key);
            }
            
            if(empty($index_data)) {
                $remote_cache_data  = array();
                
                $remote_cache_data = $this->remote_cache->get($this->partition_name, $remote_cache_key);

                if(!empty($remote_cache_data)) {
                    $index_data = json_decode(gzdecode(base64_decode($remote_cache_data)), true);

                    unset($remote_cache_data);
                
                    if(isset($this->local_cache)) {
                        $this->local_cache->set($local_cache_key, $index_data, NULL, 60);
                    }
                }
            }
            
            if(!empty($index_data)) {            
                if(!empty($this->search_index_key)) {
                    $search_index_data = array();
            
                    if(isset($this->local_cache)) {
                        $search_index_data = $this->local_cache->get($this->search_index_key);
                    }
                    
                    if(empty($search_index_data)) {
                        $remote_cache_data = $this->remote_cache->get($this->search_index_key);
                        
                        if(!empty($remote_cache_data)) {
                            $search_index_data = json_decode(gzdecode(base64_decode($remote_cache_data)), true);
                            
                            unset($remote_cache_data);
                        
                            if(isset($this->local_cache)) {
                                $this->local_cache->set($this->search_index_key, $search_index_data);                        
                            }
                        }
                    }

                    if(!empty($search_index_data)) {
                        foreach($index_data as $index_value) {
                            if(isset($search_index_data[$index_value]) && stripos($search_index_data[$index_value], $this->search_value) !== false) {
                                $filtered_index[] = $index_value;
                            }
                        }
                        unset($search_index_data);
                    }
                }
                else {
                    $filtered_index = $index_data;
                }
            }
            
            if(isset($this->local_cache)) {
                $this->local_cache->set($filtered_index_key, $filtered_index, NULL, 60);
            }
        }
        
        return $filtered_index;
    }
    
    /**
    * Set and/or retrieve a nested array value based on a specified path.
    *
    * This solution is based on one found at: https://stackoverflow.com/a/36042293
    *
    * @param array $array The array to retrieve from or modify.
    * @param array $path The path to the nested value as an array of keys found in $array.
    * @param mixed $value (optional) The value to set. Defaults to NULL for nothing to set.
    *
    * @return mixed The last nested array's value if $value is not specified, or its previous value before being modified if $value is specified.
    */
    protected function getSetByArrayPath(&$array, $path = array(), $value = NULL) {
        /* 
            This call is used to determine if $value is specified at all 
            since it's possible to set array values as NULL.
        */
        $arguments = func_get_args();
        
        $nested_array = &$array;
        
        foreach($path as $key) {
            if(!is_array($nested_array)) {
                $nested_array = array();
            }
            
            $nested_array = &$nested_array[$key];
        }
        
        $final_nested_array = $nested_array;
        
        // If $value is specified set the final nested array to $value
        if(array_key_exists(2, $arguments)) {
            $nested_array = $value;
        }
        
        return $final_nested_array;
    }
    
    /**
     * Merges retrieved join data into the filtered resultset.
     * 
     * @param array $result_data All retrieved join data.
     * @return array
     */
    public function mergeJoinedData($result_data, $join_group_number) {
        if(!empty($result_data)) {
            foreach($result_data as $index => $joined_data) {
                $join = $this->joins[$join_group_number][$index];
                
                $key = $join['key'];
                $join_field = $join['join_field'];
                $desination_field = $join['destination_field'];
                $decode_record = $join['decode_record'];
                
                if(!empty($joined_data)) {
                    foreach($this->raw_data as &$raw_data_row) {
                        $join_value = NULL;
                    
                        if(!is_array($join_field)) {
                            $join_value = $raw_data_row[$join_field];
                        }
                        else {
                            $join_value = $this->getSetByArrayPath($raw_data_row, $join_field);
                        }
                        
                        $joined_row = array();
                        
                        if(isset($joined_data[$join_value])) {
                            $joined_row = $joined_data[$join_value];
                        }
                        
                        $decoded_row = array();
                        
                        if(!empty($joined_row)) {
                            if(!empty($decode_record)) {
                                $decoded_row = json_decode(gzdecode(base64_decode($joined_row)), true);
                            }
                            else {
                                $decoded_row = $joined_row;
                            }
                        }
                        
                        if(!is_array($desination_field)) {
                            $raw_data_row[$desination_field] = $decoded_row;
                        }
                        else {
                            $this->getSetByArrayPath($raw_data_row, $desination_field, $decoded_row);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
        if(empty($this->cache_resultset_name) && empty($this->sql_resultset)) {
            throw new Exception("Either cache_resultset_name must be set via setCacheResultsetName(), or sql_resultset set via setSqlResultset(). Neither were specified.");
        }
    
        if(!isset($this->offset)) {
            $this->offset = 0;
        }
        
        $filtered_index = $this->getFilteredIndex();

        if($this->has_total_record_count) {
            $this->total_number_of_records = count($filtered_index);
        }

        $filtered_index_page = array();

        if(!empty($filtered_index)) {
            $filtered_index_page = array_slice($filtered_index, $this->offset, $this->rows_per_page);
            
            unset($filtered_index);
        }

        if(!empty($filtered_index_page)) {        
            $base_data = array();
            
            if(!empty($this->cache_resultset_name)) {            
                if(!empty($this->partition_name)) {
                    $remote_base_data = $this->remote_cache->get($this->partition_name, $this->cache_resultset_name);
                    
                    if(!empty($remote_base_data)) {
                        $base_data = json_decode(gzdecode(base64_decode($remote_base_data)), true);
                        
                        unset($remote_base_data);
                    }
                }
                else {
                    $remote_base_data = $this->remote_cache->getMultiple($filtered_index_page, $this->cache_resultset_name);

                    if(!empty($remote_base_data)) {
                        foreach($remote_base_data as $index => $remote_base_data_row) {
                            if(!empty($this->decode_records)) {
                                $base_data[$index] = json_decode(gzdecode(base64_decode($remote_base_data_row)), true);
                            }
                            else {
                                $base_data[$index] = $remote_base_data_row;
                            }
                        }
                        
                        unset($remote_base_data);
                    }
                }
                
                if(!empty($base_data)) {
                    foreach($filtered_index_page as $index) {
                        $this->raw_data[] = $base_data[$index];
                    }
                }
            }
            else {                
                $any_values = '{' . implode(',', $filtered_index_page) . '}';
            
                $this->sql_resultset->addFilterCriteria("{$this->page_filter_field} = ANY(?::integer[])", array(
                    $any_values
                ));

                $this->sql_resultset->process();
                
                $base_data = $this->sql_resultset->getData();
                
                if(!empty($base_data)) {
                    $this->raw_data = $base_data;
                }
            }

            unset($base_data);
            
            if(!empty($this->raw_data)) {
                if(!empty($this->joins)) {
                    foreach($this->joins as $join_group_number => $join_group) {
                        $transaction = $this->remote_cache->transaction();
                    
                        $transaction->setCommitProcessCallback(array(
                            $this,
                            'mergeJoinedData'
                        ), array(
                            'join_group_number' => $join_group_number
                        ));
                    
                        foreach($join_group as $join) {
                            $key = $join['key'];
                            $join_field = $join['join_field'];
                            
                            if(!is_array($join_field)) {
                                $join_values = array_column($this->raw_data, $join_field);
                            }
                            else {
                                foreach($this->raw_data as $row) {
                                    $join_values[] = $this->getSetByArrayPath($row, $join_field);
                                }
                            }

                            $transaction->hMGet($key, $join_values);
                        }
                        
                        $transaction->commit();
                    }
                }
            }
        }

        return $this->raw_data;
    }
}