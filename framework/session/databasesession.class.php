<?php
/**
* Enables the PHP session handler to utilize a database table for session read/write operations.
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
namespace Framework\Session;

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
            SELECT 
                session_data, 
                expire_time
            FROM cms_sessions
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
                
                db()->update('cms_sessions', array(
                    'session_name' => session_name(),
                    'session_data' => base64_encode($session_data),
                    'expire_time' => $expire_time
                ), array('session_id' => $session_id));
                    
                db()->useExceptionHandler();
            }
            else {
                db()->useErrorHandler();
                db()->delete("cms_sessions", array('session_id' => $session_id));
                db()->useExceptionHandler();
            }
        }
        else {
            if(!empty($session_data)) {
                db()->useErrorHandler();
                            
                db()->insert('cms_sessions', array(
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
        db()->delete('cms_sessions', array('session_id' => $session_id));
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
        db()->delete("cms_sessions", "expire_time < ?", array(time()));
        db()->useExceptionHandler();
        
        return true;
    }
}