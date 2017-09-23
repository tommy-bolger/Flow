<?php
/**
* Allows the rendering of a dynamic form within a table.
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

use \Framework\Html\Table\Table;

class TableForm
extends Form {
    /**
    * @var object The table object to render the form in.
    */
    protected $table;
    
    /**
    * @var array The list of fields that will appear outside of the table.
    */
    protected $fields_outside_table = array();

    /**
     * Initializes a new instance of TableForm.
     *      
     * @param string $form_name The form name.
     * @param string $form_action (optional) The form submit location.
     * @param string $form_method The field method. Defaults to 'post'.
     * @param boolean $enable_token A flag to enable/disable the form token.     
     * @return void
     */
    public function __construct($form_name, $form_action = NULL, $form_method = "post", $enable_token = true) {
        $this->table = new Table("{$form_name}_table");
            
        parent::__construct($form_name, $form_action, $form_method, $enable_token);
        
        $this->table->addClass('table_form');
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        parent::addElementFiles();
        
        $this->addCssFile('framework/TableForm.css');
    }
    
    /**
     * Retrieves the element inline code and files to be included with this element.
     *
     * @return array
     */
    public function getElementFiles() {
        $element_files = parent::getElementFiles();
        
        $table_element_files = $this->table->getElementFiles();
        
        $element_files['css'] += $table_element_files['css'];
        $element_files['javascript'] += $table_element_files['javascript'];
        $element_files['inline_javascript'] += $table_element_files['inline_javascript'];
        
        return $element_files;
    }
    
    /**
     * Sets the header text for the table header.
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title) {
        $this->table->addHeader($title);
    }
    
    /**
     * Sets the fields that will appear inside the form but will not be a row of the table.
     * 
     * @param array $fields_outside_table An array of field names.
     * @return void
     */
    public function setFieldsOutsideTable(array $fields_outside_table) {        
        $this->fields_outside_table = $fields_outside_table;
    }
    
    /**
     * Adds all form fields to the table.
     *             
     * @return void
     */
    protected function addFieldsToTable() {
        //Form column count without descriptions
        $column_count = 2;
    
        if(!empty($this->child_elements)) {            
            $fields_outside_table = array();
            
            if(!empty($this->fields_outside_table)) {
                $fields_outside_table = array_flip($this->fields_outside_table);
            }
        
            foreach($this->child_elements as $field_index => $form_field) {
                if(property_exists($form_field, "IS_FORM_FIELD")) {
                    $field_tag = $form_field->getInputType();
                    $field_name = $form_field->getName();
                    
                    if($field_tag != 'hidden' && !isset($fields_outside_table[$field_name])) {
                        $row = array();
                    
                        if($form_field->hasLabel()) {
                            $row[] = $form_field->getLabelHtml();
                            $row[] = $form_field->getFieldHtml();
                            
                            $field_description = $form_field->getDescription();
                            
                            if(!empty($field_description)) {
                                $row[] = $field_description;

                                $column_count = 3;
                            }
                            else {
                                if($column_count == 3) {
                                    $row[] = '&nbsp;';
                                }
                            }
                        }
                        else {
                            $row[] = array(
                                'colspan' => $column_count,
                                'contents' => $form_field->getFieldHtml()
                            );
                        }
                    
                        $this->table->addRow($row);
                        
                        unset($this->child_elements[$field_index]);
                    }
                }
            }
        }

        $this->table->setNumberOfColumns($column_count);
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {    
        return array_merge($this->toTemplateArray(), $this->table->toTemplateArray());
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function toHtml() {
        $this->addFieldsToTable();
    
        if(empty($this->template)) {
            $this->child_elements['form_table'] = $this->table->toHtml();
        }
        
        return parent::toHtml();
    }
}