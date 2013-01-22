<?php
/**
* Displays dynamic tabular data with the ability to adjust how it's viewed.
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

namespace Framework\Html\Table;

use \Framework\Utilities\Http;
use \Framework\Utilities\ArrayFunctions;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\Fields\Dropdown;
use \Framework\Html\Form\Fields\Textbox;
use \Framework\Html\Form\Fields\Submit;

class DataTable
extends Table {
    /**
    * @var array Stores the table state as an array to pass between requests.
    */
    protected $table_state_request;

    /**
    * @var object The form of the table that handles various display options.
    */
    protected $table_form;
    
    /**
    * @var object The resultset used to populate the table.
    */
    protected $resultset;

    /* Table configuration properties */
    
    /**
    * @var boolean Indicates if the DataTable should store store resume its state from the session.
    */
    protected $resume_from_session;
    
    /**
    * @var array A list of columns that can be sorted in the table.
    */
    protected $sort_column_options;
    
    /**
    * @var array The list of options of the number of rows to display per page.
    */
    protected $rows_per_page_options;
    
    /**
    * @var array The list of columns with contents that can be a link to another page.
    */
    protected $columns_as_link;
    
    /**
    * @var array The urls of the link columns.
    */
    protected $column_link_urls;
    
    /**
    * @var string The query string parameters for the link columns.
    */
    protected $column_link_parameters;
    
    /* Table state properties */
    
    /**
    * @var integer The current page number.
    */
    protected $current_page;
    
    /**
    * @var string|array The columns that the resultset is being sorted by.
    */
    protected $current_sort_column;
    
    /**
    * @var string The direction to sort the resultset in. Can only be ASC or DESC.
    */
    protected $current_sort_direction;
    
    /**
    * @var integer How many rows are currently being displayed per page.
    */
    protected $current_rows_per_page;
    
    /**
    * @var integer The index of the selected option in the filter dropdown.
    */
    protected $current_selected_filters = array();
    
    /**
    * @var integer The total number of records in the resultset.
    */
    protected $total_number_of_records;
    
    /**
    * @var array A list of all criteria for the selected filter dropdown option.
    */
    protected $selected_filter_criteria = array();

    /**
     * Initializes a new instance of DataTable.
     *      
     * @param string $table_name The table's name.
     * @param boolean $resume_from_session Indicates if the DataTable should store store resume its state from the session.
     * @return void
     */
    public function __construct($table_name, $resume_from_session = false) {
        parent::__construct($table_name);
        
        $this->resume_from_session = $resume_from_session;
        
        //Create the table form        
        $this->table_form = new Form("{$table_name}_form", Http::getPageUrl(), 'post', false);
        
        $this->table_form->removeAttribute('id');
        $this->table_form->addClass('full_submit');

        $this->addRequestVariable('t', $table_name);
        
        $this->getTableState();
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
    
        $this->addCssFile('framework/DataTable.css');
        $this->addCssFile('framework/Form.css');
        $this->addCssFile('showLoading.css');        
        
        $this->addJavascriptFile('jquery.min.js');
        $this->addJavascriptFile('jquery.showLoading.js');
        $this->addJavascriptFile('request.js');
        $this->addJavascriptFile('DataTable.js');
    }
    
    /**
     * Sets the table header row.
     *      
     * @param array|string $header The columns for the header. Can either be an array for each column of the table or a string as a cell that spans all columns of the table.
     * @return void
     */
    public function setHeader($header) {
        parent::addHeader($header);
    }
    
    /**
     * Not used in this object.
     *      
     * @param array|string $header
     * @return void
     */
    public function addHeader($header) {}
    
    /**
     * Not used in this object.
     *      
     * @param array $header_rows
     * @return void
     */
    public function addHeaderRows($header_rows) {}
    
    /**
     * Retrieves the table's current view state either from a request or from the session. 
     *      
     * @return void
     */
    private function getTableState() {
        //The names of all session variables used to store the table's state if the resume_from_session property is set to true
        $page_session_name = "{$this->name}_page_number";
        $sort_column_session_name = "{$this->name}_sort_column";
        $sort_order_session_name = "{$this->name}_sort_direction";
        $rows_per_page_session_name = "{$this->name}_rows_per_page";
        $selected_filter_session_name = "{$this->name}_selected_filters";
        
        if($this->resume_from_session) {            
            //Load the table's state from the session
            $this->current_page = session()->$page_session_name;
            $this->current_sort_column = session()->$sort_column_session_name;
            $this->current_sort_direction = session()->$sort_order_session_name;
            $this->current_rows_per_page = session()->$rows_per_page_session_name;
            $this->current_selected_filters = session()->$selected_filter_session_name;
        }

        //Retrieve any changes to the table's state from either a get request or a post request
        if(request()->t == $this->name) {
            $this->current_page = request()->get->getVariable('p', 'integer');
            $this->current_sort_column = request()->s;
            $this->current_sort_direction = request()->d;
            $this->current_rows_per_page = request()->get->getVariable("r", 'integer');
            
            if($this->table_form->wasSubmitted() && $this->table_form->isValid()) {                
                $form_rows_per_page = request()->post->getVariable("r", 'integer');
                
                if(!empty($form_rows_per_page) && $form_rows_per_page != $this->current_rows_per_page) {
                    $this->current_rows_per_page = $form_rows_per_page;
                
                    $this->current_page = 1;
                }
                
                $current_selected_filters = request()->post->f;

                if(!empty($current_selected_filters)) {
                    foreach($current_selected_filters as $filter_name => $filter_value) {
                        $this->current_selected_filters[$filter_name] = $filter_value;
                    }
                }
            }
            
            if($this->resume_from_session) {
                session()->$page_session_name = $this->current_page;
                session()->$sort_column_session_name = $this->current_sort_column;
                session()->$sort_order_session_name = $this->current_sort_direction;
                session()->$rows_per_page_session_name = $this->current_rows_per_page;
                session()->$selected_filter_session_name = $this->current_selected_filters;
            }
        }
        
        if(empty($this->current_page)) {
            $this->current_page = 1;
        }
        
        if(!empty($this->current_page)) {
            $this->addRequestVariable('p', $this->current_page);
        }
    }
    
    /**
     * Sets the allowed number of rows to limit each page by.
     *      
     * @param array $rows_per_page_options
     * @return void
     */
    public function setRowsPerPageOptions($rows_per_page_options) {
        assert('(is_array($rows_per_page_options) && !empty($rows_per_page_options))');
        
        $this->rows_per_page_options = array_combine($rows_per_page_options, $rows_per_page_options);
        
        $this->addRequestVariable('r', $this->current_rows_per_page, false);
    }
    
    /**
     * Sets the column options that the resultset can be sorted by.
     * 
     * @param string|array $sort_columns The columns to sort the resultset by.
     * @return void
     */
    public function setSortColumnOptions($sort_column_options) {
        assert('is_array($sort_column_options)');
        
        $this->sort_column_options = $sort_column_options;
        
        $this->addRequestVariable('s', $this->current_sort_column);
        $this->addRequestVariable('d', $this->current_sort_direction);
    }
    
    /**
     * Sets the columns that will be links in the resultset.
     * 
     * @param array $columns_as_link The columns that will be a link.
     * @param array $column_link_urls The base url of each column.
     * @param array $column_link_parameters (optional) The query string parameters for each column url.       
     * @return void
     */
    public function setColumnsAsLink($columns_as_link, $column_link_urls, $column_link_parameters = array()) {
        assert('is_array($columns_as_link)');
        assert('is_array($column_link_urls) || is_string($column_link_urls)');
        assert('is_array($column_link_parameters)');
    
        $this->columns_as_link = $columns_as_link;
        
        $this->column_link_urls = $column_link_urls;
        
        $this->column_link_parameters = $column_link_parameters;
    }
    
    /**
     * Adds record filter options as a dropdown.
     *
     * @param string $filter_field_name The name of the record filter field.
     * @param array $filter_options The criteria options for the record filter dropdown.
     * @param string $filter_label (optional) The label of the filter dropdown. Defaults to an empty string.
     * @param string $column_name (optional) The name of the column to attach the filter field to. Defaults to an empty string.
     * @return void
     */
    public function addFilterDropdown($filter_field_name, $filter_options, $filter_label = '', $column_name = '') {
        if(!$this->resume_from_session) {
            throw new \Exception("This feature is only supported when resume_from_session is enabled in the constructor.");
        }
    
        assert('is_array($filter_options) && !empty($filter_options)');
        
        $filter_option_labels = array_keys($filter_options);
        
        if(!empty($filter_label)) {
            foreach($filter_option_labels as &$filter_option_label) {
                $filter_option_label = "{$filter_label}: {$filter_option_label}";
            }
        }

        $filter_dropdown = new Dropdown("f[{$filter_field_name}]", '', $filter_option_labels);
        
        $selected_dropdown_index = NULL;
        
        //Retrieve the submitted index for this field
        if(isset($this->current_selected_filters[$filter_field_name])) {
            $selected_dropdown_index = $this->current_selected_filters[$filter_field_name];
        }

        $filter_dropdown->setDefaultValue($selected_dropdown_index);        
        $filter_dropdown->addBlankOption($filter_label);

        //If this field was submitted then retrieve the criteria option at the submitted index.
        $filter_option_values = array_values($filter_options);

        if(isset($filter_option_values[$selected_dropdown_index])) {
            $this->selected_filter_criteria[] = array(
                'criteria' => $filter_option_values[$selected_dropdown_index],
                'placeholder_values' => array()
            );
        }

        //If the field isn't attached to a column then give it the group name for table filters.
        if(empty($column_name)) {
            $column_name = 'table_filters';
        }
        
        $this->table_form->addField($filter_dropdown, $column_name);
    }
    
    /**
     * Adds a record filter option as a textbox.
     *
     * @param string $filter_field_name The name of the record filter field.
     * @param string $filter_criteria The filter criteria with placeholders to be added to the resultset if a value is entered for this field.
     * @param string $filter_label (optional) The label of the filter dropdown. Defaults to an empty string.
     * @param string $column_name (optional) The name of the column to attach the filter field to. Defaults to an empty string.
     * @return void
     */
    public function addFilterTextbox($filter_field_name, $filter_criteria, $filter_label = '', $column_name = '') {
        assert('is_string($filter_field_name) && !empty($filter_field_name)');
        assert('is_string($filter_criteria) && !empty($filter_criteria)');
    
        $filter_textbox = new Textbox("f[{$filter_field_name}]", $filter_label);
        
        $selected_value = NULL;

        //Retrieve the submitted index for this field if it was submitted
        if(isset($this->current_selected_filters[$filter_field_name])) {
            $selected_value = $this->current_selected_filters[$filter_field_name];

            if(!empty($selected_value) || (string)$selected_value == '0') {         
                //Retrieve the number of times the question mark placeholder appears in the query and multiply the number of placeholder values to correspond
                $placeholder_values = array();
                
                $placeholder_count = substr_count($filter_criteria, '?');
                
                if($placeholder_count > 0) {
                    $placeholder_values = array_fill(0, $placeholder_count, $selected_value);
                }
                
                $this->selected_filter_criteria[] = array(
                    'criteria' => $filter_criteria,
                    'placeholder_values' => $placeholder_values
                );
            }            
        }

        $filter_textbox->setDefaultValue($selected_value);
        
        //If the field isn't attached to a column then give it the group name for table filters.
        if(empty($column_name)) {
            $column_name = 'table_filters';
        }
        
        $this->table_form->addField($filter_textbox, $column_name);
    }
    
    /**
     * Processes and retrieves data from the finalized resultset.
     * 
     * @param object $resultset An object of type ResultSet. Valid classes fall under \Framework\Data\ResultSet.
     * @return void
     */        
    public function process($resultset, $processor_function = NULL) {
        assert('is_object($resultset) && !empty($resultset)');
        
        if(!empty($this->selected_filter_criteria)) {
            foreach($this->selected_filter_criteria as $filter_criteria) {
                $resultset->addFilterCriteria($filter_criteria['criteria'], $filter_criteria['placeholder_values']);
            }
        }
        
        if(!empty($this->current_sort_column) && !empty($this->sort_column_options[$this->current_sort_column])) {
            $resultset->setSortCriteria($this->sort_column_options[$this->current_sort_column], $this->current_sort_direction);
        }
        
        $rows_per_page = $this->current_rows_per_page;
        
        if(!empty($rows_per_page) && !empty($this->rows_per_page_options[$rows_per_page])) {
            $resultset->setRowsPerPage($rows_per_page);
        }
        
        $resultset->setPageNumber($this->current_page);
        
        $resultset->process();
        
        $data = $resultset->getData();
        
        if(is_callable($processor_function)) {
            $data = $processor_function($data);
        }
        
        $this->addRows($data);
        
        $this->total_number_of_records = $resultset->getTotalNumberOfRecords();
        
        $this->resultset = $resultset;
    }                
    
    /**
     * This function is not available in this object.
     * 
     * @param string $query the base query of the resultset.
     * @param array $query_placeholders (optional) The values of the query placeholders.
     * @param function $processor_function (optional) The function that will perform post-processing of the resultset.       
     * @return void
     */
    public function useQuery($query, $query_placeholders = array(), $processor_function = NULL) {}
    
    /**
     * Renders and retrieves the rows per page options as an html dropdown.
     *  
     * @return string
     */
    protected function setRowsPerPageHtml() {
        if(!empty($this->rows_per_page_options)) {
            $rows_per_page_options = array_combine($this->rows_per_page_options, $this->rows_per_page_options);
            
            $rows_per_page_dropdown = new Dropdown("r", '', $rows_per_page_options, array('data_table_rows'));
            $rows_per_page_dropdown->setDefaultValue($this->current_rows_per_page);
            $rows_per_page_dropdown->removeAttribute('id');
            
            $this->table_form->addField($rows_per_page_dropdown);
            
            $submit_button = new Submit("submit", '&gt;', array('rows_submit'));        
            $submit_button->removeAttribute('id');
            
            $this->table_form->addField($submit_button);
        }
    }
    
    /**
     * Adds a variable to the request used to change table state.
     * 
     * @param string $variable_name The name of the variable to add.
     * @param string|integer $variable_value The value of the variable to add.
     * @param boolean $add_to_form (optional) Indicates if the variable should be added to the table form. Defaults to true.     
     * @return string
     */
    public function addRequestVariable($variable_name, $variable_value, $add_to_form = true) {
        $this->table_state_request[$variable_name] = $variable_value;
        
        if($add_to_form) {
            $this->table_form->addHidden($variable_name, $variable_value);
        }
    }
    
    /**
     * Generates and returns a url specific to the data table.
     * 
     * @param array $query_string_parameters The query string in the following format: array('name' => 'value').    
     * @return string
     */
    protected function generateUrl($query_string_parameters) {
        $query_string_parameters = array_merge($this->table_state_request, $query_string_parameters);
    
        return Http::getPageUrl($query_string_parameters);
    } 
    
    /**
     * Retrieves alt text of a number link in the pagination navigation.
     * 
     * @param integer $page_number
     * @param string $title_name The text of the alt text prior to the page number text.          
     * @return string
     */
    private function generatePageLinkTitle($page_number, $title_name = '') {
        $rows_per_page = $this->resultset->getRowsPerPage();
    
        $page_number_start_range = ($page_number * $rows_per_page) - $rows_per_page;
            
        $page_number_end_range = $page_number_start_range + $rows_per_page;
        
        $page_number_start_range += 1;
        
        if($page_number_end_range > $this->total_number_of_records) {
            $page_number_end_range = $this->total_number_of_records;
        }
        
        if(empty($title_name)) {
            $title_name = "Show page {$page_number}";
        }
        
        return "{$title_name}, results {$page_number_start_range} - {$page_number_end_range} of {$this->total_number_of_records}";
    }
    
    /**
     * Renders and retrieves the pagination navigation html.
     *  
     * @param boolean $render_container (optional) Indicates if the pagination should be wrapped in a <div> tag or not. Defaults to true.         
     * @return string
     */
    protected function getPaginationHtml($render_container = true) {
        $page_count_html = '';
        $first_page_html = '';
        $previous_page_html = '';
        $page_numbers_html = '';
        $next_page_html = '';
        $last_page_html = '';
        
    
        if($this->resultset->hasTotalRecordCount()) {
            $total_pages = 1;
            
            $rows_per_page = $this->resultset->getRowsPerPage();
            
            if(!empty($rows_per_page)) {
                $total_pages = floor($this->total_number_of_records / $rows_per_page);
                
                //If the number of rows per page doesn't cleanly divide into the total records then add a final page.
                if(($this->total_number_of_records % $rows_per_page) > 0) {
                    $total_pages += 1;
                }
            }
            
            if($total_pages == 1) {
                return "";
            }
            
            //Determine the start of the page range to show in pagination
            $page_range_start = NULL;
            
            if($this->current_page == 1) {
                $page_range_start = 1;
            }
            elseif($this->current_page == $total_pages) {
                $page_range_start = $this->current_page - 4;
                
                if($page_range_start <= 0) {
                    $page_range_start = 1;
                }
            }
            else {
                $page_range_start = $this->current_page - 2;
                
                if($page_range_start <= 0) {
                    $page_range_start = 1;
                }
            }
            
            //Determine the end of the page range to show in pagination
            $page_range_end = NULL;
            
            if($this->current_page == $total_pages) {
                $page_range_end = $this->current_page;
            }
            elseif($this->current_page == 1) {
                $page_range_end = $this->current_page + 4;
                
                if($page_range_end >= $total_pages) {
                    $page_range_end = $total_pages;
                }
            }
            else {
                $page_range_end = $this->current_page + 2;
                
                if($page_range_end >= $total_pages) {
                    $page_range_end = $total_pages;
                }
            }
            
            //Generate the page numbers to display for pagination
            $page_range = range($page_range_start, $page_range_end);
            
            $previous_page_number = $this->current_page - 1;
            
            $next_page_number = $this->current_page + 1;
            
            $page_count_html = "<span>Page {$this->current_page} of {$total_pages}</span>";
            
            //Generate the previous page and first page links if not on the first page
            if($this->current_page > 1) {
                $first_page_title = $this->generatePageLinkTitle(1, 'First page');
                $first_page_url = $this->generateUrl(array('p' => 1));
                
                $first_page_html = "&nbsp;<li class=\"page_number\"><a href=\"{$first_page_url}\" title=\"{$first_page_title}\">&laquo;First</a></li>";
                
                $previous_page_title = $this->generatePageLinkTitle($previous_page_number, 'Previous page');
                $previous_page_url = $this->generateUrl(array('p' => $previous_page_number));
            
                $previous_page_html = "&nbsp;<li class=\"page_number\"><a href=\"{$previous_page_url}\" title=\"{$previous_page_title}\">&lt;</a></li>";
            }
            
            //Generate a link for each page in the pagination range
            foreach($page_range as $page_number) {
                //Generate the title of the link
                $page_number_title = $this->generatePageLinkTitle($page_number);
                $page_number_url = $this->generateUrl(array('p' => $page_number));
            
                $current_page_class = "";
                
                $page_number_link = '';
                
                if($page_number != $this->current_page) {
                    $page_number_link = "<a href=\"{$page_number_url}\" title=\"{$page_number_title}\">{$page_number}</a>";
                }
                else {
                    $current_page_class = " current_page_number";
                    
                    $page_number_link = "<span title=\"{$page_number_title}\">{$page_number}</span>";
                }
            
                $page_numbers_html .= "&nbsp;<li class=\"page_number{$current_page_class}\">{$page_number_link}</li>";
            }
            
            //Generate the next page and last page links if not on the last page
            if($this->current_page < $total_pages) {
                $next_page_title = $this->generatePageLinkTitle($next_page_number, 'Next page');
                $next_page_url = $this->generateUrl(array('p' => $next_page_number));
                
                $next_page_html = "&nbsp;<li class=\"page_number\"><a href=\"{$next_page_url}\" title=\"{$next_page_title}\">&gt;</a></li>";
                
                $last_page_title = $this->generatePageLinkTitle($total_pages, 'Last page');
                $last_page_url = $this->generateUrl(array('p' => $total_pages));
            
                $last_page_html = "&nbsp;<li class=\"page_number\"><a href=\"{$last_page_url}\" title=\"{$last_page_title}\">Last&raquo;</a></li>";
            }
        }
        else {            
            //Generate the previous page link if not on the first page
            if($this->current_page > 1) {
                $previous_page_number = $this->current_page - 1;
            
                $previous_page_title = $this->generatePageLinkTitle($previous_page_number, 'Previous page');
                $previous_page_url = $this->generateUrl(array('p' => $previous_page_number));
            
                $previous_page_html = "<li class=\"page_number\"><a href=\"{$previous_page_url}\" title=\"{$previous_page_title}\">&lt;</a></li>";
            }
            
            //Generate the next page link if the resultset still has data
            if(!empty($this->child_elements)) {
                $next_page_number = $this->current_page + 1;
            
                $next_page_title = $this->generatePageLinkTitle($next_page_number, 'Next page');
                $next_page_url = $this->generateUrl(array('p' => $next_page_number));
                
                $next_page_html = "<li class=\"page_number\"><a href=\"{$next_page_url}\" title=\"{$next_page_title}\">&gt;</a></li>";
            }
        }
        
        $pagination_html = "<ul class=\"page_numbers\">{$page_count_html}{$first_page_html}{$previous_page_html}{$page_numbers_html}{$next_page_html}{$last_page_html}</ul>";
        
        if($render_container) {
            $pagination_html = "<div class=\"pagination\">{$pagination_html}</div>";
        }
        
        return $pagination_html;
    }
    
    /**
     * Renders and retrieves the table's header html.
     *      
     * @param boolean $render_container (optional) Indicates if the table header should be wrapped in a <thead> tag or not. Defaults to true.
     * @param boolean $render_second_row (optional) Indicates if only the columns should be rendered. Defaults to false.
     * @return string
     */
    protected function getHeaderHtml($render_container = true, $columns_only = false) {
        if(empty($this->sort_column_options)) {
            return parent::getHeaderHtml();
        }
    
        $header_html = "";
    
        if(isset($this->header[0])) {
            $table_header = $this->header[0];
            $table_filter_header = array();
            
            if(!empty($table_header)) {
                $header_index = 0;
            
                foreach($table_header as $column_name => $column_display_name) {
                    //If a filter field is attached to this column then add it to the table header array
                    $column_filter_fields = $this->table_form->getFieldsByGroup($column_name);
                
                    if(!empty($column_filter_fields)) {
                        $table_filter_header[$header_index] = current($column_filter_fields);
                    }
                
                    $sort_direction_indicator = '';
                    $link_sort_direction = 'asc';
                    $sorted_column_class = '';

                    //Determine which sort direction to pass in the column's url and the visual sort indicator.
                    if($column_name == $this->current_sort_column) {
                        $sorted_column_class = ' sorted_column';
                    
                        switch($this->current_sort_direction) {
                            case 'asc':
                                $sort_direction_indicator = '&uarr;';
                                $link_sort_direction = 'desc';
                                break;
                            case 'desc':
                                $sort_direction_indicator = '&darr;';
                                $link_sort_direction = 'asc';
                                break;
                        }
                    }
                    
                    $sort_direction_indicator = "<span class=\"sort_indicator {$link_sort_direction}\">{$sort_direction_indicator}</span>";
                    
                    $sort_link_title = "Sort by {$column_display_name} ";
                    
                    switch($link_sort_direction) {
                        case 'asc':
                            $sort_link_title .= 'ascending';
                            break;
                        case 'desc':
                            $sort_link_title .= 'descending';
                            break;
                    }
                    
                    $sort_link_url = $this->generateUrl(array(
                        's' => $column_name,
                        'd' => $link_sort_direction
                    ));

                    $header_html .= "<th class=\"table_header{$sorted_column_class}\"><a href=\"{$sort_link_url}\" title=\"{$sort_link_title}\">{$column_display_name}</a>{$sort_direction_indicator}</th>";
                    
                    $header_index++; 
                }
                
                if($columns_only) {
                    return $header_html;
                }
                
                $header_html = "<tr class=\"columns\">{$header_html}</tr>";

                //If the filter header has fields then render it.
                if(!empty($table_filter_header)) {
                    $header_html .= '<tr class="filters">';
                
                    for($filter_header_index = 0; $filter_header_index < $header_index; $filter_header_index++) {
                        $header_html .= '<th>';
                    
                        if(!empty($table_filter_header[$filter_header_index])) {
                            $filter_field = $table_filter_header[$filter_header_index];
                        
                            $label_html = $filter_field->getLabelHtml();
                
                            if(!empty($label_html)) {
                                $label_html .= ':&nbsp;&nbsp;';
                            }
                        
                            $header_html .= $label_html . $filter_field->getFieldHtml();
                        }
                        else {
                            $header_html .= '&nbsp;';
                        }
                        
                        $header_html .= '</th>';
                    }
                    
                    $header_html .= '</tr>';
                }
                
                if($render_container) {
                    $header_html = "<thead>{$header_html}</thead>";
                }
            }
        }
        
        return $header_html;
    }
    
    /**
     * Renders and retrieves the table's body html.
     * 
     * @param boolean $render_container (optional) Indicates if the table body should be wrapped in a <tbody> tag or not. Defaults to true.     
     * @return string
     */
    protected function getBodyHtml($render_container = true) {            
        $body_html = '';
    
        if(isset($this->child_elements['default_body'])) {
            $body_rows = $this->child_elements['default_body'];
        
            if(!empty($body_rows)) {                
                foreach($body_rows as $row) {                    
                    $display_columns = NULL;
                    
                    if(!empty($this->number_of_columns)) {
                        $display_columns = array_slice($row, 0, $this->number_of_columns);
                    }
                    else {
                        $display_columns = $row;
                    }

                    if(!empty($this->columns_as_link) && !empty($this->column_link_urls)) {
                        $display_columns = array_values($display_columns);
                        
                        foreach($this->columns_as_link as $column_as_link) {
                            $link_column_index = $column_as_link - 1;
                        
                            $column_link_url = NULL;

                            if(is_string($this->column_link_urls)) {
                                $column_link_url = $this->column_link_urls;
                            }
                            elseif(is_array($this->column_link_urls)) {                            
                                if(isset($this->column_link_urls[$column_as_link])) {
                                    $column_link_url = $this->column_link_urls[$column_as_link];
                                }
                                else {
                                    throw new \Exception("No url was specified for column '{$column_as_link}'.");
                                }
                            }
                            
                            if(!empty($this->column_link_parameters)) {
                                $first_parameter_element = current($this->column_link_parameters);
                                
                                $column_link_parameters = array();
                                
                                if(is_string($first_parameter_element)) {
                                    $column_link_parameters = $this->column_link_parameters;
                                }
                                elseif(is_array($first_parameter_element) && isset($this->column_link_parameters[$column_as_link])) {
                                    $column_link_parameters = $this->column_link_parameters[$column_as_link];
                                }
                                
                                if(!empty($column_link_parameters)) {
                                    $column_values = ArrayFunctions::extractKeys($row, $column_link_parameters);
                                    
                                    $url_parameters = Http::generateQueryString(array_combine(array_keys($column_link_parameters), $column_values));

                                    $column_link_url = Http::generateUrl($column_link_url, $url_parameters);
                                }
                            }
                            
                            $display_columns[$link_column_index] = "<a href=\"{$column_link_url}\">{$display_columns[$link_column_index]}</a>";
                        }
                    }
                    
                    $body_html .= '<tr><td class="table_body">' . implode('</td><td class="table_body">', $display_columns) . '</td></tr>';
                }
                
                if($render_container) {
                    $body_html = "<tbody>{$body_html}</tbody>";
                }
            }
        }
        
        return $body_html;
    }
    
    /**
     * Renders and retrieves the table's menu html.
     *      
     * @return string
     */
    protected function getMenuHtml() {
        $this->setRowsPerPageHtml();
        
        $pagination_html = $this->getPaginationHtml();
        
        $form_template = $this->table_form->toTemplateArray();
        
        $table_menu_items = array();
        
        $filter_fields = $this->table_form->getFieldsByGroup('table_filters');
        
        if(!empty($filter_fields)) {
            foreach($filter_fields as $filter_field) {
                $label_html = $filter_field->getLabelHtml();
                
                if(!empty($label_html)) {
                    $label_html .= ':&nbsp;';
                }
            
                $table_menu_items[] = "{$label_html}{$filter_field->getFieldHtml()}&nbsp;{$form_template["submit"]}&nbsp;";
            }
        }
        
        if(isset($form_template["r"])) {
            $table_menu_items[] = "{$form_template["r"]} per page {$form_template["submit"]}";
        }
        
        if(!empty($pagination_html)) {
            $table_menu_items[] = $pagination_html;
        }

        return "
            {$form_template["{$this->name}_form_open"]}
                <div class=\"table_menu\">
                        " . implode('', $table_menu_items) . "
                    <div class=\"clear\"></div>
                </div>
        ";
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function getTableHtml() {    
        $table_html = parent::getTableHtml();
        
        $table_menu_html = $this->getMenuHtml();

        $data_table_html = "
            <div class=\"data_table\">
                {$table_menu_html}
                {$table_html}
                </form>
            </div>
        ";
        
        return $data_table_html;
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = parent::toTemplateArray();
        
        $template_array["{$this->name}_pagination"] = $this->getPaginationHtml();
        
        $template_array = array_merge($template_array, $this->table_form->toTemplateArray());
        
        return $template_array;
    }
    
    /**
     * Retrieves the DataTable as an array suitable for json encoding.
     *      
     * @return array
     */
    public function toJsonArray() {
        $json_array = array(
            'pagination' => $this->getPaginationHtml(false),
            'body' => $this->getBodyHtml(false),
            'header' => $this->getHeaderHtml(false, true),
            'sort_column' => $this->current_sort_column,
            'sort_direction' => $this->current_sort_direction,
            'page_number' => $this->current_page
        );
        
        return $json_array;
    }
}