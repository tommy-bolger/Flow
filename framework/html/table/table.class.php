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

class Table
extends \Framework\Html\Element {
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
        parent::__construct("table", array('id' => $table_name));
        
        $this->name = $table_name;
        
        $this->addHeaderRows($header);
        
        $this->addFooterRows($footer);
        
        $this->addRows($rows);
    }
    
    /**
     * Catches calls to functions not in this class and throws an exception to prevent a fatal error.
     *      
     * @param string $function_name The called function name.
     * @param array $arguments The called function arguments.
     * @return void
     */
    public function __call($function_name, $arguments) {
        throw new \Exception("Function '{$function_name}' does not exist in this class.");
    }
    
    /**
     * Adds the element's javascript and css to the page.
     *      
     * @return void
     */
    protected function addElementFiles() {
        page()->addCssFile('framework/Table.css');
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
     * @param array $header The columns for this header.
     * @return void
     */
    public function addHeader($header) {
        assert('is_array($header)');
    
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
        assert('is_array($rows)');
    
        if(!empty($rows)) {
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
                    $header_html .= '<th class="table_header">' . implode('</th><th class="table_header">', $header_row) . '</th>';
                }
                else {
                    assert('isset($this->number_of_columns) //Number of columns for this table has not been set.');
                
                    $header_html .= "<th class=\"table_header\" colspan=\"{$this->number_of_columns}\">{$header_row}</th>"; 
                }
                
                $header_html .= '</tr>';
            }
            
            $header_html .= '</thead>';
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
    
        if(!empty($this->child_elements)) {
            foreach($this->child_elements as $body_name => $body_rows) {
                if(!empty($body_rows)) {
                    $body_html .= "<tbody id=\"{$body_name}\">";
                    
                    foreach($body_rows as $row) {
                        if($this->pad_rows) {
                            assert('isset($this->number_of_columns) //Number of columns for this table has not been set.');

                            $row = array_pad($row, $this->number_of_columns, '&nbsp;');
                        }

                        if(!empty($this->number_of_columns)) {                        
                            $row = array_slice($row, 0, $this->number_of_columns);
                        }

                        $body_html .= '
                            <tr>
                                <td class="table_body">' . implode('</td><td class="table_body">', $row) . '</td>
                            </tr>
                        ';
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
    public function toHtml() {    
        $table_html = "<table{$this->renderAttributes()}>";
        
        $table_html .= $this->getHeaderHtml();
        
        //Render the footer if specified
        if(!empty($this->footer)) {
            $table_html .= '<tfoot>';
            
            foreach($this->footer as $footer_row) {
                $table_html .= '<tr>';
                
                if(is_array($footer_row)) {
                    $table_html .= '<th class="table_footer">' . implode('</th><th class="table_footer">', $footer_row) . '</th>';
                }
                else {
                    assert('isset($this->number_of_columns) //Number of columns for this table has not been set.');
                
                    $table_html .= "<th class=\"table_footer\" colspan=\"{$this->number_of_columns}\">{$footer_row}</th>";
                }
                
                $table_html .= '</tr>';
            }
            
            $table_html .= '</tfoot>';
        }
        
        $table_html .= $this->getBodyHtml();
                
        $table_html .= '</table>';
        
        return $table_html;
    }
}