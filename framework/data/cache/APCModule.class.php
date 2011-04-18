<?php
/**
* The APC module for the framework cache abstraction layer.
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
class APCModule {    
    /**
	 * Catches all function calls not present in this class and throws an exception to avoid a fatal error.
	 *
	 * @param string $function_name The function name.
	 * @param array $arguments The function arguments.
	 * @return mixed
	 */
	public function __call($function_name, $arguments) {
        trigger_error("Function '{$function_name}' does not exist in this class.");
	}
	
	/**
	 * Sets a variable value in APC.
	 *
	 * @param string $key The name of the variable to cache.
	 * @param mixed $value The value of the variable to cache.
	 * @param integer $expire_time The lifetime of the cached variable.	 
	 * @return mixed
	 */
	public function set($key, $value, $expire_time) {
        return apc_store($key, $value, $expire_time);
	}
	
	/**
	 * Retrieves a cached variable value from APC.
	 *
	 * @param string $key The name of the variable in the cache.
	 * @return mixed
	 */
	public function get($key) {
        return apc_fetch($key);
	}
}