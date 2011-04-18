<?php
/**
* Enables the PHP session handler to utilize a database table for session read/write operations.
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
class DatabaseSession {
    /**
    * @var boolean Flag to determine if a record for this session exists in this database.
    */
    private static $database_record_exists = false;

    /**
	 * The open callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @param string $session_path (optional) The path to the session file.
	 * @param string $session_name (optional) The name of the session.
	 * @return boolean
	 */
    public static function open($session_path = null, $session_name = null) {
        return true;
    }
    
    /**
	 * The close callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @return boolean
	 */
    public static function close() {
        return true;
    }
    
    /**
	 * The road callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @param string $session_id (optional) The session_id.
	 * @return mixed Returns either the decoded session data as a string or an empty string.
	 */
    public static function load($session_id = null) {
        $session_data = null;
    
        db()->useErrorHandler();
        
        $session_data = db()->getRow("
            SELECT session_data, expire_time
            FROM sessions
            WHERE session_id = ?
        ", array($session_id));
        
        db()->useExceptionHandler();
          
        if(!empty($session_data)) {
            self::$database_record_exists = true;
        
            if(time() < $session_data['expire_time']) {
                return base64_decode($session_data['session_data']);
            }
        }
          
        //Return an empty string required by the session handler
        return "";
    }

    /**
	 * The write callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @param string $session_id (optional) The session_id.
	 * @param string $session_data (optional) The session data.
	 * @return boolean
	 */
    public static function save($session_id = null, $session_data = null) {
        $expire_time = time() + get_cfg_var('session.gc_maxlifetime') - 1;

        if(self::$database_record_exists) {
            //If a session_id exists update its information
            if(!empty($session_data)) {
                db()->useErrorHandler();
                
                db()->update('sessions', array(
                    'session_name' => session_name(),
                    'session_data' => base64_encode($session_data),
                    'expire_time' => $expire_time
                ), array('session_id' => $session_id));
                    
                db()->useExceptionHandler();
            }
            else {
                db()->useErrorHandler();
                db()->delete("sessions", array('session_id' => $session_id));
                db()->useExceptionHandler();
            }
        }
        else {
            if(!empty($session_data)) {
                db()->useErrorHandler();
                            
                db()->insert('sessions', array(
                    'session_id' => $session_id,
                    'session_name' => session_name(),
                    'session_data' => base64_encode($session_data),
                    'expire_time' => $expire_time
                ));
                
                db()->useExceptionHandler();
            }
        }
        
        return true;
    }

    /**
	 * The destroy callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @param string $session_id (optional) The session_id.
	 * @return boolean
	 */
    public static function destroy($session_id = null) {
        session_unset();
        
        db()->useErrorHandler();
        db()->delete('sessions', array('session_id' => $session_id));
        db()->useExceptionHandler();
        
        return true;
    }

    /**
	 * The gc callback function for session_set_save_handler(). Should never be called otherwise.
	 *
	 * @param integer $max_lifetime (optional) The max session lifetime.
	 * @return boolean
	 */
    public static function garbageCollection($max_lifetime = null) {
        db()->useErrorHandler();
        db()->delete("sessions", "expire_time < ?", array(time()));
        db()->useExceptionHandler();
        
        return true;
    }
}