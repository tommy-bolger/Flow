<?php
/**
* Provides functionality to perform encryption and decryption operations on sensitive data. Requires the Mcrypt and Mhash modules.
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

class Encryption {
    /**
    * @var integer The encryption algorithm to use for mcrypt.
    */  
    const MCRYPT_ALGORITHM = MCRYPT_RIJNDAEL_128;
    
    /**
    * @var integer The encryption mode to use for mcrypt.
    */  
    const MCRYPT_MODE = MCRYPT_MODE_CBC;
    
    /**
    * @var integer The hash algorithm to use for mhash.
    */  
    const MHASH_ALGORITHM = MHASH_SHA512;
    
    /**
    * @var string The hash algorithm to use for hash_hmac.
    */
    const HMAC_ALGORITHM = 'sha512';
    
    /**
    * @var string The zero-padded number of iterations for use for the Blowfish alogrithm via crypt.
    */
    const CRYPT_ITERATIONS = '15';

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
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.   
    * @return string
    */
    private static function generateEncryptionKey($plain_keys, $site_key = NULL, $password_salt = NULL) {
        assert('is_array($plain_keys) && !empty($plain_keys)');
        
        if(!isset($site_key)) {
            if(!isset(self::$site_key)) {
                self::$site_key = config('framework')->site_key;
            }
            
            $site_key = self::$site_key;
        }
        
        if(!isset($password_salt)) {
            if(!isset(self::$password_salt)) {
                self::$password_salt = config('framework')->password_salt;
            }
            
            $password_salt = self::$password_salt;
        }
        
        $plain_key = implode($plain_keys);
        
        $salt = strlen($plain_key) . $site_key . $password_salt;
        
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
    * Generates a nonce to use as a salt for encryption.
    * 
    * @param string $sensitive_data The plaintext data to encrypt.
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.
    * @return string
    */
    private static function generateNonceSalt($sensitive_data, $site_key, $password_salt) {
        if(!isset($site_key)) {
            if(!isset(self::$site_key)) {
                self::$site_key = config('framework')->site_key;
            }
            
            $site_key = self::$site_key;
        }
        
        if(!isset($password_salt)) {
            if(!isset(self::$password_salt)) {
                self::$password_salt = config('framework')->password_salt;
            }
            
            $password_salt = self::$password_salt;
        }
        
        $length_nonce = ord($sensitive_data) * strlen(self::$password_salt) * ord(self::$password_salt) * strlen($sensitive_data);
        
        return sha1(self::$password_salt . $sensitive_data . $length_nonce);
    }
    
    /**
    * Encrypts sensitive data based on several plain keys.
    * 
    * @param string $sensitive_data The plaintext data to encrypt.
    * @param array $plain_keys The plain keys to use to generate the encryption key. 
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.
    * @return string The encrypted data base64 encoded.
    */
    public static function encrypt($sensitive_data, $plain_keys, $site_key = NULL, $password_salt = NULL) {
        $encryption_key = self::generateEncryptionKey($plain_keys, $site_key, $password_salt);

        $iv = self::generateIV($encryption_key);
        
        $encrypted_data = mcrypt_encrypt(self::MCRYPT_ALGORITHM, $encryption_key, $sensitive_data, self::MCRYPT_MODE, $iv);
        
        return base64_encode($encrypted_data);
    }
    
    /**
    * Decrypts sensitive data based on several plain keys.
    * 
    * @param string $sensitive_data The encrypted data to decrypt that is base64 encoded.
    * @param array $plain_keys The plain keys to use to generate the decryption key.
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.
    * @return string The sensitive data in plaintext.
    */
    public static function decrypt($sensitive_data, $plain_keys, $site_key = NULL, $password_salt = NULL) {
        $encryption_key = self::generateEncryptionKey($plain_keys, $site_key, $password_salt);
        
        $iv = self::generateIV($encryption_key);
        
        $unencoded_data = base64_decode($sensitive_data);
        
        return mcrypt_decrypt(self::MCRYPT_ALGORITHM, $encryption_key, $unencoded_data, self::MCRYPT_MODE, $iv);
    }
    
    /**
    * Generates a hash of sensitive data based on one or more specified plain keys.
    * 
    * @param string $sensitive_data The encrypted data to generate a has from.
    * @param array $plain_keys The plain keys to use to generate the encryption key.
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.
    * @return string The hash of the sensitive data.
    */
    public static function hash($sensitive_data, $plain_keys, $site_key = NULL, $password_salt = NULL) {
        $encryption_key = self::generateEncryptionKey($plain_keys, $site_key, $password_salt);
        
        $salted_data = self::generateNonceSalt($sensitive_data, $site_key, $password_salt);
    
        return hash_hmac(self::HMAC_ALGORITHM, $salted_data, $encryption_key);
    }
    
    /**
    * Generates a slow hash of sensitive data based on one or more specified plain keys.
    * 
    * @param string $sensitive_data The encrypted data to generate a has from.
    * @param array $plain_keys The plain keys to use to generate the encryption key.
    * @param string $site_key (optional) An override for the framework site key. Defaults to NULL.
    * @param string $password_salt (optional) An override for the framework password salt. Defaults to NULL.
    * @return string The hash of the sensitive data.
    */
    public static function slowHash($sensitive_data, $plain_keys, $site_key = NULL, $password_salt = NULL) {            
        $first_pass = self::hash($sensitive_data, $plain_keys, $site_key, $password_salt);
        
        $salt = substr(self::generateNonceSalt($sensitive_data, $site_key, $password_salt), 0, 22);
        
        $algorithm_iterations = '$2a$' . self::CRYPT_ITERATIONS . '$';
        
        return str_replace($algorithm_iterations, '', crypt($first_pass, "{$algorithm_iterations}{$salt}\$"));
    }
}