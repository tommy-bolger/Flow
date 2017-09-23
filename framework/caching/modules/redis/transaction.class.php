<?php
/**
* The redis module of the framework cache abstraction layer.
* Copyright (c) 2015, Tommy Bolger
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
namespace Framework\Caching\Modules\Redis;

use \Redis as RedisClient;

class Transaction {
    const COMMIT_SIZE = 100;
    
    /**
    * @var object The redis client object.
    */ 
    protected $redis_client;

    /**
    * @var object The redis transaction object.
    */ 
    protected $current_transaction;
    
    /**
    * @var integer The number of queued commands in the current transaction.
    */ 
    protected $current_transaction_size = 0;
    
    /**
    * @var mixed The callback functionality to execute commands after the transaction is executed.
    */ 
    protected $commit_post_process_callback;
    
    /**
    * @var mixed The fixed arguments to pass into the post process callback.
    */ 
    protected $commit_post_process_callback_arguments = array();
    
    /**
     * Initializes this instance of Transaction.
     *
     * @return void
     */
    public function __construct(RedisClient $redis_client) {
        $this->redis_object = $redis_client;
        
        $this->current_transaction = $this->redis_object->multi(RedisClient::PIPELINE);
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the redis transaction object.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call($function_name, $arguments) {
        if($this->current_transaction_size == self::COMMIT_SIZE) {
            $this->commit();
            
            $this->current_transaction = $this->redis_object->multi(RedisClient::PIPELINE);
        }
        
        $return_value = call_user_func_array(array($this->current_transaction, $function_name), $arguments);
        
        $this->current_transaction_size += 1;
        
        return $return_value;
    }
    
    /**
     * Sets the processing function that will execute operations after a commit is called.
     *
     * @param mixed $callback The callback functionality to execute. Can either be a function, name of a function, or array to a class method.
     * @return void
     */
    public function setCommitProcessCallback($callback, array $arguments = array()) {        
        $this->commit_post_process_callback = $callback;
        
        if(!empty($arguments)) {
            $this->commit_post_process_callback_arguments = $arguments;
        }
    }
    
    /**
     * Commits the current transaction, resets the transaction size counter, and executes and post process functionality.
     *
     * @return void
     */
    public function commit() {
        $return_data = $this->current_transaction->exec();
        
        $this->current_transaction_size = 0;
        
        if(isset($this->commit_post_process_callback) && !empty($return_data)) {
            $arguments = array(
                'return_data' => $return_data
            );
            
            if(!empty($this->commit_post_process_callback_arguments)) {
                $arguments = array_merge($arguments, $this->commit_post_process_callback_arguments);
            }
        
            call_user_func_array($this->commit_post_process_callback, $arguments);
            
            unset($arguments);                        
        }
        
        unset($return_data);
    }
    
    /**
     * Returns the object used to directly interact with redis.
     * This is for operations that require references to be passed to RedisClient functions, such as scan.     
     *
     * @return RedisClient the RedisClient instance.
     */
    public function getTransactionObject() {
        return $this->redis_transaction;
    }
}