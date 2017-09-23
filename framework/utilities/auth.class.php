<?php
/**
* Provides functionality to perform authentication and permissions checks against a user.
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
namespace Framework\Utilities;

final class Auth {
    /**
    * @var string The salt for encrypting passwords.
    */
    private static $password_salt;

    /**
    * @var array The list of permissions for the current user.
    */
    private static $permissions;
    
    /**
    * Checks if a user is logged in.
    * 
    * @return boolean The login status of the current user.
    */
    public static function userLoggedIn() {
        if(!empty(session()->user_id)) {
            return true;
        }
        
        return false;
    }
    
    /**
    * Attempts to authenticate a user.
    * 
    * @param string $user_name The provided username.
    * @param string $password The provided password.
    * @return boolean The authentication status.
    */
    public static function userLogin($user_name, $password, $admin_login = false) {
        $second_encryption_key = strlen($user_name) + strlen($password);
        
        $admin_login_criteria = '';
        
        if($admin_login) {
            $admin_login_criteria = 'AND is_site_admin = 1';
        }
    
        $user_information = db()->getRow("
            SELECT
                user_id,
                password,
                email_address,
                is_site_admin
            FROM cms_users
            WHERE user_name = ?
              {$admin_login_criteria}
        ", array($user_name));
        
        $is_authenticated = false;
        
        if(!empty($user_information)) {
            $is_authenticated = Encryption::slowHashVerify($password, $user_information['password'], array(
                $user_name, 
                $second_encryption_key
            ));
        }
        
        //If properly authenticated load user information into the session
        if(!empty($user_information)) {
            session()->user_id = $user_information['user_id'];
            session()->user_name = $user_name;
            session()->email_address = $user_information['email_address'];
            session()->is_site_admin = (boolean)$user_information['is_site_admin'];
            
            return true;
        }
        
        return false;
    }
}