<?php
namespace Framework\Data\Database;

use \Framework\Data\Database\Database;

class InsertQueue {
    const COMMIT_COUNT = 10000;
    
    protected $database;
    
    protected $table_name;

    protected $records = array();
    
    protected $commit_count;
    
    public function __construct($table_name, Database $database, $commit_count = self::COMMIT_COUNT) {
        $this->table_name = $table_name;
        
        $this->database = $database;
        
        $this->setCommitCount($commit_count);
    }
    
    public function setCommitCount($commit_count) {
        $this->commit_count = $commit_count;
    }
    
    public function addRecord(array $record) {
        if(count($this->records) >= $this->commit_count) {
            $this->commit();
        }
        
        $this->records[] = $record;
    }
    
    public function addRecords(array &$records) {
        if(!empty($records)) {
            foreach($records as &$record) {
                $this->addRecord($record);
            }
        }
    }
    
    public function commit() {   
        if(!empty($this->records)) {
            $this->database->insertMulti($this->table_name, $this->records);
            
            unset($this->records);
            
            $this->records = array();
        }
    }
}