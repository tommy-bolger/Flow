<?php
/**
* Enables the manipulation of a table in the database via a table on a web page and a form on another.
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

use \Framework\Html\Form\Form;
use \Framework\Html\Form\Fields\Dropdown;
use \Framework\Utilities\Http;

class EditTable
extends Table {
    /**
    * @var string The class name of the page to add/edit records for the table.
    */
    private $edit_page;

    /**
    * @var string The name of the table in the database that the edit table will attach to.
    */
    protected $edit_table_name;
    
    /**
    * @var string The name of the primary key field of the database table.
    */
    protected $table_id_field;
    
    /**
    * @var string The name of the sort field of the database table.
    */
    protected $table_sort_field;
    
    /**
    * @var array The fixed filter criteria for the page to make the edit table work with a limited result set of the attached database table.
    */
    protected $page_filter = array();
    
    /**
    * @var array The filter criteria to make the edit table work with a limited result set of the attached database table.
    */
    protected $record_filter = array();
    
    /**
    * @var string A SQL where clause of the record filter.
    */
    protected $record_filter_sql;
    
    /**
    * @var array The record filter minus NULL values for use as query placeholder values.
    */
    protected $filter_placeholder_values = array();
    
    /**
    * @var object The filter dropdown form used to limit edit table records.
    */
    protected $record_filter_form;
    
    /**
    * @var integer The index number of the selected record filter submitted by the record filter form.
    */
    protected $selected_filter;
    
    /**
    * @var string The name of the current table GET parameter. This property and the edit_table_name property must match for the edit table to process actions.
    */
    protected $request_table_name;
    
    /**
    * @var string The name of the action to execute on the bound database table such as add, delete, edit, etc.
    */
    protected $action;
    
    /**
    * @var mixed The id of the record having an action performed on it.
    */
    protected $record_id;

    /**
     * Initializes a new instance of EditTable.
     *      
     * @param string $table_name The name of the edit table.
     * @param string $edit_table_name The name of the database table to attach the edit table to.
     * @param string $edit_page The name of the record add/edit page.
     * @param string $table_id_field The name of the primary key field in the attached database table.
     * @param string $table_sort_field The name of the sort field in the attached database table.
     * @param array $page_filter_columns (optional) The base filter criteria of the edit table recordset.
     * @param $filter_options (optional) The available record filters selectable in a dropdown. The format is array(dropdown_name => array(filter_display_name => array(filter_column_name => filter_column_value)))
     * @return void
     */
    public function __construct($table_name, $edit_table_name, $edit_page, $table_id_field, $table_sort_field, $page_filter_columns = array(), $filter_options = array()) {
        parent::__construct($table_name);
        
        //Retrieve the table included in the _GET request if one exists
        $this->request_table_name = request()->get->table;
        
        $this->setEditTableName($edit_table_name);
        $this->setEditPage($edit_page);
        $this->setTableIDField($table_id_field);
        $this->setTableSortField($table_sort_field);
        $this->setPageFilter($page_filter_columns);
        
        if(!empty($filter_options)) {
            $this->addFilterOptions(key($filter_options), current($filter_options));
        }
        
        $this->processAction();
    }
    
    /**
     * Sets the max number of display columns.
     *      
     * @param integer $number_of_columns The max number of display columns.
     * @return void
     */
    public function setNumberOfColumns($number_of_columns) {
        $this->number_of_columns = ($number_of_columns + 1);
    }
    
    /**
     * Sets the name of the database table to attach the edit table to.
     *
     * @param string $edit_table_name The name of the database table.
     * @return void
     */
    private function setEditTableName($edit_table_name) {
        $this->edit_table_name = trim($edit_table_name);
    }
    
    /**
     * Sets the name of the record add/edit page.
     *
     * @param string $edit_page The name of the edit page.
     * @return void
     */
    private function setEditPage($edit_page) {
        $this->edit_page = trim($edit_page);
    }
    
    /**
     * Sets the name of the database table primary key.
     *
     * @param string $id_field The name of the primary key.
     * @return void
     */
    private function setTableIDField($id_field) {
        $this->table_id_field = trim($id_field);
    }
    
    /**
     * Sets the name of the database table sort field.
     *
     * @param string $table_sort_field The name of the sort field.
     * @return void
     */
    private function setTableSortField($table_sort_field) {
        $this->table_sort_field = trim($table_sort_field);
    }
    
    /**
     * Sets the base page filter criteria.
     *
     * @param array $filter_columns The page filter criteria.
     * @return void
     */
    private function setPageFilter($filter_columns) {
        assert('is_array($filter_columns)');
        
        if(!empty($filter_columns)) {
            request()->get->setRequired($filter_columns);
            
            $page_filter = array();
            
            foreach($filter_columns as $filter_column) {
                $page_filter[$filter_column] = request()->get->$filter_column; 
            }
            
            $this->page_filter = $page_filter;
            
            $this->setRecordFilter($page_filter);
        }
    }
    
    /**
     * Adds additional criteria to edit table record filter.
     *
     * @param array $filter_columns The record filter filter criteria.
     * @return void
     */
    private function setRecordFilter($filter_columns) {
        assert('!empty($filter_columns) && is_array($filter_columns)');
    
        $this->record_filter = array_merge($this->record_filter, $filter_columns);
        
        foreach($filter_columns as $column_name => $column_value) {
            if(!is_null($column_value)) {
                $this->filter_placeholder_values[] = $column_value;
            }
        }
    }
    
    /**
     * Adds record filter options for the dropdown.
     *
     * @param string $filter_field_name The label of the record filter dropdown.
     * @param array $filter_options The options for the record filter dropdown.     
     * @return void
     */
    private function addFilterOptions($filter_field_name, $filter_options) {
        assert('is_array($filter_options)');

        if(!empty($filter_options)) {            
            $this->record_filter_form = new Form("{$this->name}_filter_form", Http::getPageUrl(), 'post', false);
            
            $filter_options_name = "{$this->name}_filter_dropdown";
            
            $filter_dropdown = new Dropdown($filter_options_name, $filter_field_name, array_keys($filter_options));
            $filter_dropdown->addBlankOption();
            $this->record_filter_form->addField($filter_dropdown);
            
            $this->record_filter_form->addSubmit("{$this->name}_filter_submit", 'Submit');
            
            if($this->record_filter_form->wasSubmitted() && $this->record_filter_form->isValid()) {
                $form_data = $this->record_filter_form->getData();
                
                $selected_filter = $form_data[$filter_options_name];
                
                $filter_option_values = array_values($filter_options);

                if(isset($filter_option_values[$selected_filter])) {
                    $this->selected_filter = $selected_filter;
                
                    $this->setRecordFilter($filter_option_values[$this->selected_filter]);
                }
            }
            else {
                if(request()->get->table == $this->request_table_name) {
                    $selected_filter = request()->get->filter;
                    
                    $filter_option_values = array_values($filter_options);
                    
                    if(isset($filter_option_values[$selected_filter])) {
                        $filter_dropdown->setValue($selected_filter);
                    
                        $this->selected_filter = $selected_filter;
                        
                        $this->setRecordFilter($filter_option_values[$this->selected_filter]);
                    }
                }
            }
        }
    }
    
    /**
     * Executes and processes the edit table record action such as add, edit, delete, etc.
     *   
     * @return void
     */
    private function processAction() {    
        if($this->request_table_name == $this->name) {
            $required_parameters = array('action');
            
            //Do a first switch to determine when to set the table id field as required
            switch($this->request_table_name) {
                case 'move_up':
                case 'move_down':
                case 'delete':
                case 'edit':
                    $required_parameters[] = $this->table_id_field;
                    break;
            }
        
            request()->get->setRequired($required_parameters);
            
            $table_id_field = $this->table_id_field;
        
            $this->action = request()->get->action;
            $this->record_id = request()->get->$table_id_field;
            
            switch($this->action) {
                case 'move_up':
                case 'move_down':                
                    $this->moveRecord($this->record_id, $this->action);
                    break;
                case 'delete':
                    $delete_sort_order = db()->getOne("
                        SELECT {$this->table_sort_field}
                        FROM {$this->edit_table_name}
                        WHERE {$this->table_id_field} = ?
                    ", array($this->record_id));
                    
                    if(!empty($delete_sort_order)) {
                        db()->delete($this->edit_table_name, array($this->table_id_field => $this->record_id));
                        
                        $sort_order_where_clause = '';
                        
                        if(!empty($this->record_filter)) {
                            $sort_order_where_clause = db()->generateWhereClause($this->record_filter) . " AND";
                        }
                        else {
                            $sort_order_where_clause = 'WHERE ';
                        }
                        
                        $sort_order_where_clause .= " {$this->table_sort_field} > ?";
                        
                        $update_sort_filter = $this->filter_placeholder_values;
                        $update_sort_filter[] = $delete_sort_order;
                        
                        //Subtract the sort order of all records that come after the deleted record
                        db()->query("
                            UPDATE {$this->edit_table_name}
                            SET {$this->table_sort_field} = {$this->table_sort_field} - 1
                            {$sort_order_where_clause}
                        ", $update_sort_filter);
                    }
                    else {
                        throw new \Exception("Record id '{$this->table_id_field}' in table '{$this->edit_table_name}' has already been deleted.");
                    }
                    break;
            }
        }
    }
    
    /**
     * Increments or decrements a record's sort order in the attached database table.
     * 
     * @param mixed $record_id The value of the attached table primary key.
     * @param string $direction The direction that the record's sort order will move. Can either be 'move_up' or 'move_down'.          
     * @return void
     */
    private function moveRecord($record_id, $direction) {
        $current_sort_order = db()->getOne("
            SELECT {$this->table_sort_field}
            FROM {$this->edit_table_name}
            WHERE {$this->table_id_field} = ?
        ", array($record_id));
        
        if(!empty($current_sort_order) && is_numeric($current_sort_order)) {
            $target_sort_order = NULL;
        
            switch($direction) {
                case 'move_up':
                    if($current_sort_order > 1) {
                        $target_sort_order = $current_sort_order - 1;
                    }
                    break;
                case 'move_down':
                    $target_sort_order = $current_sort_order + 1;
                    break;
            }
            
            if(!empty($target_sort_order)) {
                //Move the record with the target sort order
                $move_query = "
                    UPDATE {$this->edit_table_name}
                    SET {$this->table_sort_field} = ?
                ";
                
                $move_query .= db()->generateWhereClause(($this->record_filter + array($this->table_sort_field => $target_sort_order)));
                
                $move_query_placeholders = $this->filter_placeholder_values;
                array_unshift($move_query_placeholders, $current_sort_order);
                array_push($move_query_placeholders, $target_sort_order);

                $affected_count = db()->query($move_query, $move_query_placeholders);
                
                if($affected_count > 0) {
                    //Move the current record to the target sort order
                    $affected_count = db()->update($this->edit_table_name, array(
                        $this->table_sort_field => $target_sort_order
                    ), array(
                        $this->table_id_field => $record_id
                    ));
                }
            }
        }
    }
    
    /**
     * Generates a url with the appropriate query string for use within the edit table.
     * 
     * @param strin $action (optional) The record action currently being performed.
     * @param mixed $id (optional) The value of the attached table primary key.
     * @return void
     */
    private function generateTableLink($action = '', $id = '') {
        $page_table_parameters = '';
        
        switch($action) {
            case 'edit':
            case 'add':
                assert('!empty($this->edit_page)');
            
                $page_table_parameters = Http::getCurrentBaseUrl() . $this->edit_page;
                break;
            default:
                $page_table_parameters = Http::getPageUrl();
                break;
        }
        
        $page_table_parameters .= "&table={$this->name}";
        
        $action_parameter = '';
        
        if(!empty($action)) {
            $action_parameter = "&action={$action}";
        }
        
        $table_id_parameter = '';
        
        if(!empty($id)) {
            $table_id_parameter = "&{$this->table_id_field}={$id}";
        }
        
        $filter_parameter = '';
        
        if(isset($this->selected_filter)) {
            $filter_parameter = "&filter={$this->selected_filter}";
        }
        
        $page_filter_parameters = '';
        
        if(isset($this->page_filter)) {
            $page_filter_parameters = http_build_query($this->page_filter);
        }
        
        return $page_table_parameters . $action_parameter . $table_id_parameter . $filter_parameter . "&{$page_filter_parameters}";
    }
    
    /**
     * Adds a row to the edit table.
     *      
     * @param array $row The record to add.
     * @param string $group_name (optional) The name of the table body this record belongs to.     
     * @return void
     */
    public function addRow($row, $group_name = '') {
        assert('is_array($row)');
        
        if(empty($this->edit_page)) {
            throw new \Exception('The edit page has not been specified.');
        }
        
        $edit_id_value = NULL;
        
        if(isset($row[$this->table_id_field])) {
            $edit_id_value = $row[$this->table_id_field];
            
            unset($row[$this->table_id_field]);
        }
        else {
            throw new \Exception('The edit id field does not exist in the table body result set.');
        }
        
        $current_row = "
            <div style=\"width: 100px;\">
                <div style=\"float: left;\">
                    <a href=\"{$this->generateTableLink('edit', $edit_id_value)}\">Edit</a>
                    <br />
                    <br />
                    <a href=\"{$this->generateTableLink('delete', $edit_id_value)}\">Delete</a>
                </div>
        ";
        
        if(!isset($this->record_filter_form) || (isset($this->record_filter_form) && isset($this->selected_filter))) {
            $current_row .= "
                <div style=\"float: left; margin-left: 10px;\">
                    <a href=\"{$this->generateTableLink('move_up', $edit_id_value)}\">Up</a>
                    <br />
                    <br />
                    <a href=\"{$this->generateTableLink('move_down', $edit_id_value)}\">Down</a>
                </div>
            ";
        }
        
        $current_row .= "
                    <div class=\"clear\"></div>
                </div>
        ";
        
        if(!empty($this->number_of_columns)) {
            $row = array_slice($row, 0, ($this->number_of_columns - 1));
        }
        
        $row[] = $current_row;
        
        parent::addRow($row, $group_name);
    }
    
    /**
     * Uses a specified SQL query to populate the records for this edit table.
     *      
     * @param string $query The SQL query to retrieve the records from.
     * @param array $query_placeholders The placeholder values for the specified query.     
     * @return void
     */
    public function useQuery($query, $query_placeholders = array(), $processor_function = NULL) {    
        if(!empty($this->record_filter)) {
            $this->record_filter_sql = db()->generateWhereClause($this->record_filter);

            $order_by_position = stripos($query, 'ORDER');
        
            if($order_by_position !== false) {
                $query_split = str_split($query, $order_by_position);
                
                $query = $query_split[0] . " {$this->record_filter_sql} " . $query_split[1];
            }
            else {
                $query .= $this->record_filter_sql;
            }
        }
    
        if(stripos($query, 'ORDER BY') === false) {
            $query .= "\nORDER BY {$this->table_sort_field} ASC";
        }
        
        parent::useQuery($query, $this->filter_placeholder_values, $processor_function);
    }
    
    /**
     * Renders and retrieves the edit table's html.
     *      
     * @return string
     */
    public function toHtml() {
        $edit_page_url = Http::getCurrentBaseUrl() . $this->edit_page;
    
        $edit_table_html = "";
        
        if(!isset($this->record_filter_form) || (isset($this->record_filter_form) && isset($this->selected_filter))) {
            $edit_table_html .= "
                <span style=\"float: left;\">
                    <a href=\"{$this->generateTableLink('add')}\">+ Add a New Record</a>
                </span>
            ";
        }
        
        if(isset($this->record_filter_form)) {
            $form_template = $this->record_filter_form->toTemplateArray();

            $edit_table_html .= "
                <span style=\"float: right;\">
                    {$form_template["{$this->name}_filter_form_open"]}
                        {$form_template["{$this->name}_filter_dropdown_label"]} {$form_template["{$this->name}_filter_dropdown"]}
                        {$form_template["{$this->name}_filter_submit"]}
                    </form>
                </span>
            ";
        }
        
        $edit_table_html .= "<div class=\"clear\"></div>";
        
        if(!empty($this->child_elements)) {
            $edit_table_html .= parent::toHtml();
        }
        
        return $edit_table_html;
    }
}