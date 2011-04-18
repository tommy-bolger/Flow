<?php
/**
* Provides functionality to perform authentication and permissions checks against a user.
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
        if(isset(session()->user_id) && !empty(session()->user_id)) {
            return true;
        }
        
        return false;
    }
    
    /**
    * Encrypts a password string into a hash.
    * 
    * @param string $plaintext_password The plaintext password to encrypt.
    * @return string The sha1 hash of the input plaintext password.
    */
    public static function encryptPassword($plaintext_password) {    
        if(!isset(self::$password_salt)) {
            self::$password_salt = config('framework')->getParameter('password_salt');
        }
        
        $salt_split = str_split($plaintext_password, 1);
        
        $password_split = str_split($plaintext_password, 1);
        
        $password = array_merge($password_split, $salt_split);
        
        sort($password);
        
        $password = sha1(implode('', $password));
        
        return $password;
    }
    
    /**
    * Attempts to authenticate a user.
    * 
    * @param string $user_name The provided username.
    * @param string $password The provided password.
    * @return boolean The authentication status.
    */
    public static function userLogin($user_name, $password) {
        $password = self::encryptPassword($password);
    
        $user_information = db()->getRow("
            SELECT
                user_id,
                email_address,
                is_site_admin
            FROM users
            WHERE user_name = ?
              AND password = ?
        ", array($user_name, $password));
        
        //If properly authenticated load user information into the session
        if(!empty($user_information)) {
            session()->user_id = $user_information['user_id'];
            session()->user_name = $user_name;
            session()->email_address = $user_information['email_address'];
            session()->is_site_admin = $user_information['is_site_admin'];
            
            return true;
        }
        
        return false;
    }
    
    /**
    * Attempts to authenticate an administrator user.
    * 
    * @param string $user_name The provided username.
    * @param string $password The provided password.
    * @return boolean The authentication status.
    */
    public static function adminLogin($user_name, $password) {
        $password = self::encryptPassword($password);
    
        $admin_information = db()->getRow("
            SELECT
                user_id,
                email_address,
                is_site_admin
            FROM users
            WHERE is_site_admin = true
              AND user_name = ?
              AND password = ?
        ", array($user_name, $password));
        
        //If properly authenticated load user information into the session
        if(!empty($admin_information)) {
            session()->user_id = $admin_information['user_id'];
            session()->user_name = $user_name;
            session()->email_address = $admin_information['email_address'];
            session()->is_site_admin = $admin_information['is_site_admin'];
            
            return true;
        }
        
        return false;
    }
}