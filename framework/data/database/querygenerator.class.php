<?php
/**
* The framework SQL generation object.
*
* Copyright (c) 2017, Tommy Bolger
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
namespace Framework\Data\Database;

use \Exception;
use \Framework\Core\Framework;

class QueryGenerator {    
    /**
     * Generates a where clause for use in a query.
     *
     * @param mixed $where_clause The fields that go in the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value.     
     * @return string The completed where clause.
     */
    public static function getWhereClause($where_clause, array $case_insensitive_columns = array()) {    
        $query_where_clause = "";
    
        if(!empty($where_clause)) {
            if(is_array($where_clause)) {
                $where_clause_columns = array_keys($where_clause);
                
                $where_column_row = 0;
                
                foreach($where_clause as $where_clause_column => $where_clause_value) {                    
                    $column_equivalency = "=";
                    $value_placeholder = "?";
                    
                    if(!is_null($where_clause_value)) {
                        if(!empty($case_insensitive_columns)) {
                            if(in_array($where_clause_column, $case_insensitive_columns)) {
                                $column_equivalency = "ILIKE";
                            }
                        }
                    }
                    else {
                        $column_equivalency = "IS";
                        $value_placeholder = 'NULL';
                    }
                
                    if($where_column_row == 0) {
                        $query_where_clause .= "\nWHERE\t{$where_clause_column} {$column_equivalency} {$value_placeholder}";
                    }
                    else {
                        $query_where_clause .= "\nAND\t{$where_clause_column} {$column_equivalency} {$value_placeholder}";
                    }
                    
                    ++$where_column_row;
                }
            }
            else {
                $query_where_clause .= "\nWHERE\n\t{$where_clause}";
            }
        }
        
        return $query_where_clause;
    }
    
    /**
     * Constructs an IN segment with placeholders for each entry.
     *
     * Example output: IN (?, ?, ?, ... ?)
     *
     * @param integer $number_of_entries The number of entries in the statement.
     * @param string $placeholder The placeholder character for all values in this segment.
     * @return string
     */
    public static function getInStatement($number_of_entries, $placeholder = '?') {
        $record_placeholders = implode(', ', array_fill(0, $number_of_entries, $placeholder));
            
        $in_statement = "IN ({$record_placeholders})";
        
        return $in_statement;
    }
    
    /**
     * Constructs a select query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to retrieve data from.
     * @param mixed $fields The fields to retrieve from the query. An associative array or a valid string are accepted.
     * @param mixed $where_clause The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns A list of columns in the where clause that are matched to a case insensitive value.     
     * @return string The completed update query
     */
    public static function getSelectQuery($table_name, $fields, $where_clause, $case_insensitive_columns) {     
        if(is_array($fields)) {
            $fields = implode(",\n", $fields);
        }
    
        $select_query = "SELECT\n{$fields}\nFROM {$table_name}";
        
        $select_query .= static::getWhereClause($where_clause, $case_insensitive_columns);

        return $select_query;
    }
    
    /**
     * Constructs a VALUES segment with placeholders for each field grouped by each record wrapped in a parenthesis and separated by a comma.
     *
     * Example output: VALUES (?, ?, ?), (?, ?, ?)
     *
     * @param integer $number_of_fields The number of fields in each record.
     * @param integer $number_of_records The number of records for this segment.
     * @param string $placeholder The placeholder character for all values in this segment.
     * @return string The completed update query.
     */
    public static function getValuesSegment($number_of_fields, $number_of_records, $placeholder = '?') {
        $record_placeholders = implode(', ', array_fill(0, $number_of_fields, $placeholder));
    
        $multi_record_placeholders = array_fill(0, $number_of_records, $record_placeholders);
            
        $values = 'VALUES (' . implode('), (', $multi_record_placeholders) . ')';
        
        return $values;
    }
    
    /**
     * Constructs an insert query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to insert a new row into.
     * @param mixed $fields The fields to be inserted. An associative array is only accepted.
     * @return string The completed update query.
     */
    public static function getInsertQuery($table_name, $fields) {
        $insert_field_names = implode(", ", $fields);

        $insert_query = "INSERT INTO {$table_name} ({$insert_field_names})\nVALUES (" . 
            implode(', ', array_fill(0, count($fields), '?')) . ")";

        return $insert_query;
    }
    
    /**
     * Constructs a multi insert query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to insert a new row into.
     * @param mixed $fields The fields to be inserted.
     * @param integer $number_of_records The number of records being inserted.
     * @return string The completed insert query.
     */
    public static function getMultiInsertQuery($table_name, array $fields, $number_of_records) {
        $insert_field_names = implode(", ", $fields);
        
        $values = static::getValuesSegment(count($fields), $number_of_records);

        $insert_query = "INSERT INTO {$table_name} ({$insert_field_names})\n {$values};";

        return $insert_query;
    }
    
    /**
     * Constructs an update query based on the parameters passed to it.
     *
     * @param string $table_name The name of the target table to update.
     * @param mixed $fields The fields with values to update. An associative array or a valid string are accepted.
     * @param mixed $where_clause The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns A list of columns in the where clause that are matched to a case insensitive value.          
     * @return string The completed update query.
     */
    public static function getUpdateQuery($table_name, $fields, $where_clause, $case_insensitive_columns) {       
        $update_query = "UPDATE\n\t{$table_name}\nSET";
        
        if(is_array($fields)) {
            $update_query_columns = array_keys($fields);
            
            //Generate the fields to be updated            
            foreach($update_query_columns as $update_query_column) {
                $update_query .= "\n\t{$update_query_column} = ?,";
            }
            
            $update_query = rtrim($update_query, ",");
        }
        else {
            $update_query .= "\n\t{$fields}";
        }
        
        $update_query .= static::getWhereClause($where_clause, $case_insensitive_columns);

        return $update_query;
    }
    
    /**
     * Constructs a delete query based on the parameters passed to it
     *
     * @param string $table_name The name of the target table to delete from.
     * @param mixed $where_clause The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns A list of columns in the where clause that are matched to a case insensitive value.     
     * @return string The generated delete query.
     */
    public static function getDeleteQuery($table_name, $where_clause, $case_insensitive_columns) {
        $delete_query = "DELETE FROM\n\t{$table_name}";
        
        $delete_query .= static::getWhereClause($where_clause, $case_insensitive_columns);
        
        return $delete_query;
    }
}
