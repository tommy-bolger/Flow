<?php
/**
* Allows the rendering of a <table> tag with headers, rows, and columns dynamically.
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

use \Framework\Html\Element;

class Table
extends Element {
    /**
    * @var string The name of the table.
    */
    protected $name;
    
    /**
    * @var integer The maximum number of display columns.
    */
    protected $number_of_columns;
    
    /**
    * @var boolean A flag that enables data records display empty cells for missing columns up to the max number of columns.
    */
    private $pad_rows = false;
    
    /**
    * @var array The header row(s) of the table. Becomes the thead tag(s) when rendered.
    */
    protected $header = array();
    
    /**
    * @var array The footer row(s) of the table. Becomes the tfoot tag(s) when rendered.
    */
    protected $footer = array();

    /**
     * Initializes a new instance of Table.
     *      
     * @param string $table_name The table's name.
     * @param array $rows (optional) The records to display on this table.
     * @param array $header (optional) The header row(s) to display on this table.
     * @param array $footer (optional) The footer row(s) to display on this table.
     * @return void
     */
    public function __construct($table_name, $rows = array(), $header = array(), $footer = array()) {
        $this->name = $table_name;
    
        parent::__construct("table", array('id' => $table_name));
        
        $this->addHeaderRows($header);
        
        $this->addFooterRows($footer);
        
        $this->addRows($rows);
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        $this->addCssFile('framework/Table.css');
    }
    
    /**
     * Sets the max number of display columns.
     *      
     * @param integer $number_of_columns The max number of display columns.
     * @return void
     */
    public function setNumberOfColumns($number_of_columns) {
        $this->number_of_columns = $number_of_columns;
    }
    
    /**
     * Sets the the table to pad rows.
     * 
     * @return void
     */
    public function padRows() {
        $this->pad_rows = true; 
    }
    
    /**
     * Adds a table header row.
     *      
     * @param array|string $header The columns for this header. Can either be an array for each column of the table or a string as a cell that spans all columns of the table.
     * @return void
     */
    public function addHeader($header) {
        if(!empty($header)) {
            $this->header[] = $header;
        }
    }
    
    /**
     * Adds several table header rows.
     *      
     * @param array $header_rows The header rows to add. Each row is an array of header columns.
     * @return void
     */
    public function addHeaderRows($header_rows) {
        assert('is_array($header_rows)');
    
        if(!empty($header_rows)) {
            foreach($header_rows as $header_row) {
                $this->addHeader($header_row);
            }
        }
    }
    
    /**
     * Adds a table footer row.
     *      
     * @param array $footer The columns for this footer.
     * @return void
     */
    public function addFooter($footer) {
        if(!empty($footer)) {
            $this->footer[] = $footer;
        }
    }
    
    /**
     * Adds several table footer rows.
     *      
     * @param array $footer_rows The footer rows to add. Each row is an array of footer columns.
     * @return void
     */
    public function addFooterRows($footer_rows) {
        assert('is_array($footer_rows)');
    
        if(!empty($footer_rows)) {
            foreach($footer_rows as $footer_row) {
                $this->addFooter($footer_row);
            }
        }
    }
    
    /**
     * Adds a table body row.
     *      
     * @param array $row The record to add.
     * @param string $group_name (optional) The name of the table body this record belongs to.     
     * @return void
     */
    public function addRow($row, $group_name = '') {
        assert('is_array($row)');
        
        if(!empty($row)) {
            if(empty($group_name)) {
                $group_name = 'default_body';
            }
            
            $this->child_elements[$group_name][] = $row;
        }
    }
    
    /**
     * Adds several table body rows.
     *      
     * @param array $rows The records to add.
     * @param string $group_name (optional) The name of the table body the specified records belong to.     
     * @return void
     */
    public function addRows($rows, $group_name = '') { 
        if(!empty($rows)) {
            assert('is_array($rows)');
        
            foreach($rows as $row) {
                $this->addRow($row, $group_name);
            }
        }
    }
    
    /**
     * Uses a specified SQL query to populate the records for this table.
     *      
     * @param string $query The SQL query to retrieve the records from.
     * @param array $query_placeholders The placeholder values for the specified query.     
     * @return void
     */
    public function useQuery($query, $query_placeholders = array(), $processor_function = NULL) {
        assert('is_array($query_placeholders)');

        $query_rows = db()->getAll($query, $query_placeholders);
        
        if(is_callable($processor_function)) {
            $query_rows = $processor_function($query_rows);
        }
        
        $this->addRows($query_rows);
    }
    
    /**
     * Renders and retrieves the html of the columns of a table row.
     * 
     * @param array $row The columns of the row to render.
     * @param boolean $is_header_row Tells the function to render <th> tags if true or <td> tags if false.      
     * @return string
     */
    protected function getColumnsHtml($row, $is_header_row = false) {
        $opening_tag = '';
        $closing_tag = '';
        
        if(!$is_header_row) {
            $opening_tag = '<td class="table_body"';
            $closing_tag = '</td>';
        }
        else {
            $opening_tag = '<th class="table_header"';
            $closing_tag = '</th>';
        }
        
        $columns_html = '';
    
        if(!empty($row)) {
            foreach($row as $column) {
                $span_attributes = '';
                $column_contents = '';
            
                if(!is_array($column)) {
                    $column_contents = $column;
                }
                else {
                    if(!empty($column['colspan'])) {
                        $span_attributes .= " colspan=\"{$column['colspan']}\"";
                    }
                    
                    if(!empty($column['rowspan'])) {
                        $span_attributes .= " rowspan=\"{$column['rowspan']}\"";
                    }
                    
                    $column_contents = $column['contents'];
                }
                
                $columns_html .= "
                    {$opening_tag}{$span_attributes}>
                        {$column_contents}
                    {$closing_tag}
                ";
            }
        }
        
        return $columns_html;
    }
    
    /**
     * Renders and retrieves the table's header html.
     *      
     * @return string
     */
    protected function getHeaderHtml() {
        $header_html = "";
    
        if(!empty($this->header)) {
            $header_html .= '<thead>';
        
            foreach($this->header as $header_row) {
                $header_html .= '<tr>';
            
                if(is_array($header_row)) {
                    $header_html .= $this->getColumnsHtml($header_row, true);
                }
                else {
                    assert('isset($this->number_of_columns)');
                
                    $header_html .= "<th class=\"table_header\" colspan=\"{$this->number_of_columns}\">{$header_row}</th>"; 
                }
                
                $header_html .= '</tr>';
            }
            
            $header_html .= '</thead>';
        }
        
        return $header_html;
    }
    
    /**
     * Renders and retrieves the table's footer html.
     *      
     * @return string
     */
    protected function getFooterHtml() {
        $footer_html = '';
    
        //Render the footer if specified
        if(!empty($this->footer)) {
            $footer_html .= '<tfoot>';
            
            foreach($this->footer as $footer_row) {
                $footer_html .= '<tr>';
                
                if(is_array($footer_row)) {
                    $footer_html .= '<th class="table_footer">' . implode('</th><th class="table_footer">', $footer_row) . '</th>';
                }
                else {
                    assert('isset($this->number_of_columns) //Number of columns for this table has not been set.');
                
                    $footer_html .= "<th class=\"table_footer\" colspan=\"{$this->number_of_columns}\">{$footer_row}</th>";
                }
                
                $footer_html .= '</tr>';
            }
            
            $footer_html .= '</tfoot>';
        }
        
        return $footer_html;
    }
    
    /**
     * Renders and retrieves the table's body html.
     *      
     * @return string
     */
    protected function getBodyHtml() {
        $body_html = '';
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $body_name => $body_rows) {
                if(!empty($body_rows)) {
                    $body_html .= "<tbody id=\"{$body_name}\">";
                    
                    foreach($body_rows as $row) {
                        if($this->pad_rows) {
                            assert('isset($this->number_of_columns)');

                            $row = array_pad($row, $this->number_of_columns, '&nbsp;');
                        }

                        if(!empty($this->number_of_columns)) {                        
                            $row = array_slice($row, 0, $this->number_of_columns);
                        }

                        $body_html .= "<tr>{$this->getColumnsHtml($row)}</tr>";
                    }
                    
                    $body_html .= '</tbody>';
                }
            }
        }
        
        return $body_html;
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function getTableHtml() {
        $table_html = "<table{$this->renderAttributes()}>";
        
        $table_html .= $this->getHeaderHtml();
        
        $table_html .= $this->getFooterHtml();
        
        $table_html .= $this->getBodyHtml();
                
        $table_html .= '</table>';
        
        return $table_html;
    }
    
    /**
     * Retrieves the table as an array suitable for a template.
     *      
     * @return array
     */
    public function toTemplateArray() {
        return array(
            "{$this->name}_open" => "<table{$this->renderAttributes()}>",
            "{$this->name}_header" => $this->getHeaderHtml(),
            "{$this->name}_body" => $this->getBodyHtml(),
            "{$this->name}_footer" => $tihs->getFooterHtml()
        );
    }
    
    /**
     * Renders and retrieves the table's html.
     *      
     * @return string
     */
    public function toHtml() {
        $table_html = '';
        
        if(isset($this->template) && $this->template->exists()) {
            $this->template->setPlaceholderValues($this->toTemplateArray());
        
            $table_html .= $this->template->parseTemplate();
        }
        else {
            $table_html = $this->getTableHtml();
        }
            
        return $table_html;
    }
}