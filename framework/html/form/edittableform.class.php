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

namespace Framework\Html\Form;

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
     * Initializes a new instance of EditTableForm.
     *      
     * @param string $table_name The name of the edit table.
     * @param string $edit_table_name The name of the database table to attach the edit table to.
     * @param string $table_id_field The name of the primary key field in the attached database table.
     * @param string $table_sort_field The name of the sort field in the attached database table.
     * @param array $page_filter_columns (optional) The base filter criteria of the edit table recordset.
     * @return void
     */
    public function __construct($table_name, $edit_table_name, $table_id_field, $table_sort_field, $page_filter_columns = array()) {
        parent::__construct($table_name, $edit_table_name, page()->getPageName(), $table_id_field, $table_sort_field);
        
        $this->page_filter_columns = $page_filter_columns;
        
        $this->edit_form = new TableForm("{$this->name}_form");
        
        $request_table_name = request()->get->table;
        
        if($this->request_table_name != $this->name) {        
            $this->edit_form->setAction(Http::getPageUrl(array(
                'table' => $this->name,
                'token' => $this->request_token,
                'action' => 'add'
            )));
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
     * Setups up a form for editing a record and processes its submission.
     *
     * @return void
     */
    public function processForm() {    
        if($this->edit_form->wasSubmitted()) {
            if($this->edit_form->isValid()) {
                $this->setPageFilter($this->page_filter_columns);
            
                $form_data = $this->edit_form->getData(true);
    
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
                $interactive_fields = $this->edit_form->getInteractiveFields();
                
                $edit_table_record = db()->getRow("
                    SELECT
                        " . implode(', ', array_keys($interactive_fields)) . "
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
}