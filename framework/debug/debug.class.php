<?php
/**
* Provides functionality for debugging and benchmarking code Still a work in progress.
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
namespace Framework\Debug;

use \Framework\Core\Framework;

class Debug {
    //Stores the page benchmark mode log
    private static $benchmark_logs = array();
    
    /**
     * Logs the time at which the function was called.
     *
     * @param string The name of the benchmark
     * @param string A description of the event being logged     
     * @return void
     */
    public static function benchmarkLogEvent($name, $event_description) {
        self::$benchmark_logs[$name]['event'][] = array(microtime(true), $event_description);
    }
    
    public static function calculateBenchmarkTotals() {
        foreach(self::$benchmark_logs as $benchmark_name => $benchmark_log) {
            $benchmark_start = array_shift($benchmark_log['event']);
            
            $benchmark_end = array_pop($benchmark_log['event']);
        
            self::$benchmark_logs[$benchmark_name]['total_time'] = number_format(round(($benchmark_end - $benchmark_start), 4), 4) . " seconds.";
        }
    }
    
    public static function display() {
        /* ---- Add code for profiling later ----*/
        if(1 == 2)
        {
            //Check the current mode and display results accordingly
            switch (Framework::$mode) {
                case "web":
                    echo "NEED TO IMPLEMENT THIS";
                    break;
                default:
                    echo "\n\n" . "NEED TO IMPLEMENT THIS" . "\n";
                    break;
            }
        }
    }

    public static function dump($data) {
        $debug_data = var_export($data, true);
    
        switch(Framework::$mode) {
            case 'page':
                print("<pre class='normal_size_text'>\n{$debug_data}\n</pre>");
                break;
            case 'ajax':
                echo json_encode(array(
                    'debug' => $debug_data
                ));
                
                exit;
                break;
        }
    }
}