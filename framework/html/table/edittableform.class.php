<?php
/**
* Enables the manipulation of a table in the database via a table and form on the same web page.
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
use \Framework\Utilities\Http;

class EditTableForm
extends EditTable {
    /**
    * @var object The form object used to manipulate records in the edit table.
    */
    private $edit_form;
    
    /**
    * @var boolean A flag determining if the edit form is visible on the page.
    */
    private $form_visible = false;
    
    /**
    * @var boolean A flag determining if the submitted form should be automatically processed.
    */
    private $process_submitted_form = true;
    
    /**
     * Initializes a new instance of EditTableForm.
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
    public function __construct($table_name, $edit_table_name, $table_id_field, $table_sort_field, $page_filter_columns = array(), $filter_options = array()) {
        parent::__construct($table_name, $edit_table_name, page()->getPageName(), $table_id_field, $table_sort_field, $page_filter_columns, $filter_options);
        
        $this->setFormVisibility();
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the edit form object.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        assert('isset($this->edit_form); //If this fails the form for this table is not visible so form operations are not allowed. Do a getFormVisibility() check before doing any form operations.');
    
        return call_user_func_array(array($this->edit_form, $function_name), $arguments);
    }
    
    /**
     * Determines if the edit form is visible based on the edit table action.
     *
     * @return mixed
     */
    private function setFormVisibility() {
        if($this->request_table_name == $this->name) {
            switch($this->action) {
                case 'add':
                case 'edit':
                    $allow_flag_name = "allow_{$this->action}_record";
                    
                    if($this->$allow_flag_name) {
                        $this->edit_form = new Form("{$this->name}_form");
                    
                        $this->form_visible = true;
                    }
                    break;
            }
        }
    }
    
    /**
     * Retrieves the form's visibility status.
     *
     * @return boolean
     */
    public function getFormVisibility() {
        return $this->form_visible;
    }
    
    /**
     * Disables automatic processing of the submitted record form.
     *
     * @return void
     */
    public function disableProcessSubmittedForm() {
        $this->process_submitted_form = false;
    }
    
    /**
     * Setups up a form for editing a record and processes its submission.
     *
     * @return void
     */
    public function processForm() {
        if($this->form_visible) {
            if(empty($this->header)) {
                throw new \Exception('The table header must be set prior to form processing.');
            }
        
            if($this->process_submitted_form && $this->edit_form->wasSubmitted() && $this->edit_form->isValid()) {
                $form_data = $this->edit_form->getData();
                
                if(!empty($this->record_filter)) {
                    $form_data = array_merge($form_data, $this->record_filter);
                }
                
                //Extract all field values that correspond to a table column.
                $columns_to_extract = array_flip(array_keys($this->header[0]));
                
                $table_data = array_intersect_key($form_data, $columns_to_extract);

                //Merge the record filter columns into the table data
                $table_data = array_merge($table_data, $this->record_filter);
                
                if($this->action == 'edit') {
                    db()->update($this->edit_table_name, $table_data, array($this->table_id_field => $this->record_id));
                    
                    $this->edit_form->addError('Your information has been updated.');
                }
                else {
                    $filter_where_clause = '';
                    
                    if(!empty($this->record_filter)) {
                        if(!empty($this->record_filter_sql)) {
                            $filter_where_clause = $this->record_filter_sql;
                        }
                        else {
                            $filter_where_clause = db()->generateWhereClause($this->record_filter);
                        }
                    }
                    
                    $record_filter = array();
                    
                    if(!empty($this->record_filter)) {
                        $record_filter = array_values($this->record_filter);
                    }
                    
                    $latest_sort_order = db()->getOne("
                        SELECT {$this->table_sort_field}
                        FROM {$this->edit_table_name}
                        {$filter_where_clause}
                        ORDER BY {$this->table_sort_field} DESC
                        LIMIT 1
                    ", $record_filter);
                    
                    if(!empty($latest_sort_order)) {
                        $latest_sort_order += 1;
                    }
                    else {
                        $latest_sort_order = 1;
                    }
                    
                    $table_data[$this->table_sort_field] = $latest_sort_order;
                
                    db()->insert($this->edit_table_name, $table_data);
                    
                    $this->edit_form->addError('Your information has been added.');
                    
                    $this->edit_form->reset();
                }
            }
            else {
                if($this->action == 'edit') {
                    $field_values = array_keys($this->header[0]);
                    
                    foreach($field_values as $field_index => $field_value) {
                        if(is_integer($field_value)) {
                            unset($field_values[$field_index]);
                        }
                    }

                    $table_data = db()->getRow("
                        SELECT
                            " . implode(', ', $field_values) . "
                        FROM {$this->edit_table_name}
                        WHERE {$this->table_id_field} = ?
                    ", array($this->record_id));

                    $this->edit_form->setDefaultValues($table_data);
                }
            }
        }
    }
    
    /**
     * Sets the edit form's template.
     * 
     * @param string $template_path The path to the template relative to the current theme.     
     * @return void
     */
    public function setFormTemplate($template_path) {
        $this->edit_form->setTemplate($template_path);
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function getTableHtml() {
        $edit_table_html = parent::getTableHtml();
        
        if($this->form_visible) {
            $edit_table_html .= $this->edit_form->toHtml();
        }
        
        return $edit_table_html;
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        $template_array = parent::getTemplateArray();
        
        if($this->form_visible) {
            $template_array["$this->name}_form"] = $this->edit_form->toHtml();
        }
        
        return $template_array;
    }
}