<?php
/**
* Acts as the add/edit functionality for EditTable on a separate page.
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

namespace Framework\Html\Form;

use \Framework\Core\Framework;
use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class EditTableForm
extends EditTable {
    /**
    * @var object The form object used to manipulate records in the edit table.
    */
    private $edit_form;
    
    /**
    * @var array The criteria to enforce on the record when adding or updating.
    */
    private $page_filter_columns;
    
    /**
    * @var array Fields with constant values to be inserted/updated with the current record.
    */
    private $constant_fields;
    
    /**
     * Initializes a new instance of EditTableForm.
     *      
     * @param string $table_name The name of the edit table.
     * @param string $edit_table_name The name of the database table to attach the edit table to.
     * @param string $table_id_field The name of the primary key field in the attached database table.
     * @param string $table_sort_field The name of the sort field in the attached database table.
     * @param array $page_filter_columns (optional) The base filter criteria of the edit table recordset.
     * @return void
     */
    public function __construct($table_name, $edit_table_name, $table_id_field, $table_sort_field = '', $page_filter_columns = array()) {
        $this->edit_form = new TableForm("{$table_name}_form");
    
        parent::__construct($table_name, $edit_table_name, Framework::getInstance()->getPageClassName(), $table_id_field, $table_sort_field);
        
        $this->page_filter_columns = $page_filter_columns;
        
        $request_table_name = request()->get->t;
        
        if($this->request_table_name != $this->name) {        
            $this->edit_form->setAction(Http::getPageUrl(array(
                'table' => $this->name,
                'token' => $this->request_token,
                'action' => 'add'
            ), self::$module_name));
        }
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the edit form object.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {    
        return call_user_func_array(array($this->edit_form, $function_name), $arguments);
    }
    
    /**
     * Retrieves the element inline code and files to be included with this element.
     *
     * @return array
     */
    public function getElementFiles() {
        return $this->edit_form->getElementFiles();
    }
    
    /**
     * Adds fields with a constant value to be inserted/updated with the current record.
     *
     * @param array $constant_fields The fields to add.
     * @return void
     */
    public function addConstantFields($constant_fields) {
        assert('!empty($constant_fields) && is_array($constant_fields)');
        
        $this->constant_fields = $constant_fields;
    }
    
    /**
     * Sets the name of the primary dropdown of the form so it is auto-selected on the form page.
     *
     * @param string $dropdown_field_name The name of the primary dropdown.
     * @return void
     */
    public function setPrimaryDropdown($dropdown_field_name) {        
        parent::setPrimaryDropdown($dropdown_field_name);
        
        $primary_dropdown_field = $this->edit_form->getField($dropdown_field_name);
        $primary_dropdown_field->setDefaultValue(request()->get->f);
    }
    
    /**
     * Setups up a form for editing a record and processes its submission.
     *
     * @return void
     */
    public function processForm() {
        $this->processToken();
        
        $this->processAction();
    
        if($this->edit_form->wasSubmitted()) {
            if($this->edit_form->isValid()) {
                $this->setPageFilter($this->page_filter_columns);
            
                $form_data = $this->edit_form->getData('input');
                
                if(!empty($this->constant_fields)) {
                    $form_data = array_merge($form_data, $this->constant_fields); 
                }
    
                if(!empty($this->record_filter)) {
                    $form_data = array_merge($form_data, $this->record_filter);
                }
                
                if($this->action == 'edit') {
                    db()->update($this->edit_table_name, $form_data, array($this->table_id_field => $this->record_id));
                    
                    $this->edit_form->addConfirmation('This record has been successfully updated.');
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
                    
                    if(!empty($this->table_sort_field)) {
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
                        
                        $form_data[$this->table_sort_field] = $latest_sort_order;
                    }
    
                    db()->insert($this->edit_table_name, $form_data);
                    
                    $this->edit_form->addConfirmation('This record has been successfully added.');
                    
                    $this->edit_form->reset();
                }
            }
        }
        else {
            if($this->action == 'edit') {
                $input_fields = $this->edit_form->getFieldsByGroup('input');
                
                $edit_table_record = db()->getRow("
                    SELECT
                        " . implode(', ', array_keys($input_fields)) . "
                    FROM {$this->edit_table_name}
                    WHERE {$this->table_id_field} = ?
                ", array($this->record_id));
                
                $this->edit_form->setDefaultValues($edit_table_record);
            }
        }
    }
    
    /**
     * Sets the edit form's template.
     * 
     * @param string $template_path The path to the template relative to the current theme.     
     * @return void
     */
    public function setTemplate($template_path) {
        $this->edit_form->setTemplate($template_path);
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        return $this->edit_form->toTemplateArray();
    }
    
    /**
     * Renders and retrieves this object's html.
     *
     * @return string The rendered edit table form.
     */
    public function toHtml() {
        return $this->edit_form->toHtml();
    }
    
    /**
     * Retrieves the DataTable as an array suitable for json encoding.
     *      
     * @return array
     */
    public function toJsonArray() {
        return $this->edit_form->toJsonArray();
    }
}