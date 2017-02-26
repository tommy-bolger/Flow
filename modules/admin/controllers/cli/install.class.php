<?php
namespace Modules\Admin\Controllers\Cli;

use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\Encryption;

class Install
extends Cli {     
    protected function installEncryptionKey() {
        Encryption::installKey();
    }

    public function actionInstall() {
        /*$encrypted = Encryption::encrypt('something', array(
            '12345'
        ));
        
        var_dump($encrypted);
        
        $decrypted = Encryption::decrypt($encrypted, array(
            '12345'
        ));
        
        var_dump($decrypted);*/
        
        $hash = Encryption::hash('something123', array(
            '12345'
        ));
        
        var_dump($hash);
        
        $slow_hash = Encryption::slowHash('something123', array(
            '12345'
        ));
        
        var_dump($slow_hash);
        
        var_dump(Encryption::slowHashNeedsUpdate($slow_hash));
        
        $slow_hash_verified = Encryption::slowHashVerify('something123', $slow_hash, array(
            '12345'
        ));
        
        var_dump($slow_hash_verified);
    }
    
    public function actionInstallEncryptionKey() {
        $this->installEncryptionKey();
    }
}