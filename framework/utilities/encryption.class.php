<?php
/**
* Provides functionality to perform encryption, decryption, and hashing operations on sensitive data. Requires libsodium and Halite.
*
* Documentation can be found at https://github.com/paragonie/halite/tree/master/doc
*
* Copyright (c) 2017, Tommy Bolger
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

use \Exception;
use \ParagonIE\Halite\KeyFactory;
use \ParagonIE\Halite\Symmetric\Crypto as SymmetricEncryption;
use \ParagonIE\Halite\Password;
use \ParagonIE\Halite\HiddenString;
use \Framework\Core\Framework;
use \Framework\Core\Configuration;
use \Framework\Core\Loader;

class Encryption {    
    /**
    * @var string The hash algorithm to use for hash_hmac.
    */
    const HMAC_ALGORITHM = 'sha512';
    
    /**
    * @var integer The number of bytes to use when generating cryptographically secure strings.
    */
    const NUMBER_OF_BYTES = 64;
    
    /**
    * @var string The initial salt generated by the framework to use with all hashing operations.
    */
    protected static $base_salt;
    
    /**
    * Generates and returns random bytes.
    * 
    * @return mixed
    */
    protected static function generateBytes() {
        return random_bytes(static::NUMBER_OF_BYTES);
    }

    /**
    * Generates and retrieves a sha1 hash based on a random value.
    * 
    * @return string
    */
    public static function generateShortHash() {    
        return sha1(bin2hex(static::generateBytes()));
    }
    
    /**
    * Generates and retrieves a sha256 hash based on a random value.
    * 
    * @return string
    */
    public static function generateMediumHash() {    
        return hash('sha256', bin2hex(static::generateBytes()));
    }
    
    /**
    * Generates and retrieves a sha512 hash based on a random value.
    * 
    * @return string
    */
    public static function generateLongHash() {
        return hash(static::HMAC_ALGORITHM, bin2hex(static::generateBytes()));
    }
    
    /**
    * Retrieves the encryption key path
    * 
    * @return void
    */
    protected static function getKeyPath() {
        return Framework::getInstance()->getInstallationPath() . '/protected/encryption.key';
    }
    
    /**
    * Retrieves the encryption key path
    * 
    * @return void
    */
    protected static function loadKey() {  
        return KeyFactory::loadEncryptionKey(static::getKeyPath());
    }
    
    /**
    * Installs a new encryption key for use by the framework.
    * 
    * @return void
    */
    public static function installKey() {
        $key_path = static::getKeyPath();
        
        if(is_file($key_path)) {
            throw new Exception("Encryption key '{$key_path}' already exists.");
        }
    
        KeyFactory::save(KeyFactory::generateEncryptionKey(), $key_path);
        
        chmod($key_path, '0660');
    }
    
    /**
    * Encrypts sensitive data based the generate site key.
    * 
    * @param string $sensitive_data The plaintext data to encrypt.
    * @return string The encrypted data.
    */
    public static function encrypt($sensitive_data) {
        $encryption_key = static::loadKey();

        $encrypted_data = SymmetricEncryption::encrypt(new HiddenString($sensitive_data), $encryption_key);
        
        return $encrypted_data;
    }
    
    /**
    * Decrypts sensitive data based on the generated site key.
    * 
    * @param string $encrypted_data The encrypted data to decrypt.
    * @return string The sensitive data in plaintext.
    */
    public static function decrypt($encrypted_data) {
        $encryption_key = static::loadKey();

        $decrypted_data = SymmetricEncryption::decrypt($encrypted_data, $encryption_key);
        
        return $decrypted_data->getString();
    }
    
    /**
    * Sets the base salt used by the framework in all hashing operations.
    * 
    * @param string $encrypted_data The encrypted data to decrypt.
    * @return string The sensitive data in plaintext.
    */
    public static function setBaseSalt($base_salt) {
        static::$base_salt = $base_salt;
    }
    
    /**
    * Adds salt to sensitive data.
    * 
    * @param string $sensitive_data The data to generate a hash from.
    * @param array $salt Any salt to apply the password.
    * @return string The hash of the sensitive data.
    */
    protected static function addSalt($sensitive_data, array $salt) {  
        if(!empty(static::$base_salt)) {
            $sensitive_data = static::$base_salt . ";{$sensitive_data}";
        }
    
        if(!empty($salt)) {
            $salt_string = implode(';', $salt);
        
            $sensitive_data = "{$sensitive_data};{$salt_string}";
        }
        
        return $sensitive_data;
    }
    
    /**
    * Generates a hash of salted sensitive data using SHA512.
    * 
    * @param string $sensitive_data The plaintext data to generate a hash from.
    * @param array $salt Any salt to apply the password. Defaults to an empty array for no additional salt.
    * @return string The hash of the sensitive data.
    */
    public static function hash($sensitive_data, array $salt = array()) {
        $encryption_key = static::loadKey();
        
        $salted_data = static::addSalt($sensitive_data, $salt);
    
        return hash_hmac(static::HMAC_ALGORITHM, $salted_data, $encryption_key);
    }
    
    /**
    * Generates a hash of salted sensitive using Argon2i in libsodium.
    * 
    * @param string $sensitive_data The plaintext data to generate a hash from.
    * @param array $salt Any salt to apply the password. Defaults to an empty array for no additional salt.
    * @return string The hash of the sensitive data.
    */
    public static function slowHash($sensitive_data, array $salt = array()) { 
        $encryption_key = static::loadKey();
        
        $salted_data = static::addSalt($sensitive_data, $salt);
    
        return Password::hash(new HiddenString($salted_data), $encryption_key);
    }
    
    /**
    * Indicates if a slow hash needs to be updated.
    * 
    * @param string $slow_hash The slow hash to check.
    * @return boolean
    */
    public static function slowHashNeedsUpdate($slow_hash) { 
        $encryption_key = static::loadKey();
        
        return Password::needsRehash($slow_hash, $encryption_key, KeyFactory::INTERACTIVE);
    }
    
    /**
    * Verifies that a hash matches a given plantext value.
    * 
    * @param string $sensitive_data The plaintext data to generate a hash from.
    * @param string $slow_hash The hash to verify.
    * @param array $salt Any salt to apply the password. Defaults to an empty array for no additional salt.
    * @return boolean A flag indicating if the hash is matches the given plaintext value.
    */
    public static function slowHashVerify($sensitive_data, $slow_hash, array $salt = array()) {            
        $encryption_key = static::loadKey();
        
        $salted_data = static::addSalt($sensitive_data, $salt);
        
        return Password::verify(new HiddenString($salted_data), new HiddenString($slow_hash), $encryption_key);
    }
}