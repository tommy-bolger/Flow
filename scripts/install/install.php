<?php
/**
* The command-line installation script for the framework.
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
use \Framework\Utilities\Encryption;
use \Framework\Data\Database;
use \Framework\Modules\Module;

print(
    "\nWelcome to the installation for Flow! Before you proceed please go over this checklist:\n\n" . 
    "1. The following directories relative to the installation path need to be writable recursively for the web user and your user:\n\t" . 
    "- cache/\n\t" .
    "- public/assets/\n\t" . 
    "- protected/ (can be reset to default permissions when this script finishes)\n" . 
    "2. The database you'll be installing to needs to be empty.\n" . 
    "3. Read the README file to go over the packages required by this framework.\n\n" . 
    "Press enter to continue when this checklist has been finished."
);

$continue = trim(fgets(STDIN));

/*
* ----- Framework initialization -----
*/
print("Initializing the framework...");

error_reporting(-1);

require_once('../../framework/core/framework.class.php');

$framework = new Framework();

print("done.\n");

$installation_path = $framework->installation_path;

/*
* ----- Config directory path -----
*/
$config_directory_path = "{$installation_path}/protected";

config_path:
print("Checking to see if the path to the configuration file is writable ({$config_directory_path})...");

if(!is_dir($config_directory_path)) {
    print("\nThe directory '{$config_directory_path}' does not exist. Please create it and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto config_path;
}

if(!is_writable($config_directory_path)) {
    print("\nThe directory '{$config_directory_path}' is not writeable. This directory needs to be writeable by PHP for the duration of this install. It can be reverted back to its previous permissions after this is finished. Please press enter to run the check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto config_path;
}

$new_configuration_path = "{$config_directory_path}/configuration.ini";

if(is_file($new_configuration_path)) {
    print("\nThe user configuration file '{$new_configuration_path}' for the framework already exists. This site has already been installed. If installation needs to be run again then remove the old configuration.ini, clear the database if it is not empty, and execute this script again.\n");
    
    exit;
}

print("done.\n");

/*
* ----- Assets directory path -----
*/
assets_directory_writable:
$assets_path = "{$installation_path}/public/assets";

print("Checking to see if the path to the assets directory is writable ({$assets_path})...");

if(!is_dir($assets_path)) {
    print("\nThe assets path does not exist. Please create it and press enter to run the check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto assets_directory_writable;
}

if(!is_writable($assets_path)) {
    print("\nThe assets path is not writable. Please make the directory writable and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto assets_directory_writable;
}

if(!is_writable("{$assets_path}/css")) {
    print("\nAll subdirectories within the assets path need to be writable. Please make these subdirectories writable and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto assets_directory_writable;
}

print("done.\n");

/*
* ----- Cache directory path -----
*/
$cache_path = "{$installation_path}/cache";

cache_path:
print("Checking to see if the path to the cache directory is writable ({$cache_path})...");

if(!is_dir($cache_path)) {
    print("\nThe directory '{$cache_path}' does not exist. Please create it and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto cache_path;
}

if(!is_writable($cache_path)) {
    print("\nThe directory '{$cache_path}' is not writable. Write permissions must be granted to the web server user. Press enter to run the check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto cache_path;
}

print("done.\n");

/*
* ----- Site key generation -----
*/
print("Generating the site key...");

$site_key = Encryption::generateLongHash();

print("done.\n");

/*
* ----- Password salt -----
*/
password_salt:
print("Please input a password salt: ");

$password_salt = trim(fgets(STDIN));

if(empty($password_salt)) {
    goto password_salt;
}

/*
* ----- Initial configuration set -----
*/
print("Setting the configuration...");

config('framework')->set(array(
    'site_key' => $site_key,
    'password_salt' => $password_salt
));

print("done.\n");

/*
* ----- Database connection configuration -----
*/
print("Now to configure the connection to the database.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

database_engine:
print("The database engine being used (pgsql/mysql/sqlite): ");

$database_engine = trim(fgets(STDIN));

if(empty($database_engine) || ($database_engine != 'pgsql' && $database_engine != 'mysql' && $database_engine != 'sqlite')) {
    goto database_engine;
}

$database_user = '';
$database_password = '';
$database_dsn = '';

if($database_engine != 'sqlite') {
    database_host:
    print("The database host: ");
    
    $database_host = trim(fgets(STDIN));
    
    if(empty($database_host)) {
        goto database_host;
    }

    database_name:
    print("The database name: ");
    
    $database_name = trim(fgets(STDIN));
    
    if(empty($database_name)) {
        goto database_name;
    }
    
    database_user:
    print("The database user: ");
    
    $database_user = trim(fgets(STDIN));
    
    if(empty($database_user)) {
        goto database_user;
    }
    
    database_password:
    $password_prompt = "/usr/bin/env bash -c 'read -s -p \"The database password: \" mypassword && echo \$mypassword'";
    
    $database_password = trim(shell_exec($password_prompt));
    
    echo "\n";
    
    if(empty($database_password)) {
        goto database_password;
    }
    
    database_password_verify:
    $password_verify_prompt = "/usr/bin/env bash -c 'read -s -p \"Re-enter password: \" mypassword && echo \$mypassword'";
    
    $database_password_verify = trim(shell_exec($password_verify_prompt));
    
    echo "\n";
    
    if(empty($database_password_verify)) {
        goto database_password_verify;
    }
    
    if($database_password_verify != $database_password) {
        print("The passwords you specified do not match. Please press enter and try again. -");
        
        $continue = trim(fgets(STDIN));
        
        goto database_password;
    }
    
    $database_dsn = "{$database_engine}:dbname={$database_name};host={$database_host}";
}
else {
    database_path:
    print("The path to the database file: ");
    
    $database_path = trim(fgets(STDIN));
    
    if(empty($database_path)) {
        goto database_path;
    }
    $database_dsn = "{$database_engine}:{$database_path}";
}

try {
    db('setup')->connect($database_dsn, $database_user, $database_password);
}
catch(Exception $exception) {
    print("Could not connect to the database based on what was given. Please try again.\n");
    goto database_host;
}

Database::setDefault('setup');

print("Database connection was successful.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

/*
* ----- Database structure creation -----
*/
print("Creating database structure...");

$database_structure_sql = file_get_contents("{$installation_path}/scripts/install/database/{$database_engine}.sql");

db()->exec($database_structure_sql);

print("done.\n");

print("Database structure creation was successful. \n- Press enter to continue -");

$continue = trim(fgets(STDIN));

/*
* ----- Database password encryption -----
*/
print("Encrypting database password...");

$encrypted_database_password = '';

if(!empty($database_password)) {
    $encrypted_database_password = Encryption::encrypt($database_password, array($database_dsn, $database_user));
}

print("done.\n");

/*
* ----- Configuration file encryption and save -----
*/
print("Encrypting the new configuration and writing it to disk...");

$new_configuration = 
    "site_key = \"{$site_key}\"\n" . 
    "password_salt = \"{$password_salt}\"\n" . 
    "database_dsn = \"{$database_dsn}\"\n" . 
    "database_user = \"{$database_user}\"\n" . 
    "database_password = \"{$encrypted_database_password}\""
;

file_put_contents($new_configuration_path, $new_configuration);

config('framework')->load();

print("done.\n");

/*
* ----- Administrator user creation -----
*/
print("Let's add an administrator user for your site.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

administrator_username:
print("The username for the administrator user (50 characters max): ");

$administrator_username = trim(fgets(STDIN));

if(empty($administrator_username)) {
    goto administrator_username;
}
else {
    if(strlen($administrator_username) > 50) {
        print("Specified username exceeds 50 characters. Please try again.\n");
        goto administrator_username;
    }
}

administrator_password:
//print("The user's password: ");
$password_prompt = "/usr/bin/env bash -c 'read -s -p \"The administrator password: \" mypassword && echo \$mypassword'";

$administrator_password = trim(shell_exec($password_prompt));

echo "\n";

if(empty($administrator_password)) {
    goto administrator_password;
}

administrator_password_verify:
$password_verify_prompt = "/usr/bin/env bash -c 'read -s -p \"Re-enter password: \" mypassword && echo \$mypassword'";

$administrator_password_verify = trim(shell_exec($password_verify_prompt));

echo "\n";

if(empty($administrator_password_verify)) {
    goto administrator_password_verify;
}

if($administrator_password_verify != $administrator_password) {
    print("The passwords you specified do not match. Please press enter and try again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto administrator_password;
}

administrator_email:
print("The email address for the administrator user: ");

$administrator_email = trim(fgets(STDIN));

if(empty($administrator_email) || filter_var($administrator_email, FILTER_VALIDATE_EMAIL) === false) {
    goto administrator_email;
}

/*
* ----- Administrator user record insert -----
*/
print("Adding administrator user to the database...");

$second_key = strlen($administrator_username) * strlen($administrator_password);

$encrypted_admin_password = Encryption::slowHash($administrator_password, array($administrator_username, $second_key));

db()->insert('cms_users', array(
    'user_name' => $administrator_username,
    'password' => $encrypted_admin_password,
    'email_address' => $administrator_email,
    'is_site_admin' => 1
));

print("done.\n");

/*
* ----- Module installation -----
*/
print("Let's setup which modules you want to install.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

//Retrieve the list of available modules on the filesystem
$modules = Module::getInstalledModules();

$modules_list = implode(', ', $modules);

modules:
print("Please specify a comma-separated list of modules you want to install ({$modules_list}): ");

$specified_modules = trim(fgets(STDIN));

if(empty($specified_modules)) {
    goto modules;
}

$modules_to_install = explode(',', $specified_modules);

$invalid_modules = array();

//Validate the specified modules
foreach($modules_to_install as &$module) {
    if(!in_array($module, $modules_to_install)) {
        $invalid_modules[] = $module;
    }
    else {
        $module = trim($module);
    }
}

if(!empty($invalid_modules)) {
    print("Specified modules '" . implode(', ', $invalid_modules) . "' are not valid. Please try again.\n");
        
    goto modules;
}

print("Enabling specified modules...");

//Start the sort order at two since the admin module takes the first by default
$sort_order = 2;

foreach($modules_to_install as $module) {
    ob_start();
            
    //Parse the module's database install script
    include("{$installation_path}/modules/{$module}/scripts/install/database/{$database_engine}.php");
    
    $module_database_script = ob_get_clean();

    //Execute the module's database install script    
    db()->exec($module_database_script);
    
    $sort_order += 1;
}

print("done.\n");

print("\nCongratulations! The CMS has been setup and the site is ready to be used. Please navigate to the new site's url in your browser and login as the administrator user you just created to begin managing this install.\n\n");