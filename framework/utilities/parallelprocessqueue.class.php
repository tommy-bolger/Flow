<?php
/**
* A process queue used to execute several blocks of code contained in functions via callbacks simultaneously by spawning a child process for each block of code.
* Original code that this is based on can be found at http://php.net/manual/en/function.pcntl-wait.php#98710 and http://php.net/manual/en/function.pcntl-wait.php#98712
* Copyright (c) 2016, Tommy Bolger
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

declare(ticks = 100);

class ParallelProcessQueue {
    const DEFAULT_MAX_PARALLEL_PROCESSES = 10;

    protected $max_parallel_processes;
    protected $process_queue;
    protected $processes_started = 0;
    protected $active_processes = array();
    protected $signal_queue = array();   
    protected $parent_pid;
   
    public function __construct() {    
        $this->max_parallel_processes = self::DEFAULT_MAX_PARALLEL_PROCESSES;
    
        $this->parent_pid = getmypid();
        
        $current_microtime = explode(' ', microtime());
        
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
    }
    
    public function setMaxParallelProcesses($max_parallel_processes) {
        assert('is_integer($max_parallel_processes) && $max_parallel_processes > 0');
    
        $this->max_parallel_processes = $max_parallel_processes;
    }
    
    public function addProcessToQueue($callback_function, array $callback_function_parameters = array()) {
        assert('is_callable($callback_function)');
        
        $process_id = random_int(PHP_INT_MIN, PHP_INT_MAX);
        
        $this->process_queue[$process_id]['process_id'] = $process_id;
        $this->process_queue[$process_id]['callback_function'] = $callback_function;
        $this->process_queue[$process_id]['callback_function_parameters'] = $callback_function_parameters;
    }
   
    /**
    * Run the Daemon
    */
    public function run() {
        foreach($this->process_queue as $process_id => $process) {
            while(count($this->active_processes) >= $this->max_parallel_processes) {
                sleep(1);
            }
            
            $this->launchProcess($process_id);
        }
       
        //Wait for child processes to finish before exiting here
        while(!empty($this->active_processes)) {
            sleep(1);
        }
    }
   
    /**
    * Launch a process from the process queue
    */
    protected function launchProcess($process_id) {
        $process = $this->process_queue[$process_id];
    
        $pid = pcntl_fork();
        
        if($pid == -1) {
            //The an exception if there was a problem with launching the child process
            throw new Exception("A problem was encountered while attempting to start a child process.");
        }
        elseif($pid) { 
            /*
                Parent process
                Sometimes the childSignalHandler function can receive a signal before this code executes if the child script executes quickly enough.
                In the event that a signal for this pid was caught before it gets checked, it will be in signal_queue.
                Process it now as if the signal was just received, and then remove the parent process from signal_queue.
            */
            
            $this->active_processes[$pid] = $process_id;
        
            if(isset($this->signal_queue[$pid])){
                $this->childSignalHandler(SIGCHLD, $pid, $this->signal_queue[$pid]);
                
                unset($this->signal_queue[$pid]);
            }
        }
        else {
            //The child process is running, so execute its callback function            
            call_user_func_array($process['callback_function'], array_values($process['callback_function_parameters']));
            
            exit;
        }
    }
   
    public function childSignalHandler($signal_number, $pid = NULL, $status = NULL) {        
        /*
            If no pid is provided then retrieve the signal from the system to figure out which child process ended.
        */
        if(!$pid) {
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
        
        while($pid > 0) {
            if($pid && isset($this->active_processes[$pid])) {                
                unset($this->active_processes[$pid]);
            }
            else {
                /*
                    The child process has finished before the parent process could know that it has launched.
                    Add it to the signal queue so that the parent process can handle it when it's ready.
                */
                $this->signal_queue[$pid] = $status;
            }
            
            $pid = pcntl_waitpid(-1, $status, WNOHANG);
        }
    }
}