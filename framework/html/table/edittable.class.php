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
use \Framework\Utilities\Encryption;

class EditTable
extends DataTable {
    /**
    * @var string The name of the module to use in generated urls.
    */
    protected static $module_name;

    /**
    * @var string The class name of the page to add/edit records for the table.
    */
    protected $edit_page;
    
    /**
    * @var string The token used to validate requests and prevent csrf attacks.
    */
    protected $request_token;
    
    /**
    * @var boolean A flag to allow records to be added via the table.
    */
    protected $allow_add_record = true;
    
    /**
    * @var boolean A flag to allow records to be edited via the table.
    */
    protected $allow_edit_record = true;
    
    /**
    * @var boolean A flag to allow records to be deleted via the table.
    */
    protected $allow_delete_record = true;
    
    /**
    * @var boolean A flag to allow records to have their sort position changed via the table.
    */
    protected $allow_move_record = true;

    /**
    * @var string The name of the table in the database that the edit table will attach to.
    */
    protected $edit_table_name;
    
    /**
    * @var string The alias of the database table name for use with criteria that requires it.
    */
    protected $edit_table_alias;
    
    /**
    * @var string The name of the primary key field of the database table.
    */
    protected $table_id_field;
    
    /**
    * @var string The name of the sort field of the database table.
    */
    protected $table_sort_field;
    
    /**
    * @var integer The number of rows originally set for this table. This is used to make sure the Action column increments number_of_rows only 1 time.
    */
    protected $original_number_of_rows;
    
    /**
    * @var array The fixed filter criteria for the page to make the edit table work with a limited result set of the attached database table.
    */
    protected $page_filter = array();
    
    /**
    * @var array The filter criteria to make the edit table work with a limited result set of the attached database table.
    */
    protected $record_filter = array();
    
    /**
    * @var array The record filter minus NULL values for use as query placeholder values.
    */
    protected $filter_placeholder_values = array();
    
    /**
    * @var string The name of the filter dropdown to use to determine when to show the add, move up, and move down links.
    */
    protected $primary_dropdown_name;
    
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
    * @var string The word to display to indicate the subject of the action.
    */
    protected $subject_title = 'Record';
    
    /**
     * Sets the name of the module to include in generated urls.
     *      
     * @param string $module_name The name of the module.     
     * @return void
     */
    public static function setModuleName($module_name) {
        self::$module_name = $module_name;
    }

    /**
     * Initializes a new instance of EditTable.
     *      
     * @param string $table_name The name of the edit table.
     * @param string $edit_table_name The name of the database table to attach the edit table to.
     * @param string $edit_page The name of the record add/edit page.
     * @param string $table_id_field The name of the primary key field in the attached database table.
     * @param string $table_sort_field The name of the sort field in the attached database table.
     * @param array $page_filter_columns (optional) The base filter criteria of the edit table recordset.
     * @return void
     */
    public function __construct($table_name, $edit_table_name, $edit_page, $table_id_field, $table_sort_field = '', $page_filter_columns = array()) {
        parent::__construct($table_name, true);
        
        //Retrieve the table included in the _GET request if one exists
        $this->request_table_name = request()->get->t;
        
        $this->setEditTableName($edit_table_name);
        $this->setEditPage($edit_page);
        $this->setTableIDField($table_id_field);
        $this->setTableSortField($table_sort_field);
        $this->setPageFilter($page_filter_columns);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
    
        $this->addCssFile('framework/EditTable.css');
        $this->addJavascriptFile('EditTable.js');
        
        $this->addInlineJavascript("
            $(document).ready(function() {
                var edit_table = new EditTable($('#{$this->name}_et'));
            });
        ", $this->name);
    }
    
    /**
     * Sets the max number of display columns.
     *      
     * @param integer $number_of_columns The max number of display columns.
     * @return void
     */
    public function setNumberOfColumns($number_of_columns) {
        $this->number_of_columns = $number_of_columns;
        
        $this->original_number_of_columns = $number_of_columns;
    }
    
    /**
     * Sets the name of the database table to attach the edit table to.
     *
     * @param string $edit_table_name The name of the database table.
     * @return void
     */
    protected function setEditTableName($edit_table_name) {
        $this->edit_table_name = trim($edit_table_name);
    }
    
    /**
     * Sets the alias of the database table name.
     *
     * @param string $edit_table_alias The database table alias.
     * @return void
     */
    public function setEditTableAlias($edit_table_alias) {
        $this->edit_table_alias = trim($edit_table_alias);
    }
    
    /**
     * Sets the name of the record add/edit page.
     *
     * @param string $edit_page The name of the edit page.
     * @return void
     */
    protected function setEditPage($edit_page) {
        $this->edit_page = trim($edit_page);
    }
    
    /**
     * Sets the name of the database table primary key.
     *
     * @param string $id_field The name of the primary key.
     * @return void
     */
    protected function setTableIDField($id_field) {
        $this->table_id_field = trim($id_field);
    }
    
    /**
     * Sets the name of the database table sort field.
     *
     * @param string $table_sort_field The name of the sort field.
     * @return void
     */
    protected function setTableSortField($table_sort_field) {
        if(!empty($table_sort_field)) {
            $this->table_sort_field = trim($table_sort_field);
        }
        else {
            $this->disableMoveRecord();
        }
    }
    
    /**
     * Sets the base page filter criteria.
     *
     * @param array $filter_columns The page filter criteria.
     * @return void
     */
    protected function setPageFilter($filter_columns) {
        assert('is_array($filter_columns)');
        
        if(!empty($filter_columns)) {
            request()->setRequired($filter_columns);
            
            $page_filter = array();
            
            foreach($filter_columns as $filter_column) {
                $page_filter[$filter_column] = request()->$filter_column; 
            }
            
            $this->page_filter = $page_filter;
            
            $this->setRecordFilter($page_filter);
            
            $this->table_form->setAction(Http::getPageUrl($page_filter));
        }
    }
    
    /**
     * Adds additional criteria to edit table record filter.
     *
     * @param array $filter_columns The record filter filter criteria.
     * @return void
     */
    protected function setRecordFilter($filter_columns) {
        assert('!empty($filter_columns) && is_array($filter_columns)');
    
        $this->record_filter = array_merge($this->record_filter, $filter_columns);

        foreach($filter_columns as $column_name => $column_value) {
            if(!is_null($column_value)) {
                $this->filter_placeholder_values[] = $column_value;
            }
        }
    }
    
    /**
     * Sets the name of the primary result filter dropdown to use to determine when to show the add, move up, and move down links.
     *
     * @param string $dropdown_field_name The name of the primary dropdown.
     * @return void
     */
    public function setPrimaryDropdown($dropdown_field_name) {
        $this->primary_dropdown_name = $dropdown_field_name;
    }
    
    /**
     * Disables the ability to add records via the table.
     * 
     * @return void
     */
    public function disableAddRecord() {
        $this->allow_add_record = false;
    }
    
    /**
     * Disables the ability to edit records via the table.
     * 
     * @return void
     */
    public function disableEditRecord() {
        $this->allow_edit_record = false;
    }
    
    /**
     * Disables the ability to delete records via the table.
     * 
     * @return void
     */
    public function disableDeleteRecord() {
        $this->allow_delete_record = false;
    }
    
    /**
     * Disables the ability to change a record's sort order via the table.
     * 
     * @return void
     */
    public function disableMoveRecord() {
        $this->allow_move_record = false;
    }
    
    /**
     * Sets the title used in place of 'Record' on the table's display.
     * 
     * @param string $subject_title
     * @return void
     */
    public function setSubjectTitle($subject_title) {
        $this->subject_title = $subject_title;
    }
    
    /**
     * Adds a table header row.
     *      
     * @param array|string $header The columns for this header. Can either be an array for each column of the table or a string as a cell that spans all columns of the table.
     * @return void
     */
    public function setHeader($header) {
        if(empty($this->header)) {
            $header[] = 'Action';
        }
        
        parent::setHeader($header);
    }
    
    /**
     * Adds a token or validates the token to a session token to prevent CSRF attacks.
     *      
     * @return void
     */
    protected function processToken() {
        $token_name = "{$this->name}_token";

        if($this->request_table_name != $this->name) {
            $this->request_token = session()->$token_name;
                        
            if(empty($this->request_token)) {
                $this->request_token = substr(Encryption::generateShortHash(), 0, 10);

                //Add the token to the session
                session()->$token_name = $this->request_token;
            }
        }
        else {
            $this->request_token = request()->get->tk;
        
            if(empty(session()->$token_name)) {
                throw new \Exception("Token '{$token_name}' for table '{$this->name}' does not exist in the session.");
            }

            if(session()->$token_name != $this->request_token) {
                throw new \Exception("Token '{$token_name}' for table '{$this->name}' does not match up with session token. A possible CSRF attack was attempted.");
            }
        }
    }
    
    /**
     * Executes and processes the edit table record action such as add, edit, delete, etc.
     *   
     * @return void
     */
    protected function processAction() {    
        $this->action = request()->get->a;
    
        if($this->request_table_name == $this->name && !empty($this->action)) {
            $required_parameters = array();
            
            //Do a first switch to determine when to set the table id field as required
            switch($this->action) {
                case 'move_up':
                case 'move_down':
                case 'delete':
                case 'edit':
                    $required_parameters[] = 'id';
                    break;
            }
            
            if(!empty($required_parameters)) {
                request()->get->setRequired($required_parameters);
            }                        
                        
            $this->record_id = request()->get->id;
            
            switch($this->action) {
                case 'move_up':
                case 'move_down':
                    if($this->allow_move_record) {                    
                        $this->moveRecord($this->record_id, $this->action);
                    }
                    break;
                case 'delete':
                    if($this->allow_delete_record) {
                        $record_exists = true;
                        
                        if(!empty($this->table_sort_field)) {
                            $delete_sort_order = db()->getOne("
                                SELECT {$this->table_sort_field}
                                FROM {$this->edit_table_name}
                                WHERE {$this->table_id_field} = ?
                            ", array($this->record_id));

                            if(!empty($delete_sort_order)) {
                                $sort_order_where_clause = 'WHERE ';
                                
                                //Add the primary dropdown field as the first criteria if it exists
                                if(!empty($this->selected_filter_criteria[$this->primary_dropdown_name])) {
                                    $sort_order_where_clause .= $this->selected_filter_criteria[$this->primary_dropdown_name]['criteria'] . " AND";
                                }
                                
                                //Add any other filter criteria managed by this instance
                                if(!empty($this->record_filter)) {
                                    $sort_order_where_clause .= str_replace('WHERE', '', db()->generateWhereClause($this->record_filter)) . " AND";
                                }
                                
                                $sort_order_where_clause .= " {$this->table_sort_field} > ?";

                                $update_sort_filter = $this->filter_placeholder_values;
                                $update_sort_filter[] = $delete_sort_order;
                                
                                //Subtract the sort order of all records that come after the deleted record
                                db()->query("
                                    UPDATE {$this->edit_table_name} {$this->edit_table_alias}
                                    SET {$this->table_sort_field} = {$this->table_sort_field} - 1
                                    {$sort_order_where_clause}
                                ", $update_sort_filter);
                            }
                            else {
                                $record_exists = false;
                            }
                        }
                        else {
                            $record_count = db()->getOne("
                                SELECT COUNT(*)
                                FROM {$this->edit_table_name}
                                WHERE {$this->table_id_field} = ?
                            ", array($this->record_id));
                            
                            if(empty($record_count)) {
                                $record_exists = false;
                            }
                        }
                        
                        if($record_exists) {
                            db()->delete($this->edit_table_name, array($this->table_id_field => $this->record_id));
                        }
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
    protected function moveRecord($record_id, $direction) {
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
                    UPDATE {$this->edit_table_name} {$this->edit_table_alias}
                    SET {$this->table_sort_field} = ?
                    WHERE 
                ";
                
                if(!empty($this->selected_filter_criteria[$this->primary_dropdown_name])) {
                    $move_query .= $this->selected_filter_criteria[$this->primary_dropdown_name]['criteria'] . " AND ";
                }
                
                $move_query .= str_replace('WHERE', '', db()->generateWhereClause(($this->record_filter + array($this->table_sort_field => $target_sort_order))));

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
     * Generates and returns a url specific to the data table.
     * 
     * @param array $query_string_parameters The query string in the following format: array('name' => 'value').    
     * @return string
     */
    protected function generateUrl($query_string_parameters) {
        $query_string_parameters = array_merge($this->table_state_request, $query_string_parameters);
        
        $query_string_parameters['tk'] = $this->request_token;
        
        $action = '';
        
        if(!empty($query_string_parameters['action'])) {
            $action = $query_string_parameters['action'];
            
            $query_string_parameters['a'] = $query_string_parameters['action'];
            
            unset($query_string_parameters['action']);
        }
        
        if(isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name])) {
            $query_string_parameters['f'] = $this->current_selected_filters[$this->primary_dropdown_name];
        }

        if(!empty($this->page_filter)) {
            $query_string_parameters = array_merge($query_string_parameters, $this->page_filter);
        }
        
        $table_link_url = '';
        
        switch($action) {
            case 'edit':
            case 'add':
                assert('!empty($this->edit_page)');
            
                $table_link_url = Http::getCurrentLevelPageUrl($this->edit_page, $query_string_parameters, self::$module_name);
                break;
            default:
                $table_link_url = Http::getPageUrl($query_string_parameters, self::$module_name);
                break;
        }

        return $table_link_url;
    }
    
    /**
     * Processes and retrieves data from the finalized resultset.
     * 
     * @param object $resultset An object of type ResultSet. Valid classes fall under \Framework\Data\ResultSet.
     * @return void
     */        
    public function process($resultset, $processor_function = NULL) {
        assert('is_object($resultset) && !empty($resultset)');
        
        $this->processToken();
        
        $this->processAction();

        if(!empty($this->record_filter)) {
            foreach($this->record_filter as $column_name => $column_value) {
                $column_equality = '';
                $placeholder_values = array();
            
                if(!is_null($column_value)) {
                    $column_equality = '= ?';
                    $placeholder_values[] = $column_value;
                }
                else {
                    $column_equality = 'IS NULL';
                }

                $resultset->addFilterCriteria("{$column_name} {$column_equality}", $placeholder_values);
            }
        }
        
        parent::process($resultset, $processor_function);
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
        
        $record_links = array();
        
        if($this->allow_edit_record) {
            $edit_link = $this->generateUrl(array(
                'action' => 'edit',
                'id' => $edit_id_value
            ));
        
            $record_links[] = "<a href=\"{$edit_link}\">Edit</a>";
        }
        
        if($this->allow_delete_record && (empty($this->primary_dropdown_name) || (isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name])))) {
            $delete_link = $this->generateUrl(array(
                'action' => 'delete',
                'id' => $edit_id_value
            ));
        
            $record_links[] = "<a class=\"delete\" href=\"{$delete_link}\">Delete</a>";
        }
        
        if($this->allow_move_record && (empty($this->primary_dropdown_name) || (isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name])))) {
            $move_up_link = $this->generateUrl(array(
                'action' => 'move_up',
                'id' => $edit_id_value
            ));
            
            $move_down_link = $this->generateUrl(array(
                'action' => 'move_down',
                'id' => $edit_id_value
            ));
        
            $record_links[] = "<a class=\"move move_up\" href=\"{$move_up_link}\">Up</a>";
            $record_links[] = "<a class=\"move move_down\" href=\"{$move_down_link}\">Down</a>";
        }
        
        if(!empty($record_links)) {                    
            if(!empty($this->original_number_of_columns) && $this->original_number_of_columns < count($row)) {
                $row = array_slice($row, 0, $this->original_number_of_columns); 
            }
            
            if(($this->number_of_columns - $this->original_number_of_columns) == 0) {
                $this->number_of_columns += 1;
            }
            
            $row[] = implode(' | ', $record_links);
        }
        
        parent::addRow($row, $group_name);
    }
    
    /**
     * Renders and retrieves the edit table's add link a tag.
     *      
     * @return string
     */
    protected function getAddLink() {
        $add_link = $this->generateUrl(array(
            'action' => 'add'
        ));
        
        return "<a href=\"{$add_link}\">+ Add a New {$this->subject_title}</a>";
    }
    
    /**
     * Renders and retrieves the edit table's new record link html.
     *      
     * @return string
     */
    public function getAddLinkHtml() {
        $link_html = '';

        if($this->allow_add_record) {
            $link_html = '<div class="add_link">';
            
            if(empty($this->primary_dropdown_name) || (isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name]))) {
                $link_html .= $this->getAddLink();
            }
            
            $link_html .= '</div>';
        }
        
        return $link_html;
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function getTableHtml() {
        $edit_table_html = "
            <div id=\"{$this->name}_et\" class=\"edit_table\">
                <div class=\"edit_table_bar\">
                    {$this->getAddLinkHtml()}
                    <div class=\"clear\"></div>
                </div>
        ";
        
        if(!empty($this->child_elements)) {
            $edit_table_html .= parent::getTableHtml();
        }
        
        $edit_table_html .= '</div>';
        
        return $edit_table_html;
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = parent::getTemplateArray();
        
        if($this->allow_add_record && (empty($this->primary_dropdown_name) || (isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name])))) {
            $add_link = $this->generateUrl(array(
                'action' => 'add'
            ));
        
            $template_array["add_link_open"] = "<a href=\"{$add_link}\">";
        }
        
        return $template_array;
    }
    
    /**
     * Retrieves the EditTable as an array suitable for json encoding.
     *      
     * @return array
     */
    public function toJsonArray() {
        $json_array = parent::toJsonArray();
        
        if($this->allow_add_record) {
            if((empty($this->primary_dropdown_name) || (isset($this->current_selected_filters[$this->primary_dropdown_name]) && strlen($this->current_selected_filters[$this->primary_dropdown_name])))) {
                $json_array['add_link'] = $this->getAddLink();
            }
            else {
                $json_array['add_link'] = '';
            }
        }
        
        return $json_array;
    }
}