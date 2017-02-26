<?php
namespace Modules\Admin\Controllers\Cli;

use \Framework\Core\Controllers\Cli;
use \Framework\Utilities\Encryption;

class Configuration
extends Cli {     
    public function actionSetEncryptedValue($name, $module = NULL) {
        $parameter_record = array();
        
        if(!empty($module)) {
            $parameter_record = db()->getRow("
                SELECT *
                FROM cms_configuration_parameters ccp
                JOIN cms_modules cm ON cm.module_id = ccp.module_id
                WHERE cm.module_name = :module_name
                    AND ccp.parameter_name = :parameter_name
            ", array(
                ':module_name' => $module,
                ':parameter_name' => $name
            ));
        }
        else {
            $parameter_record = db()->getRow("
                SELECT *
                FROM cms_configuration_parameters
                WHERE module_id IS NULL
                    AND parameter_name = :parameter_name
            ", array(
                ':parameter_name' => $name
            ));
        }
        
        if(!empty($parameter_record)) {
            $value = $this->framework->getInput('Please specify your plaintext data');
        
            db()->update('cms_configuration_parameters', array(
                'value' => Encryption::encrypt($value)
            ), array(
                'configuration_parameter_id' => $parameter_record['configuration_parameter_id']
            ));
        }
    }
    
    public function actionResetDatabasePassword() {
        $new_password = $value = $this->framework->getInput('Please specify your new password');
        
        $encrypted_password = Encryption::encrypt($new_password);
        
        $this->framework->configuration->database_password = $encrypted_password;
        
        $this->framework->configuration->writeBaseFile();
    }
}