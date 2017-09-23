<?php
/**
* Manages access bans by IP address.
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

namespace framework\Security;

class Bans {
    /**
     * Adds a banned ip address to the database.
     *      
     * @param string $ip_address The ip address to ban.
     * @param string $expiration_time (optional) The date and time when the ban will expire. Defaults to NULL.           
     * @return void
     */
    public static function add($ip_address, $expiration_time = NULL) {    
        $active_ban_id = db()->getOne("
            SELECT banned_ip_address_id
            FROM cms_banned_ip_addresses
            WHERE ip_address = ?
                AND (
                    expiration_time IS NULL
                    OR expiration_time >= NOW()
                )
        ", array($ip_address));
        
        $new_ban_id = NULL;
        
        if(empty($active_ban_id)) {
            $new_ban_id = db()->insert('cms_banned_ip_addresses', array(
                'ip_address' => $ip_address,
                'expiration_time' => $expiration_time
            ));
        }
        else {
            return false;
        }
        
        return $new_ban_id;
    }
    
    /**
     * Retrieves all information about a banned ip address.
     *      
     * @param string $ip_address The banned ip address.       
     * @return array
     */
    public static function get($ip_address) {
        return db()->getRow("
            SELECT
                banned_ip_address_id,
                expiration_time
            FROM cms_banned_ip_addresses
            WHERE ip_address = ?
                AND (expiration_time IS NULL OR expiration_time >= ?) 
        ", array(
            $ip_address,
            date('Y-m-d H:i:s')
        ));
    }
}