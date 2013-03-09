<?php
use \Framework\Core\Modes\Cli\Framework;
use \Framework\Utilities\File;
use \Framework\Modules\Module;
use \Framework\Minification\Css;
use \Framework\Minification\Javascript;
use \Framework\Minification\Htc;

require_once(dirname(__DIR__) . '/framework/core/modes/cli/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script minifies and compresses asset files.\n" . 
        "\nOptions:\n" . 
        "\n-m <module_name> The name of the module to work in." . 
        "\n-t <type> The asset type to work with. Can be either 'css', 'javascript', or 'htc'." .
        "\n-f <file_name> The file to minify." .
        "\n-h Outputs this help menu." . 
        "\n================================================================================\n"
    );
}

$framework = new Framework('m:t:f:h', false);

if(isset($framework->arguments->h) || empty($framework->arguments->m) || empty($framework->arguments->t) || empty($framework->arguments->f)) {
    display_help();
}

$framework->coutLine("Validating specifying module."); 

$module_name = $framework->arguments->m;

$installed_modules = Module::getInstalledModules();

if(empty($installed_modules[$module_name])) {
    throw new \Exception("Module '{$module_name}' is not an installed module.");
}

$framework->coutLine("Validating asset type.");

$asset_type = $framework->arguments->t;

switch($asset_type) {
    case 'css':
    case 'javascript':
    case 'htc':
        break;
    default:
        throw new \Exception("Asset type '{$asset_type}' can only be either 'css', 'javascript', or 'htc'.");
        break;
}

$framework->coutLine("Compiling file paths.");

$file_name = $framework->arguments->f;

if(strpos($file_name, '.') !== false) {
    $file_name_split = explode('.', $file_name);
    
    $file_name = $file_name_split[0];
}

$file_name = File::sanitizePathSegment($file_name);

$file_path = "{$framework->installation_path}/modules/{$module_name}/cache/{$asset_type}/{$file_name}";

$unminified_file_path = "{$file_path}.tmp";
$file_lock_path = "{$file_path}.lock";

$framework->cout("Is the file already being processed? - ");

//If this file is being processed by another instance then stop execution
if(is_file($file_lock_path)) {
    $framework->coutLine("Yep. Exiting.");
    
    exit;
}

$framework->coutLine("Nope.");

$framework->coutLine("Writing the lock file to disk.");

//Lock this file for processing
file_put_contents($file_lock_path, 'locked');

$framework->cout("Does the temp file exist on disk? - ");

if(!is_file($unminified_file_path)) {
    $framework->coutLine("Nope. Removing lock file and exiting.");

    unlink($file_lock_path);

    throw new \Exception("File '{$unminified_file_path}' is not valid.");
}

$framework->coutLine("Yep.");

$minifier = NULL;

switch($asset_type) {
    case 'css':
        $framework->coutLine("Using the css minifier.");
    
        $minifier = new Css($unminified_file_path);
        
        break;
    case 'javascript':
        $framework->coutLine("Using the javascript minifier.");
    
        $minifier = new Javascript($unminified_file_path);
        
        break;
    case 'htc':
        $framework->coutLine("Using the htc minifier.");
        
        $minifier = new Htc($unminified_file_path);
        
        break;
    default:
        throw new \Exception("Asset type '{$asset_type}' can only be either 'css', 'javascript', or 'htc'.");
        break;
}

$framework->coutLine("Cleaning the unminified data.");

$minifier->clean();

$framework->coutLine("Compressing the minified data.");

$minifier->compress();

$framework->coutLine("Writing the compressed data to disk.");

$compressed_file_path = "{$file_path}.gz";

file_put_contents($compressed_file_path, $minifier->getMinifiedData());

$framework->coutLine("Deleting the temp and lock files.");

unlink($unminified_file_path);
unlink($file_lock_path);