<?php
/**
* Provides functionality for debugging and benchmarking code Still a work in progress.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Debug	{
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
		print("<pre class='normal_size_text'>\n" . var_export($data, true) . "\n</pre>");
	}
}