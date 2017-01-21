<?php
/**
* Abstraction layer for PDO with functions that automate several database operations.
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
namespace Framework\Data;

use PDO;
use \PDOStatement;
use \Framework\Core\Framework;
use \Framework\Utilities\Encryption;

class Database {
    /**
    * @var array A static list of all active database connection objects.
    */
    protected static $database_connections = array();
    
    /**
    * @var string The name of the database.
    */
    protected $database_name;
    
    /**
    * @var object The database connection object for this instance.
    */
    protected $database_connection;
    
    /**
    * @var string The name of the loaded database driver (mysql, pgsql, etc.).
    */
    protected $database_driver_name;
    
    /**
    * @var boolean A flag to determine whether to use the error handler or exception handler.
    */
    protected $use_error_handler = true;
    
    /**
    * @var array A list of all cached queries.
    */
    protected $cached_queries;
    
    /**
    * @var PDOStatement The statement of the last executed query.
    */
    protected $last_executed_statement;
    
    /**
    * @var boolean Indicates if stats should be collected on each query or not.
    */
    protected $log_query_stats = false;
    
    /**
    * @var array The list of statistics of each query type that was run.
    */
    protected $query_stats = array();

    /**
     * Retrieves an instantiated database object of the specified database connection or instantiates a new database connection object.
     *
     * @param string $database_connection (optional) The name of the database connection.
     * @return object The database connection object.
     */
    public static function getInstance($database_connection = NULL, $reconnect = false) {            
        if(empty($database_connection)) {
            $database_connection = 'default';            
        }
        
        if(!isset(self::$database_connections[$database_connection]) || $reconnect == true) {
            $new_database_connection = new database();

            if($database_connection == 'default') {
                $framework = Framework::getInstance();
            
                $dsn = $framework->configuration->database_dsn;
                $username = $framework->configuration->database_user;
                $encrypted_password = $framework->configuration->database_password;
                
                $unencrypted_password = '';
                
                if(!empty($encrypted_password)) {
                    //Autodetect a password stored in base64 to attempt to decrypt its contents for a plaintext password.
                    if(strpos($encrypted_password, '==') !== false) {
                        $unencrypted_password = Encryption::decrypt($encrypted_password, array($dsn, $username));
                    }
                    else {
                        $unencrypted_password = $encrypted_password;
                    }
                }

                $new_database_connection->connect($dsn, $username, $unencrypted_password);
            }
            
            self::$database_connections[$database_connection] = $new_database_connection;
        }
        
        return self::$database_connections[$database_connection];
    }
    
    /**
     * Destroys an instantiated database object of the specified database connection.
     *
     * @param string $database_connection (optional) The name of the database connection.
     * @return object The database connection object.
     */
    public static function destroyInstance($database_connection = NULL) {
        if(empty($database_connection)) {
            $database_connection = 'default';            
        }
        
        if(isset(self::$database_connections[$database_connection])) {
            unset(self::$database_connections[$database_connection]);
        }
    }
    
    /**
     * Sets which database connection will be the default that db() returns.
     *
     * @param string $connection_name The name of the connection.
     * @return void
     */
    public static function setDefault($connection_name) {
        if(isset(self::$database_connections[$connection_name])) {
            self::$database_connections['default'] = self::$database_connections[$connection_name];
            
            unset(self::$database_connections[$connection_name]);
        }
        else {
            throw new \Exception("Connection '{$connection_name}' does not exist.");
        }
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the database connection object.
     *
     * @param string $function_name The function name.
     * @param array $arguments The function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        return call_user_func_array(array($this->database_connection, $function_name), $arguments);
    }
    
    /**
     * Initializes a new database connection using PDO.
     *
     * @param string $engine The database engine to connect to.
     * @param string $host The the url or IP address to the database server.    
     * @param string $database The name of the database to load.      
     * @param string $username The database username.
     * @param string $password The database user password.
     * @return void
     */
    public function connect($dsn, $username, $password) {
        $this->database_connection = new PDO($dsn, $username, $password, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            PDO::ATTR_EMULATE_PREPARES => false
        ));
        
        $this->useExceptionHandler();
        
        $this->database_driver_name = $this->database_connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
    
    /**
     * Closes the connection to the current database.
     *
     * @return void
     */
    public function closeConnection() {
        $this->database_connection = NULL;
    }
    
    /**
     * Sets the database class to use the error handler instead of throwing an exception.
     *
     * @return void
     */
    public function useErrorHandler() {
        $this->database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        
        $this->use_error_handler = true;
    }
    
    /**
     * Sets the database class to throw an exception instead of using the error handler.
     *
     * @return void
     */
    public function useExceptionHandler() {
        $this->database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->use_error_handler = false;
    }
    
    /**
     * Either throws an exception or triggers an error if use_error_handler is set to true.
     *
     * @param string $exception_message The exception message.
     * @param string $exception_code (optional) The exception code. Defaults to 0.
     * @return void
     */
    private function throwException($exception_message, $exception_code = 0) {
        if(!$this->use_error_handler) {
            throw new \Exception($exception_message, $exception_code);
        }
        else {
            trigger_error($exception_message);
        }
    }
    
    /**
     * Enables logging of query statistics.
     *
     * @return void
     */
    public function enableQueryStatLogging() {
        $this->log_query_stats = true;
    }
    
    /**
     * Disables logging of query statistics.
     *
     * @return void
     */
    public function disableQueryStatLogging() {
        $this->log_query_stats = false;
    }
    
    /**
     * Retrieves query stats
     *
     * @return array The stats on all executed queries.
     */
    public function getQueryStats() {
        if(!empty($this->query_stats)) {
            foreach($this->query_stats as $query_type => &$query_stat) {            
                $query_stat['average_execution_time'] = $query_stat['total_execution_time'] / $query_stat['number_of_queries'];
            }
        }
        
        return $this->query_stats;
    }
    
    /**
     * Gets the name of the current database.
     *
     * @return string The name of the current database.
     */
    public function getDatabaseName() {
        return $this->database_name;
    }
    
    /**
     * Prepares and executes a query that returns results.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return object The PDO statement object.
     */
    public function prepareExecuteQuery($sql_statement, $placeholder_values = array(), $query_name = '') {    
        assert('is_array($placeholder_values)');
    
        $query_object = NULL;
        
        if(!empty($query_name)) {            
            if(isset($this->cached_queries['prepared_queries'][$query_name])) {
                $query_object = $this->cached_queries['prepared_queries'][$query_name];
            }
            else {
                $query_object = $this->database_connection->prepare($sql_statement);
                
                $this->cached_queries['prepared_queries'][$query_name] = $query_object;
            }
        }
        else {
            $query_object = $this->database_connection->prepare($sql_statement);
        }
        
        $query_start_time = NULL;
        
        if($this->log_query_stats) {
            $query_start_time = time();
        }

        $query_object->execute($placeholder_values);
        
        if($this->log_query_stats) {
            $query_end_time = time();
            
            $query_index = md5($sql_statement);
            
            if(empty($this->query_stats[$query_index])) {
                $this->query_stats[$query_index] = array(
                    'query' => $sql_statement,
                    'number_of_queries' => 0,
                    'total_execution_time' => 0
                );
            }

            $this->query_stats[$query_index]['number_of_queries'] += 1;
            $this->query_stats[$query_index]['total_execution_time'] += ($query_end_time - $query_start_time);
        }
        
        $this->last_executed_statement = $query_object;

        return $query_object;
    }
    
    /**
     * Retrieves the PDO statement object of the last query executed.
     *
     * @return PDOStatement
     */
     public function getLastExecutedStatement() {
        return $this->last_executed_statement;
     }
    
    /**
     * Retrieves a single row from an executed PDO statement object.
     *
     * @param string $pdo_statement The PDO statement object to fetch for.
     * @return array|boolean The result row of the PDO statement, or false when there are no more results.
     */
     
     public function getStatementRow(PDOStatement $pdo_statement) {
        return $pdo_statement->fetch(PDO::FETCH_ASSOC);
     }
    
    /**
     * Gets all columns of all rows in a query result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getAll($sql_statement, $placeholder_values = array(), $query_name = '') {        
        $get_all_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);

        return $get_all_object->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Gets all columns of all rows in a query result set grouped by the first column in each row.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getAssoc($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_assoc_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        return $get_assoc_object->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    }
    
    /**
     * Gets all columns of all rows in a query result set with each row grouped by its first column.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getGroupedRows($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_grouped_rows_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        $grouped_rows = array();
        
        while($grouped_row = $get_grouped_rows_object->fetch(PDO::FETCH_ASSOC)) {
            $first_column_value = current($grouped_row);
        
            $grouped_rows[$first_column_value] = $grouped_row;
        }

        return $grouped_rows;
    }
    
    /**
     * Gets all columns of one row in a query result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getRow($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_row_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);

        return $get_row_object->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Gets all values of a specified column for the query result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getColumn($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_column_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);

        return $get_column_object->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    /**
     * Gets all values of the second single column grouped by the first column in the result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getGroupedColumn($sql_statement, $placeholder_values = array(), $query_name = '') {    
        $get_grouped_column_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        return $get_grouped_column_object->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
    }
    
    /**
     * Gets all values of the single column mapped to the first column in the result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getMappedColumn($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_mapped_column_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        $mapped_columns = array();
        
        while($mapped_column = $get_mapped_column_object->fetch(PDO::FETCH_NUM)) {
            $mapped_columns[$mapped_column[0]] = $mapped_column[1];
        }
        
        return $mapped_columns;
    }
    
    /**
     * Gets the concatenated values of columns mapped to the first column in the result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array The result set of the query.
     */
    public function getConcatMappedColumn($sql_statement, $delimiter, $placeholder_values = array(), $query_name = '') {
        $get_mapped_column_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        $mapped_columns = array();
        
        while($mapped_column = $get_mapped_column_object->fetch(PDO::FETCH_NUM)) {
            $first_column = $mapped_column[0];
            unset($mapped_column[0]);
        
            $mapped_columns[$first_column] = implode($delimiter, $mapped_column);
        }
        
        return $mapped_columns;
    }
    
    /**
     * Gets a first column's value of the first row in the result set.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return mixed The result set of the query.
     */
    public function getOne($sql_statement, $placeholder_values = array(), $query_name = '') {
        $get_one_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        return $get_one_object->fetchColumn();
    }
    
    /**
     * Generates a where clause for use in a query.
     *
     * @param mixed $where_clause The fields that go in the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value.     
     * @return string The completed where clause.
     */
    public function generateWhereClause($where_clause, $case_insensitive_columns = array()) {
        assert('is_array($case_insensitive_columns)');
    
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
     * Constructs a select query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to retrieve data from.
     * @param mixed $fields The fields to retrieve from the query. An associative array or a valid string are accepted.
     * @param mixed $where_clause The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns A list of columns in the where clause that are matched to a case insensitive value.     
     * @return string The completed update query
     */
    private function generateSelectQuery($table_name, $fields, $where_clause, $case_insensitive_columns) {
        //Set the beginning of the query        
        if(is_array($fields)) {
            $fields = implode(",\n", $fields);
        }
    
        $select_query = "SELECT\n{$fields}\nFROM {$table_name}";
        
        $select_query .= $this->generateWhereClause($where_clause, $case_insensitive_columns);

        return $select_query;
    }
    
    /**
     * Constructs an insert query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to insert a new row into.
     * @param mixed $fields The fields to be inserted. An associative array is only accepted.
     * @return string The completed update query.
     */
    private function generateInsertQuery($table_name, $fields) {
        $insert_field_names = implode(", ", array_keys($fields));

        //Set the beginning of the query
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
    private function generateMultiInsertQuery($table_name, array $fields, $number_of_records) {
        $insert_field_names = implode(", ", $fields);
        
        $record_placeholders = implode(', ', array_fill(0, count($fields), '?'));
    
        $multi_record_placeholders = array_fill(0, $number_of_records, $record_placeholders);
            
        $values = '(' . implode('), (', $multi_record_placeholders) . ')';

        $insert_query = "INSERT INTO {$table_name} ({$insert_field_names})\nVALUES {$values};";

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
    private function generateUpdateQuery($table_name, $fields, $where_clause, $case_insensitive_columns) {
        //Set the beginning of the query        
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
        
        $update_query .= $this->generateWhereClause($where_clause, $case_insensitive_columns);

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
    private function generateDeleteQuery($table_name, $where_clause, $case_insensitive_columns) {
        //Set the beginning of the query
        $delete_query = "DELETE FROM\n\t{$table_name}";
        
        $delete_query .= $this->generateWhereClause($where_clause, $case_insensitive_columns);
        
        return $delete_query;
    }
    
    /**
     * Performs the common functionality of the getData(), update(), insert(), and delete() functions.
     *
     * @param string $query_type The type of query that this function will execute.     
     * @param string $table_name The name of the table to perform the query on.
     * @param mixed $fields The fields affected by the query. An associative array or a valid string are accepted.
     * @param array $where_clause The where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value. 
     * @param string $query_name (optional) The cache name of the query. This enables caching of the generated query and the prepared statement.
     * @return object The query object.
     */
    private function generateQuery($query_type, $table_name, $fields, $where_clause, $case_insensitive_columns = array(), $query_name = '') {                
        $cached_queries_name = "{$query_type}_queries";
        $query = '';

        if(!empty($query_name) && isset($this->cached_queries[$cached_queries_name][$query_name])) {        
            $query = $this->cached_queries[$cached_queries_name][$query_name];
        }
          
        if(empty($query)) {
            switch($query_type) {
                case 'insert':
                    $query = $this->generateInsertQuery($table_name, $fields);
                    break;
                case 'update':
                    $query = $this->generateUpdateQuery($table_name, $fields, $where_clause, $case_insensitive_columns);
                    break;
                case 'delete':
                    $query = $this->generateDeleteQuery($table_name, $where_clause, $case_insensitive_columns);
                    break;
                case 'select':
                    $query = $this->generateSelectQuery($table_name, $fields, $where_clause, $case_insensitive_columns);
                    break;
                default:
                    throw new \Exception("Specified query type '{$query_type}' is not valid.");
                    break;
            }
              
            if(!empty($query_name)) {
                  $this->cached_queries[$cached_queries_name][$query_name] = $query;
            }
        }
        
        //If running a select query remove any NULL values in the where clause to prevent a parameter count mismatch with IS NULL criteria.
        if($query_type == 'select' || $query_type == 'update' || $query_type == 'delete') {
            if(!empty($where_clause)) {
                foreach($where_clause as $column_name => $column_value) {
                    if(is_null($column_value)) {
                        unset($where_clause[$column_name]);
                    }
                }
            }
        }

        $placeholder_values = array();

        if($query_type != 'select') {
            if(is_array($fields)) {
                $placeholder_values = array_values($fields);
            }
        }
          
        if(!empty($where_clause) && is_array($where_clause)) {
            $placeholder_values = array_merge($placeholder_values, array_values($where_clause));
        }

        $query_object = $this->prepareExecuteQuery($query, array_values($placeholder_values), $query_name);

        return $query_object;
    }
    
    /**
     * Constructs a select query with a where clause based on the parameters passed to it, caches the query, executes it, and returns the resulting dataset.
     *
     * @param string $table_name The name of the table to retrieve data from.
     * @param mixed $fields (optional) The fields to retrieve from the query. An associative array or a valid string are accepted. Defaults to '*'.
     * @param array $where_clause (optional) The where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return array
     */
    public function getData($table_name, $fields = "*", $where_clause = "", $case_insensitive_columns = array(), $query_name = '') {
        $select_object = $this->generateQuery('select', $table_name, $fields, $where_clause, $case_insensitive_columns, $query_name);
        
        return $select_object->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Constructs an update query based on the parameters passed to it, prepares the query, and then executes it.
     *
     * @param string $table_name The name of the target table to insert a new row into.
     * @param mixed $fields (optional) The fields to be inserted. An associative array is only accepted.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @param boolean $return_new_id (optional) Indicates if the new primary key value from this insertion should be returned. Defaults to true.
     * @return integer The primary key of the new record.
     */
    public function insert($table_name, $fields = array(), $query_name = '', $return_new_id = true) {
        assert('is_array($fields)');
    
        $this->generateQuery('insert', $table_name, $fields, NULL, NULL, $query_name);
        
        $new_id = NULL;
        
        if($return_new_id) {
            $sequence_name = NULL;
          
            if($this->database_driver_name == 'pgsql') {
                $sequence_name = "{$table_name}_seq";
            }
        
            $new_id = $this->database_connection->lastInsertId($sequence_name);
        }

        return $new_id;
    }
    
    /**
     * Constructs an update query based on the parameters passed to it, prepares the query, and then executes it.
     *
     * @param string $table_name The name of the target table to insert a new row into.
     * @param array $records The records to be inserted. Must be a multidimensional array containing associative arrays for each record.
     * @return void
     */
    public function insertMulti($table_name, array $records) {
        $query_object = NULL;
    
        if(!empty($records)) {
            $first_record = current($records);
            
            $query = $this->generateMultiInsertQuery($table_name, array_keys($first_record), count($records));
            
            $placeholder_values = array();
            
            array_walk_recursive($records, function($value, $key) use(&$placeholder_values){
                $placeholder_values[] = $value;
            });
            
            $query_object = $this->prepareExecuteQuery($query, array_values($placeholder_values));
        }
        
        return $query_object;
    }
    
    /**
     * Constructs an update query based on the parameters passed to it, prepares the query, and then executes it.
     *
     * @param string $table_name The name of the target table to update.
     * @param mixed $fields The fields with values to update. An associative array or a valid string are accepted.
     * @param mixed $where_clause (optional) The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return integer The number of rows affected.
     */
    public function update($table_name, $fields, $where_clause = "", $case_insensitive_columns = array(), $query_name = '') {
          $update_object = $this->generateQuery('update', $table_name, $fields, $where_clause, $case_insensitive_columns, $query_name);
          
          return $update_object->rowCount();
    }
    
    /**
     * Constructs a delete query based on the parameters passed to it, prepares the query, and then executes it.
     *
     * @param string $table_name The name of the target table to delete from.
     * @param mixed $where_clause (optional) The fields that go to the where clause. An associative array or a valid string are accepted.
     * @param array $case_insensitive_columns (optional) A list of columns in the where clause that are matched to a case insensitive value.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.
     * @return integer The number of rows affected.
     */
    public function delete($table_name, $where_clause = "", $case_insensitive_columns = array(), $query_name = '') {
        $delete_object = $this->generateQuery('delete', $table_name, NULL, $where_clause, $case_insensitive_columns, $query_name);
        
        return $delete_object->rowCount();
    }
    
    /**
     * Performs a generic query against the database.
     *
     * @param string $sql_statement The sql query.
     * @param array $placeholder_values (optional) The values of the query placeholders in the order they appear in the query.
     * @param string $query_name (optional) The cache name of the query. This enables caching of the prepared statement.       
     * @return integer The number of rows affected.
     */
    public function query($sql_statement, $placeholder_values = array(), $query_name = '') {
        $query_object = $this->prepareExecuteQuery($sql_statement, $placeholder_values, $query_name);
        
        return $query_object->rowCount();
    }
}
