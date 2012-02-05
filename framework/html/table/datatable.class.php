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
use \Framework\Html\Form\Fields\Submit;

class DataTable
extends Table {
    /**
    * @var array The table's name in array format for use in generating a query string.
    */
    protected $url_query_parameter;

    /**
    * @var object The form of the table that handles various display options.
    */
    protected $table_form;

    /* Table configuration properties */
    
    /**
    * @var array A list of columns that can be sorted in the table.
    */
    protected $sort_column_options;
    
    /**
    * @var string The default column to sort the results by.
    */
    protected $default_sort_columns;
    
    /**
    * @var array The list of options of the number of rows to display per page.
    */
    protected $rows_per_page_options;
    
    /**
    * @var integer The default number of rows to display per page.
    */
    protected $default_rows_per_page = 10;
    
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
    * @var integer The total number of records in the resultset.
    */
    protected $total_number_of_records;

    /**
     * Initializes a new instance of DataTable.
     *      
     * @param string $table_name The table's name.
     * @param array $rows (optional) The records to display on this table.
     * @param array $header (optional) The header row(s) to display on this table.
     * @param array $footer (optional) The footer row(s) to display on this table.
     * @return void
     */
    public function __construct($table_name, $rows = array(), $header = array(), $footer = array()) {
        parent::__construct($table_name, $rows, array(), $footer);
        
        $this->addHeader($header);
        
        //Create the table form        
        $this->table_form = new Form("{$table_name}_form", Http::getPageUrl(), 'post', false);
        
        $this->table_form->removeAttribute('id');
        
        $this->getTableState();
        
        $this->rows_per_page_options = array(10, 25, 50, 100);
        
        $this->url_query_parameter = array("table" => $table_name);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
    
        page()->addCssFile('framework/DataTable.css');
    }
    
    /**
     * Not used in this object.
     *      
     * @param array $header_rows
     * @return void
     */
    public function addHeaderRows($header_rows) {}
    
    /**
     * Adds a table body row.
     *      
     * @param array $row The record to add.
     * @param string $group_name Not used in this object.   
     * @return void
     */
    public function addRow($row, $group_name = '') {
        parent::addRow($row);
    }
    
    /**
     * Adds several table body rows.
     *      
     * @param array $rows The records to add.
     * @param string $group_name Not used in this object.    
     * @return void
     */
    public function addRows($rows, $group_name = '') {
        parent::addRows($rows);
    }
    
    /**
     * Retrieves the table's current view state either from a request or from the session. 
     *      
     * @return void
     */
    private function getTableState() {
        //The names of all session variables used to store the table's state
        $page_session_name = "{$this->name}_page_number";
        $sort_column_session_name = "{$this->name}_sort_column";
        $sort_order_session_name = "{$this->name}_sort_direction";
        $rows_per_page_session_name = "{$this->name}_rows_per_page";
        
        //Load the table's state from the session
        if(isset(session()->$page_session_name)) {
            $this->current_page = session()->$page_session_name;
        }
        
        if(isset(session()->$sort_column_session_name)) {
            $this->current_sort_column = session()->$sort_column_session_name;
        }
        
        if(isset(session()->$sort_order_session_name)) {
            $this->setSortDirection(session()->$sort_order_session_name);
        }
        
        if(isset(session()->$rows_per_page_session_name)) {
            $this->current_rows_per_page = session()->$rows_per_page_session_name;
        }
        
        //Retrieve any changes to the table's state from either a get request or a post request
        if(request()->get->table == $this->name) {
            //The current page
            $current_page = request()->get->getVariable('table_page', 'integer');
            
            if(!empty($current_page)) {
                $this->current_page = $current_page;
            
                session()->$page_session_name = $this->current_page;
            }
            
            //The current sort column
            $current_sort_column = request()->get->sort;
            
            if(!empty($current_sort_column)) {
                $this->current_sort_column = $current_sort_column;
            
                session()->$sort_column_session_name = $this->current_sort_column;
                
                $this->setSortDirection(request()->get->direction);
                session()->$sort_order_session_name = $this->current_sort_direction;
            }
            
            //The current number of rows to show per page
            $current_rows_per_page = request()->get->getVariable('rows', 'integer');
            
            if(!empty($current_rows_per_page)) {
                $this->current_rows_per_page = $current_rows_per_page;
                
                session()->$rows_per_page_session_name = $this->current_rows_per_page;
            }
        }
        elseif($this->table_form->wasSubmitted() && $this->table_form->isValid()) {
            //The current number of rows to show per page
            $current_rows_per_page = request()->post->getVariable("{$this->name}_rows", 'integer');
            
            if(!empty($current_rows_per_page)) {
                $this->current_rows_per_page = $current_rows_per_page;
                
                if(!isset(session()->$rows_per_page_session_name) || $this->current_rows_per_page != session()->$rows_per_page_session_name) {
                    $this->current_page = 1;
                }
                
                session()->$rows_per_page_session_name = $this->current_rows_per_page;
            }
        }

        if(empty($this->current_page)) {
            $this->current_page = 1;
            
            session()->$page_session_name = 1;
        }
    }
    
    /**
     * Sets the allowed number of rows to limit each page by.
     *      
     * @param array $rows_per_page_options
     * @return void
     */
    public function setRowsPerPageOptions($rows_per_page_options) {
        assert('is_array($rows_per_page_options && !empty($rows_per_page_options))');
        
        $this->rows_per_page_options = $rows_per_page_options;
    }
    
    /**
     * Sets the default allowed number of rows to limit each page by.
     *      
     * @param int $default_rows_per_page
     * @return void
     */
    public function setDefaultRowsPerPage($default_rows_per_page) {
        assert('is_integer($rows_per_page) && !empty($rows_per_page)');
    
        $this->default_rows_per_page = $default_rows_per_page;
    }
    
    /**
     * Sets the direction that the records will be sorted in.
     *      
     * @param string $sort_direction The sort direction. Can only be either ASC or DESC.
     * @return void
     */
    private function setSortDirection($sort_direction) {
        $sort_direction = strtoupper($sort_direction);
    
        switch($sort_direction) {
            case 'ASC':
            case 'DESC':
                $this->current_sort_direction = $sort_direction;
                break;
            default:
                $this->current_sort_direction = 'ASC';
                break;
        }
    }
    
    /**
     * Sets the columns that the resultset will be sorted by default.
     * 
     * @param string|array $sort_columns The columns to sort the resultset by.     
     * @param string $sort_direction The sort direction. Can only be either ASC or DESC.
     * @return void
     */
    public function setDefaultSortOrder($sort_columns, $direction) {
        assert('(is_string($sort_columns) || is_array($sort_columns)) && !empty($sort_columns)');
        
        $this->default_sort_columns = $sort_columns;
        
        if(empty($this->current_sort_direction)) {
            $this->setSortDirection($direction);
        }
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
     * Sets the query that will be used to populate the table.
     * 
     * @param string $query the base query of the resultset.
     * @param array $query_placeholders (optional) The values of the query placeholders.
     * @param function $processor_function (optional) The function that will perform post-processing of the resultset.       
     * @return void
     */
    public function useQuery($query, $query_placeholders = array(), $processor_function = NULL) {
        $this->total_number_of_records = db()->getOne("
            SELECT COUNT(*)
            FROM (
                {$query}
            ) AS query_record_count
        ", $query_placeholders);
        
        $sort_columns = '';

        if(!empty($this->current_sort_column)) {
            if(isset($this->sort_column_options[$this->current_sort_column])) {
                $sort_option = $this->sort_column_options[$this->current_sort_column];
                
                if(is_array($sort_option)) {
                    $sort_option = implode(', ', $sort_option);
                }
                
                $sort_columns = $sort_option;
            }
        }
        
        if(empty($sort_columns) && !empty($this->default_sort_columns)) {            
            if(!is_array($this->default_sort_columns)) {
                $sort_columns = $this->default_sort_columns;
            }
            else {
                $sort_columns = implode(', ', $this->default_sort_columns);
            }
        }
        
        if(!empty($sort_columns)) {
            $query .= "\nORDER BY {$sort_columns} {$this->current_sort_direction}";
        }
        
        if(empty($this->current_rows_per_page)) {
            $this->current_rows_per_page = $this->default_rows_per_page;
        }
    
        if(!empty($this->current_rows_per_page)) {
            if($this->current_rows_per_page > $this->total_number_of_records) {
                $this->current_page = 1;
            }
        
            $record_offset = ($this->current_page - 1) * $this->current_rows_per_page;

            $query .= "\nLIMIT {$this->current_rows_per_page} OFFSET {$record_offset}";
        }

        parent::useQuery($query, $query_placeholders, $processor_function);
    }
    
    /**
     * Renders and retrieves the rows per page options as an html dropdown.
     *  
     * @return string
     */
    protected function setRowsPerPageHtml() {
        $rows_per_page_options = array_combine($this->rows_per_page_options, $this->rows_per_page_options);
        
        $rows_per_page_dropdown = new Dropdown("{$this->name}_rows", '', $rows_per_page_options, array('data_table_rows'));
        $rows_per_page_dropdown->setDefaultValue($this->current_rows_per_page);
        $rows_per_page_dropdown->removeAttribute('id');
        
        $this->table_form->addField($rows_per_page_dropdown);
        
        $submit_button = new Submit("{$this->name}_page_rows_submit", '&gt;', array('rows_submit'));        
        $submit_button->removeAttribute('id');
        
        $this->table_form->addField($submit_button);
    }
    
    /**
     * Generates and returns a url specific to the data table.
     * 
     * @param array $query_string_parameters The query string in the following format: array('name' => 'value').    
     * @return string
     */
    protected function generateUrl($query_string_parameters) {
        $query_string_parameters = array_merge($this->url_query_parameter, $query_string_parameters);
    
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
        $page_number_start_range = ($page_number * $this->current_rows_per_page) - $this->current_rows_per_page;
            
        $page_number_end_range = $page_number_start_range + $this->current_rows_per_page;
        
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
     * @return string
     */
    protected function getPaginationHtml() {
        $total_pages = 1;
        
        if(!empty($this->current_rows_per_page)) {
            $total_pages = floor($this->total_number_of_records / $this->current_rows_per_page);
            
            //If the number of rows per page doesn't cleanly divide into the total records then add a final page.
            if(($this->total_number_of_records % $this->current_rows_per_page) > 0) {
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
        
        //Generate the pagination html for the table
        $pagination_html = "
            <div class=\"pagination\">
                <span>Page {$this->current_page} of {$total_pages}</span>
                <ul class=\"page_numbers\">
        ";
        
        //Generate the previous page and first page links if not on the first page
        if($this->current_page > 1) {
            $first_page_title = $this->generatePageLinkTitle(1, 'First page');
            $first_page_url = $this->generateUrl(array('table_page' => 1));
            
            $previous_page_title = $this->generatePageLinkTitle($previous_page_number, 'Previous page');
            $previous_page_url = $this->generateUrl(array('table_page' => $previous_page_number));
        
            $pagination_html .= "
                <li class=\"page_number\">
                    <a href=\"{$first_page_url}\" title=\"{$first_page_title}\">&laquo;First</a>
                </li>
                <li class=\"page_number\">
                    <a href=\"{$previous_page_url}\" title=\"{$previous_page_title}\">&lt;</a>
                </li>
            ";
        }
        
        //Generate a link for each page in the pagination range
        foreach($page_range as $page_number) {
            //Generate the title of the link
            $page_number_title = $this->generatePageLinkTitle($page_number);
            $page_number_url = $this->generateUrl(array('table_page' => $page_number));
        
            $current_page_class = "";
            
            $page_number_link = '';
            
            if($page_number != $this->current_page) {
                $page_number_link = "<a href=\"{$page_number_url}\" title=\"{$page_number_title}\">{$page_number}</a>";
            }
            else {
                $current_page_class = " current_page_number";
                
                $page_number_link = "<span title=\"{$page_number_title}\">{$page_number}</span>";
            }
        
            $pagination_html .= "
                <li class=\"page_number{$current_page_class}\">
                    {$page_number_link}
                </li>
            ";
        }
        
        //Generate the next page and last page links if not on the last page
        if($this->current_page < $total_pages) {
            $next_page_title = $this->generatePageLinkTitle($next_page_number, 'Next page');
            $next_page_url = $this->generateUrl(array('table_page' => $next_page_number));
            
            $last_page_title = $this->generatePageLinkTitle($total_pages, 'Last page');
            $last_page_url = $this->generateUrl(array('table_page' => $total_pages));
        
            $pagination_html .= "
                <li class=\"page_number\">
                    <a href=\"{$next_page_url}\" title=\"{$next_page_title}\">&gt;</a>
                </li>
                <li class=\"page_number\">
                    <a href=\"{$last_page_url}\" title=\"{$last_page_title}\">Last&raquo;</a>
                </li>
            ";
        }
        
        $pagination_html .= "
                </ul>
            </div>
        ";
        
        return $pagination_html;
    }
    
    /**
     * Renders and retrieves the table's header html.
     *      
     * @return string
     */
    protected function getHeaderHtml() {
        $header_html = "";
    
        if(isset($this->header[0])) {
            $table_header = $this->header[0];
            
            if(!empty($table_header)) {                    
                $header_html .= '
                    <thead>
                        <tr>
                ';
            
                foreach($table_header as $column_name => $column_display_name) {
                    $sort_direction_indicator = '';
                    $link_sort_direction = 'asc';

                    //Determine which sort direction to pass in the column's url and the visual sort indicator.
                    if($column_name == $this->current_sort_column) {
                        switch($this->current_sort_direction) {
                            case 'ASC':
                                $sort_direction_indicator = '&uarr;';
                                $link_sort_direction = 'desc';
                                break;
                            case 'DESC':
                                $sort_direction_indicator = '&darr;';
                                $link_sort_direction = 'asc';
                                break;
                        }
                    }
                    
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
                        'sort' => $column_name,
                        'direction' => $link_sort_direction
                    ));

                    $header_html .= "
                        <th class=\"table_header\">
                            <a href=\"{$sort_link_url}\" title=\"{$sort_link_title}\">{$column_display_name}</a>
                            {$sort_direction_indicator}
                        </th>
                    "; 
                }
                
                $header_html .= '
                        </tr>
                    </thead>
                ';
            }
        }
        
        return $header_html;
    }
    
    /**
     * Renders and retrieves the table's body html.
     *      
     * @return string
     */
    protected function getBodyHtml() {            
        $body_html = '';
    
        if(isset($this->child_elements['default_body'])) {
            $body_rows = $this->child_elements['default_body'];
        
            if(!empty($body_rows)) {
                $body_html .= "<tbody>";
                
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
                    
                    $body_html .= '
                        <tr>
                            <td class="table_body">' . implode('</td><td class="table_body">', $display_columns) . '</td>
                        </tr>
                    ';
                }
                
                $body_html .= '</tbody>';
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
        
        $table_menu_items = array("{$form_template["{$this->name}_rows"]} per page {$form_template["{$this->name}_page_rows_submit"]}");
        
        if(!empty($pagination_html)) {
            $table_menu_items[] = $pagination_html;
        }

        return "
            <div class=\"table_menu\">
                {$form_template["{$this->name}_form_open"]}
                    " . implode(' | ', $table_menu_items) . "
                </form>
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
                {$table_menu_html}
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
}