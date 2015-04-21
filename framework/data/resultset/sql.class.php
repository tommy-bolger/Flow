<?php
/**
* Configures, retrieves, and stores data from a result set based on a SQL query.
* Copyright (c) 2011, Tommy Bolger
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
     * Adds a filter criteria to the result set.
     * 
     * @param array $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.     
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {
        assert('!empty($criteria)');
        
        $this->filter_criteria[] = $criteria;
        
        $this->filter_placeholder_values += $placeholder_values;
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
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
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

        if($this->has_total_record_count) {
            $this->total_number_of_records = db()->getOne("
                SELECT COUNT(*)
                FROM (
                    {$query}
                ) AS query_record_count
            ", $query_placeholders);
        }

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

            $query .= "\nLIMIT {$this->rows_per_page}{$offset_criteria}";
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