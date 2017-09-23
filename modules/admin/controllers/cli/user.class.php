<?php
namespace Modules\Admin\Controllers\Cli;

use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\Encryption;

class User
extends Cli {
    protected $user_id;

    public function checkIfUsernameExists($user_name) {
        if(strlen($user_name) > 50) {
            $this->framework->getInput("Specified username exceeds 50 characters. Please try again. -", false);
            
            return false;
        }
    
        $this->user_id = db()->getOne("
            SELECT user_id
            FROM cms_users
            WHERE user_name = :user_name   
        ", array(
            ':user_name' => $user_name
        ));
        
        $user_exists = !empty($this->user_id);
        
        if(!$user_exists) {
            $this->framework->getInput("The username you entered doesn't exist. Please press enter and try again. -", false);
            
            return false;
        }
        
        return $user_exists;
    }

    public function actionResetPassword() {
        $user_name = $this->framework->getInput('The username for the user user (50 characters max):', true, array(
            $this,
            'checkIfUsernameExists'
        ));
        
        $new_password = NULL;
        
        $new_password_verified = false;
        
        while(!$new_password_verified) {
            $new_password = $this->framework->getInput("The new password for the user:", true, array(), true);
        
            $verify_new_password = $this->framework->getInput("Re-enter the new password:", true, array(), true);
            
            if($new_password == $verify_new_password) {
                $new_password_verified = true;
            }
            else {
                $this->framework->getInput("The passwords you specified do not match. Please press enter and try again. -", false);
            }
        }
        
        $second_key = strlen($user_name) + strlen($new_password);

        $encrypted_password = Encryption::slowHash($new_password, array($user_name, $second_key));

        db()->update('cms_users', array(
            'password' => $encrypted_password
        ), array(
            'user_id' => $this->user_id
        ));

        $this->framework->coutLine("Done!");
    }
}