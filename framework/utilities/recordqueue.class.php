<?php
/**
* A queue used to process several records in batches.
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

namespace Framework\Utilities;

use \Exception;

class RecordQueue {
    /**
     * @const integer COMMIT_COUNT The number of records in the queue required to trigger an automatic commit.
     */
    const COMMIT_COUNT = 100;

    /**
     * @var array $records The records in the queue.
     */
    protected $records = array();
    
    /**
     * @var array $commit_callback The callback to execute when a commit has been triggered.
     */
    protected $commit_callback;
    
    /**
     * @var array $commit_callback_arguments The arguments to pass into every callback when a commit has been triggered.
     */
    protected $commit_callback_arguments = array();
    
    /**
     * @var integer $commit_count The number of records in the queue required to trigger an automatic commit. Is set to COMMIT_COUNT by default.
     */
    protected $commit_count;
    
    /**
     * @var integer $commit_callback_reattempts The number of times to reattempt the set callback if it fails.
     */
    protected $commit_callback_reattempts = 1;
    
    /**
     * @var integer $commit_callback_reattempt_interval The wait time in seconds to wait in between callback reattempts.
     */
    protected $commit_callback_reattempt_interval = 1;
    
    /**
    * Initializes an instance of RecordQueue.
    * 
    * @param integer $commit_count (optional) The number of records in the queue that triggers an automatic commit. Defaults to the COMMIT_COUNT constant.
    * @return void
    */
    public function __construct($commit_count = self::COMMIT_COUNT) {        
        $this->setCommitCount($commit_count);
    }
    
    /**
    * Sets commit count to automatically trigger a commit.
    * 
    * @param integer $commit_count
    * @return void
    */
    public function setCommitCount($commit_count) {
        $this->commit_count = $commit_count;
    }
    
    /**
    * Sets commit callback to execute when a commit is triggered.
    * 
    * @param array $commit_callback The callback to execute.
    * @param array $commit_callback_arguments (optional) The callback arguments to pass into every execution of this callback. Defaults to an empty array.
    * @return void
    */
    public function setCommitCallback($commit_callback, array $commit_callback_arguments = array()) {
        if(!is_callable($commit_callback)) {
            throw new Exception("Specified commit callback is not valid.");
        }
        
        $this->commit_callback = $commit_callback;
        $this->commit_callback_arguments = $commit_callback_arguments;
    }
    
    /**
    * Sets the number of reattempts to execute the callback if it fails.
    * 
    * @param integer $commit_callback_reattempts
    * @return void
    */
    public function setCommitCallbackReattempts($commit_callback_reattempts) {
        $this->commit_callback_reattempts = $commit_callback_reattempts;
    }
    
    /**
    * Sets the amount of time to wait in between reattempts to execute the callback if it fails.
    * 
    * @param integer $commit_callback_reattempts Number of seconds between reattmpts.
    * @return void
    */
    public function setCommitCallbackReattemptInterval($commit_callback_reattempt_interval) {
        $this->commit_callback_reattempt_interval = $commit_callback_reattempt_interval;
    }
    
    /**
    * Adds a record to the queue.
    * 
    * @param mixed $record
    * @return void
    */
    public function addRecord($record) {
        if(count($this->records) >= $this->commit_count) {
            $this->commit();
        }
        
        $this->records[] = $record;
    }
    
    /**
    * Add several records to the queue.
    * 
    * @param array $records
    * @return void
    */
    public function addRecords(array &$records) {
        if(!empty($records)) {
            foreach($records as &$record) {
                $this->addRecord($record);
            }
        }
    }
    
    /**
    * Executes the set callback with the records stored in the queue.
    * 
    * @return void
    */
    public function commit() {
        if(!empty($this->records)) {
            if(empty($this->commit_callback)) {
                throw new Exception("Commit callback has not been set.");
            }
            
            $commit_callback_arguments = $this->commit_callback_arguments;
            
            array_unshift($commit_callback_arguments, $this->records);
            
            $commit_callback_attempts = 1;
            $commit_callback_successful = false;
            
            while($commit_callback_successful == false && $commit_callback_attempts <= $this->commit_callback_reattempts) {
                try {
                    call_user_func_array($this->commit_callback, $commit_callback_arguments);

                    $commit_callback_successful = true;
                }
                catch(Exception $exception) {
                    $commit_callback_successful = false;
                    $commit_callback_attempts += 1;
                
                    if($commit_callback_attempts <= $this->commit_callback_reattempts) {
                        sleep($this->commit_callback_reattempt_interval);
                    }
                    else {
                        throw $exception;
                    }
                }
            }
            
            $this->records = array();
        }
    }
}