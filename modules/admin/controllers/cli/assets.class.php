<?php
namespace Modules\Admin\Controllers\Cli;

use \Exception;
use \Framework\Utilities\File;
use \Framework\Modules\Module;
use \Framework\Minification\Css;
use \Framework\Minification\Javascript;
use \Framework\Core\Controllers\Cli;

class Assets
extends Cli {
    protected $user_id;
    
    public function actionMinify($module_name, $asset_type, $file_name) {
        $this->framework->coutLine("Validating specifying module."); 

        $installed_modules = Module::getInstalledModules();

        if(empty($installed_modules[$module_name])) {
            throw new Exception("Module '{$module_name}' is not an installed module.");
        }

        $this->framework->coutLine("Validating asset type.");

        switch($asset_type) {
            case 'css':
            case 'javascript':
                break;
            default:
                throw new Exception("Asset type '{$asset_type}' can only be either 'css' or 'javascript'.");
                break;
        }

        $this->framework->coutLine("Compiling file paths.");

        if(strpos($file_name, '.') !== false) {
            $file_name_split = explode('.', $file_name);
            
            $file_name = $file_name_split[0];
        }

        $file_name = File::sanitizePathSegment($file_name);

        $file_path = "{$this->framework->installation_path}/modules/{$module_name}/cache/{$asset_type}/{$file_name}";

        $unminified_file_path = "{$file_path}.tmp";
        $file_lock_path = "{$file_path}.lock";

        $this->framework->cout("Is the file already being processed? - ");

        //If this file is being processed by another instance then stop execution
        if(is_file($file_lock_path)) {
            $this->framework->coutLine("Yep. Exiting.");
            
            exit;
        }

        $this->framework->coutLine("Nope.");

        $this->framework->coutLine("Writing the lock file to disk.");

        //Lock this file for processing
        file_put_contents($file_lock_path, 'locked');

        $this->framework->cout("Does the temp file exist on disk? - ");

        if(!is_file($unminified_file_path)) {
            $this->framework->coutLine("Nope. Removing lock file and exiting.");

            unlink($file_lock_path);

            throw new Exception("File '{$unminified_file_path}' is not valid.");
        }

        $this->framework->coutLine("Yep.");

        $minifier = NULL;

        switch($asset_type) {
            case 'css':
                $this->framework->coutLine("Using the css minifier.");
            
                $minifier = new Css($unminified_file_path);
                
                break;
            case 'javascript':
                $this->framework->coutLine("Using the javascript minifier.");
            
                $minifier = new Javascript($unminified_file_path);
                
                break;
            default:
                throw new Exception("Asset type '{$asset_type}' can only be either 'css' or 'javascript'.");
                break;
        }

        $this->framework->coutLine("Cleaning the unminified data.");

        $minifier->clean();

        $this->framework->coutLine("Compressing the minified data.");

        $minifier->compress();

        $this->framework->coutLine("Writing the compressed data to disk.");

        $compressed_file_path = "{$file_path}.gz";

        file_put_contents($compressed_file_path, $minifier->getMinifiedData());

        $this->framework->coutLine("Deleting the temp and lock files.");

        unlink($unminified_file_path);
        unlink($file_lock_path);
    }
}