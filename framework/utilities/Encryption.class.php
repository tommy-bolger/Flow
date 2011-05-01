<?php
/**
* Provides functionality to perform encryption and decryption operations on sensitive data. Requires the Mcrypt and Mhash modules.
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
class Encryption {
    /**
    * @var int The encryption algorithm to use for mcrypt.
    */  
    const MCRYPT_ALGORITHM = MCRYPT_RIJNDAEL_128;
    
    /**
    * @var int The encryption mode to use for mcrypt.
    */  
    const MCRYPT_MODE = MCRYPT_MODE_CBC;
    
    /**
    * @var int The hash algorithm to use for mhash.
    */  
    const MHASH_ALGORITHM = MHASH_SHA512;
    
    /**
    * @var string The hash algorithm to use for hash_hmac.
    */
    const HMAC_ALGORITHM = 'sha512';

    /**
    * @var string The site key stored in the framework configuration.
    */
    private static $site_key;
    
    /**
    * @var string The salt for all passwords stored in the configuration.
    */
    private static $password_salt;

    /**
    * Generates and retrieves a sha1 hash based on a random value.
    * 
    * @return string
    */
    public static function generateShortHash() {
        return sha1(uniqid(mt_rand(), true));
    }
    
    /**
    * Generates and retrieves a sha256 hash based on a random value.
    * 
    * @return string
    */
    public static function generateMediumHash() {
        return hash('sha256', uniqid(mt_rand(), true));
    }
    
    /**
    * Generates and retrieves a sha512 hash based on a random value.
    * 
    * @return string
    */
    public static function generateLongHash() {
        return hash(self::HMAC_ALGORITHM, uniqid(mt_rand(), true));
    }
    
    /**
    * Generates and retrieves a key for encryption with mcrypt based on or more plain keys.
    * 
    * @param array $plain_keys The plain keys to use to generate an encryption key.    
    * @return string
    */
    public static function generateEncryptionKey($plain_keys) {
        assert('is_array($plain_keys) && !empty($plain_keys)');
        
        if(!isset(self::$site_key)) {
            self::$site_key = config('framework')->getParameter('site_key');
        }
        
        if(!isset(self::$password_salt)) {
            self::$password_salt = config('framework')->getParameter('password_salt');
        }
        
        $plain_key = implode($plain_keys);
        
        $salt = strlen($plain_key) . self::$site_key . self::$password_salt;
        
        $key_length = mcrypt_get_key_size(self::MCRYPT_ALGORITHM, self::MCRYPT_MODE);

        return mhash_keygen_s2k(self::MHASH_ALGORITHM, $plain_key, $salt, $key_length);
    }
    
    /**
    * Generates and retrieves an iv using mhash for encryption/decryption with mcrypt.
    * 
    * @param string $encryption_key The generated encryption key to base the iv off of.    
    * @return string
    */
    private static function generateIV($encryption_key) {
        assert('is_string($encryption_key) && !empty($encryption_key)');
    
        return mhash_keygen_s2k(self::MHASH_ALGORITHM, $encryption_key, strlen($encryption_key), mcrypt_get_iv_size(self::MCRYPT_ALGORITHM, self::MCRYPT_MODE));
    }
    
    /**
    * Encrypts sensitive data based on several plain keys.
    * 
    * @param string $sensitive_data The plaintext data to encrypt.
    * @param array $plain_keys The plain keys to use to generate the encryption key.     
    * @return string The encrypted data base64 encoded.
    */
    public static function encrypt($sensitive_data, $plain_keys) {
        $encryption_key = self::generateEncryptionKey($plain_keys);

        $iv = self::generateIV($encryption_key);
        
        $encrypted_data = mcrypt_encrypt(self::MCRYPT_ALGORITHM, $encryption_key, $sensitive_data, self::MCRYPT_MODE, $iv);
        
        return base64_encode($encrypted_data);
    }
    
    /**
    * Decrypts sensitive data based on several plain keys.
    * 
    * @param string $sensitive_data The encrypted data to decrypt.
    * @param array $plain_keys The plain keys to use to generate the decryption key.     
    * @return string The sensitive data in plaintext.
    */
    public static function decrypt($sensitive_data, $plain_keys) {
        $encryption_key = self::generateEncryptionKey($plain_keys);
        
        $iv = self::generateIV($encryption_key);
        
        $unencoded_data = base64_decode($sensitive_data);
        
        return mcrypt_decrypt(self::MCRYPT_ALGORITHM, $encryption_key, $unencoded_data, self::MCRYPT_MODE, $iv);
    }
    
    /**
    * Generates a hash of sensitive data based on one or more specified plain keys.
    * 
    * @param string $sensitive_data The encrypted data to generate a has from.
    * @param array $plain_keys The plain keys to use to generate the encryption key.     
    * @return string The hash of the sensitive data.
    */
    public static function hash($sensitive_data, $plain_keys) {
        $encryption_key = self::generateEncryptionKey($plain_keys);
        
        $salted_data = self::$password_salt . strlen($sensitive_data) . $sensitive_data;
    
        return hash_hmac(self::HMAC_ALGORITHM, $salted_data, $encryption_key);
    }
}