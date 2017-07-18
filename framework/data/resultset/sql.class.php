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
    * @var Database An instance of the database connection.
    */
    protected $database;

    /**
    * @var string The query for the base SQL dataset.
    */
    protected $base_query;
    
    /**
    * @var array The placeholder values for the base SQL dataset.
    */
    protected $base_query_placeholders = array();
    
    /**
    * @var object The resultset of the count query to get the count of the base query.
    */
    protected $count_resultset;
    
    /**
    * @var integer The number of records to retrieve from a cursor of this resultset if it has been set to one.
    */
    protected $cursor_retrieval_chunk_size;
    
    /**
    * @var array The fields to retrieve in the SQL dataset.
    */
    protected $select_fields = array();
    
    /**
    * @var string The base table of the dataset.
    */
    protected $from_table;
    
    /**
    * @var array The tables used in a unioned query across partitioned tables.
    */
    protected $partition_table_names = array();
    
    /**
    * @var array All of the joins in this sql dataset.
    */
    protected $join_criteria = array();
    
    /**
    * @var array All of the placeholder values of the joins in this dataset.
    */
    protected $join_placeholder_values = array();
    
    /**
    * @var array All of the left joins in this sql dataset.
    */
    protected $left_join_criteria = array();
    
    /**
    * @var array All of the placeholder values for the left joins in this dataset.
    */
    protected $left_join_placeholder_values = array();
    
    /**
    * @var array All of the GROUP BY criteria for this query dataset.
    */
    protected $group_by_criteria = array();
    
    /**
     * Initializes a new instance of this class.
     *
     * @param string $name The name of the resultset.    
     * @return void
     */
    public function __construct($name) {
        parent::__construct($name);
        
        $this->database = db();
        
        $this->base_query = "
            {{SELECT_FIELDS}}
            {{FROM_TABLE}}
            {{JOIN_CRITERIA}}
            {{WHERE_CRITERIA}}
            {{GROUP_BY_CRITERIA}}
            {{ORDER_CRITERIA}}
            {{LIMIT_CRITERIA}}
        ";
    }
    
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
        
            $return_value = call_user_func_array(array($this->database, $function_name), $finalized_query);
        }
        else {
            throw new Exception("Method '{$function_name}' does not exist in this object");
        }
        
        return $return_value;
    }
    
    /**
     * Sets the resultset as a cursor to retrieve chunks of data from.
     * 
     * @param integer $cursor_retrieval_chunk_size The number of records to retrieve of each chunk in this cursor.
     * @return void
     */
    public function setAsCursor($cursor_retrieval_chunk_size) {        
        $this->cursor_retrieval_chunk_size = $cursor_retrieval_chunk_size;
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
     * Retrieves a select field definition.
     * 
     * @param string $field The field to add.     
     * @param string $alias (optional) The alias of the field.  
     * @return void
     */
    public function getSelectField($alias) {
        $select_field = array();
    
        if(isset($this->select_fields[$alias])) {
            $select_field = $this->select_fields[$alias];
        }
        
        return $select_field;
    }
    
    /**
     * Sets the from table that the query is based on.
     * 
     * @param string $from_table The name of the from table.     
     * @return void
     */
    public function setFromTable($from_table) {        
        $this->from_table = $from_table;
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
     * Adds a join criteria to the result set.
     * 
     * @param string $criteria The sql criteria to join with.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.  
     * @return void
     */
    public function addJoinCriteria($criteria, array $placeholder_values = array()) {        
        $this->join_criteria[] = $criteria;
        
        $this->join_placeholder_values = array_merge($this->join_placeholder_values, $placeholder_values);
    }
    
    /**
     * Clears all left join criteria from this resultset.
     *  
     * @return void
     */
    public function clearLeftJoinCriteria() {
        $this->left_join_criteria = array();
        
        $this->left_join_placeholder_values = array();
    }
    
    /**
     * Adds a left join criteria to the result set.
     * 
     * @param array $criteria The sql criteria to left join with.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.   
     * @return void
     */
    public function addLeftJoinCriteria($criteria, array $placeholder_values = array()) {        
        $this->left_join_criteria[] = $criteria;
        
        $this->left_join_placeholder_values = array_merge($this->left_join_placeholder_values, $placeholder_values);
    }
    
    /**
     * Adds a filter criteria to the result set.
     * 
     * @param array $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria. 
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {        
        $this->filter_criteria[] = $criteria;
        
        $this->filter_placeholder_values = array_merge($this->filter_placeholder_values, $placeholder_values);
    }
    
    /**
     * Adds a group by criteria to the query dataset.
     * 
     * @param string $criteria The individual criteria to group the dataset by.
     * @param string $having (optional) The criteria for the HAVING keyword.
     * @return void
     */
    public function addGroupByCriteria($criteria, $having = NULL) {        
        $group_by_criteria = $criteria;
        
        if(!empty($having)) {
            $group_by_criteria .= " HAVING {$having}";
        }
        
        $this->group_by_criteria[] = $group_by_criteria;
    }

    /**
     * Adds a sort column criteria to the existing criteria of the result set.
     * 
     * @param string|array $criteria The criteria to sort the result set by Can either be a single column name as a string or several in an array.     
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function addSortCriteria($criteria, $direction = 'ASC') {        
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
     * Clears all sort criteria for the current resultset.

     * @return void
     */
    public function clearSortCriteria() {        
        $this->sort_criteria = array();
    }
    
    /**
     * Sets sort column criteria that overrides any existing criteria of the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.     
     * @param string $direction (optional) The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function setSortCriteria($criteria, $direction = 'ASC') {        
        $this->clearSortCriteria();
        
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
        $this->clearSortCriteria();
        
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
        
        /* ---------- SELECT ---------- */
        
        $select_sql = '';
        
        if(!empty($this->select_fields)) {            
            $select_fields = array();
            
            foreach($this->select_fields as $alias => $field) {
                $select_fields[] = "{$field} AS {$alias}";
            }
            
            $select_sql = "SELECT \n" . implode(",\n", $select_fields) . "\n";
        }
        
        /* ---------- FROM ---------- */
        
        $from_sql = '';
        
        if(!empty($this->from_table)) {
            $from_sql = "FROM {$this->from_table}\n";
        }
        
        /* ---------- JOIN and LEFT JOIN ---------- */
        
        $join_criteria = '';
        
        if(!empty($this->join_criteria) || !empty($this->left_join_criteria)) {
            $join_sql = implode("\nJOIN ", $this->join_criteria);
            
            if(!empty($join_sql)) {
                $join_criteria = "\nJOIN {$join_sql}";
            }
            
            $query_placeholders += $this->join_placeholder_values;
            
            $left_join_sql = implode("\nLEFT JOIN ", $this->left_join_criteria);
            
            if(!empty($left_join_sql)) {
                $join_criteria .= "\nLEFT JOIN {$left_join_sql}";
            }
            
            $query_placeholders += $this->left_join_placeholder_values;
        }        

        /* ---------- WHERE/AND ---------- */
        $where_criteria = '';
        $and_criteria = '';
        
        if(!empty($this->filter_criteria)) {
            $where_criteria = implode(' AND ', $this->filter_criteria);

            if(strpos($query, '{{WHERE_CRITERIA}}') !== false) {
                if(!empty($where_criteria)) {
                    $where_criteria = "WHERE {$where_criteria}";
                }
                
            }
            elseif(strpos($query, '{{AND_CRITERIA}}') !== false) {
                if(!empty($where_criteria)) {
                    $and_criteria = $where_criteria;
                    $where_criteria = '';
                }
            }
            
            $query_placeholders += $this->filter_placeholder_values;
        }
        
        /* ---------- GROUP BY ---------- */
        
        $group_by_criteria = '';
        
        if(!empty($this->group_by_criteria)) {
            $group_by_criteria = 'GROUP BY ' . implode(', ', $this->group_by_criteria);
        }
        
        $query = str_replace(array(
            '{{SELECT_FIELDS}}',
            '{{FROM_TABLE}}',
            '{{JOIN_CRITERIA}}',
            '{{WHERE_CRITERIA}}',
            '{{AND_CRITERIA}}',
            '{{GROUP_BY_CRITERIA}}'
        ), array(
            $select_sql,
            $from_sql,
            $join_criteria,
            $where_criteria,
            $and_criteria,
            $group_by_criteria
        ), $query);
        
        /* ---------- Partition tables ---------- */
        
        if(!empty($this->partition_table_names)) {
            $partition_queries = array();
            $partition_query_placeholders = array();
            
            foreach($this->partition_table_names as $index => $partition_table_name) {
                $partition_queries[] = str_replace(array(
                    '{{PARTITION_TABLE}}',
                    '{{ORDER_CRITERIA}}',
                    '{{LIMIT_CRITERIA}}'
                ), array(
                    "{$partition_table_name}",
                    '',
                    ''
                ), $query);

                if(!empty($query_placeholders)) {
                    foreach($query_placeholders as $placeholder_name => $placeholder_value) {
                        $partition_query_placeholders[] = $placeholder_value;
                    }
                }
            }
            
            $query = "
                SELECT *
                FROM (" . implode("\nUNION ALL\n", $partition_queries) . ") unioned_query
                {{ORDER_CRITERIA}}
                {{LIMIT_CRITERIA}}
            ";
            
            $query_placeholders = $partition_query_placeholders;
        }
        
        /* ---------- CURSOR Declaration ---------- */
        
        if(isset($this->cursor_retrieval_chunk_size)) {
            $query = "
                DECLARE {$this->name} CURSOR FOR
                {$query}
            ";
        }
        
        /* ---------- ORDER BY ---------- */
        
        $order_by_criteria = '';
    
        if(!empty($this->sort_criteria)) {
            $order_by_criteria = '';
        
            foreach($this->sort_criteria as $sort_criteria) {
                $criteria = $sort_criteria['criteria'];
                
                if(!empty($this->partition_table_names) && strpos($criteria, '.') !== false) {
                    $criteria_split = explode('.', $criteria);
                    
                    $criteria = $criteria_split[1];
                }
            
                $order_by_criteria .= "{$criteria} {$sort_criteria['direction']}, ";
            }
            
            $order_by_criteria = "ORDER BY " . rtrim($order_by_criteria, ', ');
        }
        
        /* ---------- LIMIT ---------- */
    
        $limit_criteria = '';
    
        if(!empty($this->rows_per_page)) {
            $offset_criteria = '';
        
            if(!empty($this->page_number)) {
                $offset = ($this->page_number - 1) * $this->rows_per_page;
                
                $offset_criteria = " OFFSET {$offset}";
            }
            elseif(isset($this->offset)) {
                $offset_criteria = " OFFSET {$this->offset}";
            }
            
            $limit_criteria = "\nLIMIT {$this->rows_per_page}{$offset_criteria}";
        }
        
        $query = str_replace(array(
            '{{ORDER_CRITERIA}}',
            '{{LIMIT_CRITERIA}}'
        ), array(
            $order_by_criteria,
            $limit_criteria
        ), $query);

        return array(
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

        $query = $finalized_query['query'];
        $query_placeholders = $finalized_query['query_placeholders'];
        
        $data = $this->database->getAll($query, $query_placeholders);

        if($this->has_total_record_count) {
            if(isset($this->count_resultset)) {         
                $record_count = $this->count_resultset->getColumn();
            
                $this->total_number_of_records = array_sum($record_count);
            }
            else {
                throw new Exception("A count query has not been set despite total record counts being enabled.");
            }
        }
        else {
            $this->total_number_of_records = count($data);
        }
        
        return $data;
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
    
    /**
     * Retrieves the base query.
     * 
     * @return string The base query of this resultset.
     */
    public function getBaseQuery() {
        return $this->base_query;
    }
    
    /**
     * Clears the count resultset for this resultset.
     * 
     * @return void
     */
    public function clearCountResultset() {
        $this->count_resultset = NULL;
    }
    
    /**
     * Sets the resultset that will be used to count the total number of rows in this resultset before any pagination is applied.
     * 
     * @param object $count_resultset An instance of this object.
     * @return void
     */
    public function setCountResultset($count_resultset) {
        $count_resultset->setSelectField('COUNT(*)', 'total_record_count');
        
        $count_resultset->disableTotalRecordCount();
        
        $count_resultset->clearCountResultset();
        
        $count_resultset->clearSortCriteria();
    
        $this->count_resultset = $count_resultset;
    }
    
    /**
     * Retrieves the count resultset.
     * 
     * @return object
     */
    public function getCountResultset() {
        return $this->count_resultset;
    }
    
    /**
     * Retrieves the next chunk of data from this resultset's cursor if it is set to one.
     * 
     * @return string The base query of this resultset.
     */
    public function getNextCursorChunk() {
        if(!isset($this->cursor_retrieval_chunk_size)) {
            throw new Exception("This resultset is not set to a cursor. Please do so via setAsCursor().");
        }
        
        return $this->database->getAll("
            FETCH {$this->cursor_retrieval_chunk_size}
            FROM {$this->name}
        ");
    }
}