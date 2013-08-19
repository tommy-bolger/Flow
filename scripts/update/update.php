<?php
/**
* The command-line update script to migrate from one version to a later version.
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

use \Framework\Core\Framework;
use \Framework\Modules\Module;

require_once(dirname(dirname(__DIR__)) . '/framework/core/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script will run all php or database sql update scripts for a specific module or the framework to update to a target version.\n" . 
        "\nOptions:\n" . 
        "\n-m <module_name> (optional) The name of the module to update. If left blank then framework updates (located under <installation_path>/scripts/update) will be run." . 
        "\n-t <version> The version to update to." . 
        "\n-h Outputs this help menu." . 
        "\n================================================================================\n"
    );
}

/**
 * Compile Files for APC
 *
 * This function was adapted from: http://blog.digitalstruct.com/2008/01/31/performance-tuning-overview/
 *  
 * @param string $starting_directory The absolute path to the starting directory to begin scanning.
 * @return void
 */
function compile_files($starting_directory) {
    $directories = glob($starting_directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    
    if(!empty($directories)) {
        foreach($directories as $directory) {
            compile_files($directory);
        }
    }
 
    $files = glob($starting_directory . DIRECTORY_SEPARATOR . '*.php');
    
    if(!empty($files)) {
        foreach($files as $file) {
            echo "Compiling {$file}.\n";
            apc_compile_file($file);
        }
    }
}

$framework = new Framework('cli');

$installation_path = $framework->installation_path;

$arguments = getopt('m:t:h');

$module_name = NULL;
$module_id = NULL;
$module_folder = '';

if(!empty($arguments['m'])) {
    $module_name = $arguments['m'];
    
    $module_folder = "/modules/{$module_name}";
    
    $module_id = db()->getOne("
        SELECT module_id
        FROM cms_modules
        WHERE module_name = ?
    ", array($module_name));
}

$update_to = NULL;

if(!empty($arguments['t'])) {
    $update_to = $arguments['t'];
}

if(empty($update_to)) {
    display_help();
}

echo "Initializing...\n";

$update_from = '';

if(!empty($module_name)) {
    $module = new Module($module_name);

    $update_from = $module->configuration->version;
}
else {
    $update_from = $framework->configuration->version;
}

if($update_from == $update_to) {
    die("Already at version {$update_to}. Exiting.\n");
}

$database_dsn = $framework->configuration->database_dsn;

$database_engine = substr($database_dsn, 0, strpos($database_dsn, ':'));

$update_base_path = "{$installation_path}{$module_folder}/scripts/update";

echo "Retrieving versions between the current and the target.\n";

$version_directories = scandir($update_base_path);

if(!in_array($update_to, $version_directories)) {
    throw new Exception("Update folder for version '{$update_to}' could not be found in '{$update_base_path}'.");
}

sort($version_directories);

$in_update_range = false;

if(!in_array($update_from, $version_directories)) {
    $in_update_range = true;
}

foreach($version_directories as $version_directory) {
    $version_path = "{$update_base_path}/{$version_directory}";

    if(is_dir($version_path) && $version_directory != '.' && $version_directory != '..') {
        if($in_update_range) {
            echo "Running updates for version '{$version_directory}'.\n";
            
            $version_data = db()->getData('cms_versions', array(
                'version_id',
                'finished'
            ), array(
                'module_id' => $module_id,
                'version' => $version_directory
            ));
            
            if(!empty($version_data[0])) {
                $version_data = $version_data[0];
            }
        
            if(empty($version_data['finished'])) {
                $version_id = NULL;
                
                if(empty($version_data['version_id'])) {
                    $version_id = db()->insert('cms_versions', array(
                        'module_id' => $module_id,
                        'version' => $version_directory
                    ));
                }
                else {
                    $version_id = $version_data['version_id'];
                }
            
                $update_types = scandir($version_path);
                
                foreach($update_types as $update_type) {
                    if($update_type != '.' && $update_type != '..') {
                        $update_type_id = db()->getOne("
                            SELECT update_type_id
                            FROM cms_update_types
                            WHERE update_type = ?
                        ", array($update_type));
                        
                        if(empty($update_type_id)) {
                            $update_type_id = db()->insert('cms_update_types', array('update_type' => $update_type));
                        }
                    
                        $update_type_path = '';
                        
                        if($update_type == 'database') {
                            $update_type_path = "{$version_path}/database/{$database_engine}";
                        }
                        else {
                            $update_type_path = "{$version_path}/{$update_type}";
                        }
                    
                        if(is_dir($update_type_path) && is_readable($update_type_path)) {
                            echo "Running updates for directory '{$update_type_path}'.\n";
                        
                            $update_files = scandir($update_type_path);
                        
                            sort($update_files);
                            
                            foreach($update_files as $update_file) {
                                if($update_file != '.' && $update_file != '..') {
                                    $update_file_path = "{$update_type_path}/{$update_file}";
                                    
                                    if(is_file($update_file_path)) {
                                        echo "Running update '{$update_file}'.\n";
                                    
                                        $update_data = db()->getData('cms_updates', array(
                                            'update_id',
                                            'run'
                                        ), array(
                                            'module_id' => $module_id,
                                            'version_id' => $version_id,
                                            'update_type_id' => $update_type_id,
                                            'update_file' => $update_file
                                        ));
                                        
                                        if(!empty($update_data[0])) {
                                            $update_data = $update_data[0];
                                        }
                                        
                                        $update_id = NULL;
                                        
                                        if(empty($update_data['update_id'])) {
                                            $update_id = db()->insert('cms_updates', array(
                                                'module_id' => $module_id,
                                                'version_id' => $version_id,
                                                'update_type_id' => $update_type_id,
                                                'update_file' => $update_file
                                            ));
                                        }
                                        else {
                                            $update_id = $update_data['update_id'];
                                        }
                                    
                                        if(empty($update_data['run'])) {
                                            db()->beginTransaction();
                                                                                
                                            if(strpos($update_file, '.php') !== false) {
                                                echo "Running PHP file '{$update_file}'.\n";
                                            
                                                include($update_file_path);
                                            }
                                            elseif($update_type == 'database' && strpos($update_file, '.sql') !== false) {                
                                                $database_update = file_get_contents($update_file_path);
                                                
                                                echo "Running SQL file '{$update_file}'.\n";
                                                
                                                db()->exec($database_update);
                                            }
                                            else {
                                                echo "File '{$update_file}' is not a supported type. Skipping.\n";
                                            }
                                            
                                            db()->update('cms_updates', array('run' => 1), array('update_id' => $update_id));
                                            
                                            db()->commit();
                                        }
                                        else {
                                            echo "Update '{$update_file}' has already been run. Skipping.\n";
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            echo "Path '{$update_type_path}' is not a directory or not readable. Skipping. \n";
                        }
                    }
                }
                
                db()->update('cms_versions', array('finished' => 1), array('version_id' => $version_id));
            }
            else {
                echo "Version '{$version_directory}' has already been finished. Skipping.\n";
            }
        }
        
        if($version_directory == $update_from) {
            $in_update_range = true;
        }
        
        if($version_directory == $update_to) {    
            $in_update_range = false;
        }
    }
}

echo "Modifying the database to reflect the new version.\n";

if(empty($module_name)) {
    db()->update('cms_configuration_parameters', array('value' => $update_to), array('parameter_name' => 'version'));
}
else {
    db()->query("
        UPDATE cms_configuration_parameters
        SET value = ?
        WHERE module_id = ?
            AND parameter_name = 'version'
    ", array(
        $update_to,
        $module_id
    ));
}

//Clear memory cache if enabled
if($framework->enable_cache) {
    echo "Clearing memory cache.\n";
    
    cache()->clear();
}

echo "Clearing cached files.\n";

//Clear cached files
if(!empty($module_name)) {    
    file_cache($module_name)->clear();
}
else {
    $modules = Module::getInstalledModules();

    foreach($modules as $module) {
        file_cache($module)->clear();
    }
}

//If APC is installed then refresh the opcode cache
$loaded_extensions = get_loaded_extensions();

if(in_array('apc', $loaded_extensions)) {
    apc_clear_cache();
    
    compile_files($installation_path);
}

echo "Update successful!\n";