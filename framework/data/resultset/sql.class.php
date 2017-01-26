<?php
/**
* Configures, retrieves, and stores data from a result set based on a SQL query.
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

namespace Framework\Data\ResultSet;

use \Exception;

class SQL 
extends ResultSet {
    /**
    * @var string The query for the base SQL dataset.
    */
    protected $base_query;
    
    /**
    * @var array The placeholder values for the base SQL dataset.
    */
    protected $base_query_placeholders;
    
    /**
    * @var array The fields to retrieve in the SQL dataset.
    */
    protected $select_fields = array();
    
    /**
    * @var array The tables used in a unioned query across partitioned tables.
    */
    protected $partition_table_names = array();
    
    /**
     * Catches all get function calls not present in this class and passes them to the database abstraction object using this resultset's finalized query.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments This is a dummy parameter that is ignored.
     * @return mixed
     */
    public function __call($function_name, $arguments = array()) {
        $return_value = NULL;
    
        if(strpos($function_name, 'get') !== false || strpos($function_name, 'prepareExecuteQuery') !== false) {
            $finalized_query = $this->getFinalizedQuery();
            
            unset($finalized_query['non_sort_limit_query']);
        
            $return_value = call_user_func_array(array(db(), $function_name), $finalized_query);
        }
        else {
            throw new Exception("Method '{$function_name}' does not exist in this object");
        }
        
        return $return_value;
    }
    
    /**
     * Adds a select field to retrieve to the SQL query.
     * 
     * @param string $field The field to add.     
     * @param string $alias (optional) The alias of the field.  
     * @return void
     */
    public function addSelectField($field, $alias = NULL) {
        if(empty($alias)) {
            $alias = $field;
        }
    
        $this->select_fields[$alias] = $field;
    }
    
    /**
     * Adds several select fields to retrieve to the SQL query.
     * 
     * @param string $field The field to add.     
     * @param string $alias (optional) The alias of the field.  
     * @return void
     */
    public function addSelectFields(array $fields) {
        if(!empty($fields)) {
            foreach($fields as $field) {
                if(!isset($field['field'])) {
                    throw new Exception("field must be an array key for all elements when adding select fields.");
                }
                
                $alias = NULL;
                
                if(isset($field['alias'])) {
                    $alias = $field['alias'];
                }
                
                $this->addSelectField($field['field'], $alias);
            }
        }
    }
    
    /**
     * Adds a select field to retrieve to the SQL query that overrides any existing select fields of the result set.
     * 
     * @param string $field The field to add.     
     * @param string $alias (optional) The alias of the field.  
     * @return void
     */
    public function setSelectField($field, $alias = NULL) {
        $this->select_fields = array();
    
        $this->addSelectField($field, $alias);
    }
    
    /**
     * Adds several select fields to retrieve to the SQL query that overrides any existing select fields of the result set.
     * 
     * @param string $field The field to add.     
     * @param string $alias (optional) The alias of the field.  
     * @return void
     */
    public function setSelectFields(array $fields) {
        $this->select_fields = array();
    
        $this->addSelectFields($fields);
    }
    
    /**
     * Adds a partition table name that changes for each query in a unioned query.
     * 
     * @param string $partition_table_name The name of the partitioned table.     
     * @return void
     */
    public function addPartitionTable($partition_table_name) {        
        $this->partition_table_names[] = $partition_table_name;
    }
    
    /**
     * Adds a filter criteria to the result set.
     * 
     * @param array $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.     
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {
        assert('!empty($criteria)');
        
        $this->filter_criteria[] = $criteria;
        
        $this->filter_placeholder_values = array_merge($this->filter_placeholder_values, $placeholder_values);
    }

    /**
     * Adds a sort column criteria to the existing criteria of the result set.
     * 
     * @param string|array $criteria The criteria to sort the result set by Can either be a single column name as a string or several in an array.     
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function addSortCriteria($criteria, $direction = 'ASC') {
        assert('!empty($criteria)');
        
        $direction = strtoupper($direction);
    
        switch($direction) {
            case 'ASC':
            case 'DESC':
                break;
            default:
                $direction = 'ASC';
                break;
        }
        
        $this->sort_criteria[] = array(
            'criteria' => $criteria,
            'direction' => $direction
        );
    }
    
    /**
     * Adds a column sort criteria based on its alias, and checks to make sure that the select field associated with it exists.
     * 
     * @param string $alias The select field alias.
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC.
     * @return void
     */
    public function addSortCriteriaFromAlias($alias, $direction = 'ASC') {
        if(empty($this->select_fields)) {
            throw new Exception("Select fields must be added to set sort criteria by alias.");
        }
        
        if(!isset($this->select_fields[$alias])) {
            $aliases = "'" . implode("', '", array_keys($this->select_fields)) . "'";
        
            throw new Exception("Alias '{$alias}' is invalid. Valid aliases are {$aliases}.");
        }
        
        $this->addSortCriteria($this->select_fields[$alias], $direction);
    }
    
    
    /**
     * Sets sort column criteria that overrides any existing criteria of the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.     
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function setSortCriteria($criteria, $direction = 'ASC') {        
        $this->sort_criteria = array();
        
        $this->addSortCriteria($criteria, $direction);
    }
    
    /**
     * Sets column sort criteria based on its alias that overrides any existing criteria of the result set.
     * 
     * @param string $alias The select field alias.
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC.
     * @return void
     */
    public function setSortCriteriaFromAlias($alias, $direction = 'ASC') {
        $this->sort_criteria = array();
        
        $this->addSortCriteriaFromAlias($alias, $direction);
    }
    
    /**
     * Retrieves the finalized query and its placeholder values.
     * 
     * @return array.
     */
    public function getFinalizedQuery() {
        $query = $this->base_query;
        $query_placeholders = $this->base_query_placeholders;

        if(isset($this->filter_criteria)) {
            $where_criteria = implode(' AND ', $this->filter_criteria);

            if(strpos($query, '{{WHERE_CRITERIA}}') !== false) {
                if(!empty($where_criteria)) {
                    $where_criteria = "WHERE {$where_criteria}";
                }
            
                $query = str_replace('{{WHERE_CRITERIA}}', $where_criteria, $query);
            }
            elseif(strpos($query, '{{AND_CRITERIA}}') !== false) {
                if(!empty($where_criteria)) {
                    $where_criteria = "WHERE {$where_criteria}";
                }
            
                $query = str_replace('{{AND_CRITERIA}}', $where_criteria, $query);
            }
            
            $query_placeholders += $this->filter_placeholder_values;
        }
        
        if(isset($this->select_fields)) {
            $select_fields = array();
            
            foreach($this->select_fields as $alias => $field) {
                $select_fields[] = "{$field} AS {$alias}";
            }
            
            $select_sql = implode(",\n", $select_fields);

            if(strpos($query, '{{SELECT_FIELDS}}') !== false) {            
                $query = str_replace('{{SELECT_FIELDS}}', $select_sql, $query);
            }
        }
        
        if(!empty($this->partition_table_names) && strpos($query, '{{PARTITION_TABLE}}') !== false) {
            $partition_queries = array();
            $partition_query_placeholders = array();
            
            foreach($this->partition_table_names as $index => $partition_table_name) {
                $partition_queries[] = str_replace(array(
                    '{{PARTITION_TABLE}}',
                    ':'
                ), array(
                    $partition_table_name,
                    ":{$index}_"
                ), $query);

                if(!empty($query_placeholders)) {
                    foreach($query_placeholders as $placeholder_name => $placeholder_value) {
                        if(!is_integer($placeholder_name)) {
                            $partition_query_placeholders["{$index}_{$placeholder_name}"] = $placeholder_value;
                        }
                        else {
                            $partition_query_placeholders[] = $placeholder_value;
                        }
                    }
                }
            }
            
            $query = "
                SELECT *
                FROM (" . implode("\nUNION ALL\n", $partition_queries) . ") unioned_query
            ";
            
            $query_placeholders = $partition_query_placeholders;
        }
        
        $non_sort_limit_query = $query;
    
        if(!empty($this->sort_criteria)) {
            $order_by_criteria = '';
        
            foreach($this->sort_criteria as $sort_criteria) {
                $criteria = $sort_criteria['criteria'];
                
                if(is_array($criteria)) {
                    $criteria = implode(', ', $criteria);
                }
            
                $order_by_criteria .= "{$sort_criteria['criteria']} {$sort_criteria['direction']}, ";
            }
            
            $order_by_criteria = rtrim($order_by_criteria, ', ');
            
            $query .= "\nORDER BY {$order_by_criteria}";
        }
    
        if(!empty($this->rows_per_page)) {
            $offset_criteria = '';
        
            if(!empty($this->page_number)) {
                $offset = ($this->page_number - 1) * $this->rows_per_page;
                
                $offset_criteria = " OFFSET {$offset}";
            }
            elseif(isset($this->offset)) {
                $offset_criteria = " OFFSET {$this->offset}";
            }

            $query .= "\nLIMIT {$this->rows_per_page}{$offset_criteria}";
        }
        
        return array(
            'non_sort_limit_query' => $non_sort_limit_query,
            'query' => $query,
            'query_placeholders' => $query_placeholders
        );
    }
    
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
        $finalized_query = $this->getFinalizedQuery();

        $non_sort_limit_query = $finalized_query['non_sort_limit_query'];
        $query = $finalized_query['query'];
        $query_placeholders = $finalized_query['query_placeholders'];

        if($this->has_total_record_count) {
            $this->total_number_of_records = db()->getOne("
                SELECT COUNT(*)
                FROM (
                    {$non_sort_limit_query}
                ) AS query_record_count
            ", $query_placeholders);
        }

        return db()->getAll($query, $query_placeholders);
    }
    
    /**
     * Sets the query that will be used to populate the resultset.
     * 
     * @param string $query the base query of the resultset.
     * @param array $query_placeholders (optional) The values of any query placeholders. Defaults to an empty array.
     * @return void
     */
    public function setBaseQuery($query, $query_placeholders = array()) {
        $this->base_query = $query;
        
        $this->base_query_placeholders = $query_placeholders;
    }
}