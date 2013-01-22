<?php
/**
* Configures, retrieves, and stores data from a result set.
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

class ResultSet {
    /**
    * @var string The name of the resultset.
    */
    protected $name;

    /**
    * @var boolean Indicates if the resultset will count the total amount of records of the whole dataset.
    */
    protected $has_total_record_count = false;

    /**
    * @var integer The page number of the result set.
    */
    protected $page_number;
    
    /**
    * @var integer How many rows are currently being displayed per page.
    */
    protected $rows_per_page;
    
    /**
    * @var array The filters to apply to the result set.
    */
    protected $filter_criteria = array();
    
    /**
    * @var array The criteria to sort the result set by.
    */
    protected $sort_criteria = array();
    
    /**
    * @var array A list of callback functions to perform processing tasks.
    */
    protected $processor_functions = array();
    
    /**
    * @var integer The total number of records in the data set.
    */
    protected $total_number_of_records;
    
    /**
    * @var array The data of the processed result set.
    */
    protected $data;

    /**
     * Initializes a new instance of ResultSet.
     *
     * @param string $name The name of the resultset.    
     * @return void
     */
    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Enables the resultset to count the total number of rows in the base dataset.
     *      
     * @return void
     */
    public function enableTotalRecordCount() {
        $this->has_total_record_count = true;
    }
    
    /**
     * Indicates if the resultset will count the total number of records in the dataset.
     *      
     * @return boolean
     */
    public function hasTotalRecordCount() {
        return $this->has_total_record_count;
    }
    
    /**
     * Sets the page number of the result set.
     *      
     * @param integer $page_number
     * @return void
     */
    public function setPageNumber($page_number) {
        assert('!empty($page_number) && is_integer($page_number)');
        
        $this->page_number = $page_number;
    }
    
    /**
     * Sets number of max number rows allowed per page.
     *      
     * @param integer $rows_per_page
     * @return void
     */
    public function setRowsPerPage($rows_per_page) {
        assert('!empty($rows_per_page) && is_integer($rows_per_page)');
    
        $this->rows_per_page = $rows_per_page;     
    }
    
    /**
     * Adds a filter criteria to the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.
     * @param array $placeholder_values The values of corresponding placeholders in the criteria.     
     * @return void
     */
    public function addFilterCriteria($criteria, array $placeholder_values = array()) {
        throw new \Exception('This ResultSet does not support filter criteria.');
    }
    
    /**
     * Adds a sort column criteria to the existing criteria of the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.     
     * @param string $direction The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function addSortCriteria($criteria, $direction) {}
    
    /**
     * Sets sort column criteria that overrides any existing criteria of the result set.
     * 
     * @param string $criteria The criteria to sort the result set by.     
     * @param string $direction The sort direction. Can only be either ASC or DESC. Defaults to ASC otherwise.
     * @return void
     */
    public function setSortCriteria($criteria, $direction) {
        $this->sort_criteria = array();
        
        $this->addSortCriteria($criteria, $direction);
    }
    
    /**
     * Adds a processor function.
     * 
     * @param function $processor_function The function that will perform post-processing of the resultset.   
     * @return void
     */
    public function addProcessorFunction($processor_function) {
        assert('is_callable($processor_function)');
        
        $this->processor_functions[] = $processor_function;
    }
    
    /**
     * Extracts and processes the finalized result set.
     * 
     * @return void
     */
    public function process() {
        $this->data = $this->getRawData();
        
        //Execute all added processor functions
        if(!empty($this->processor_functions)) {
            foreach($this->processor_functions as $processor_function) {
                $this->data = $processor_function($this->data);
            }
        }
    }
    
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {}
    
    /**
     * Retrieves the finalized data of the result set.
     *      
     * @return array
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * Retrieves the total number of records of the entire data set.
     * 
     * @return integer
     */
    public function getTotalNumberOfRecords() {
        return $this->total_number_of_records;
    }
    
    /**
     * Retrieves the number of records allowed per page.
     * 
     * @return integer
     */
    public function getRowsPerPage() {
        return $this->rows_per_page;
    }
}