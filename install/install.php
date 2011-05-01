<?php
/**
* The command-line installation script for the framework.
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

print(
    "\nWelcome to the installation for Flow! Before you proceed please go over this checklist:\n\n" . 
    "1. The following directory paths relative to the site root need to be writable recursively for the web user and your user:\n\t" . 
    "- cache/\n\t" .
    "- assets/\n\t" . 
    "- framework/core/ (can be reset to default permissions when this script finishes)\n" . 
    "2. The database you'll be installing to is empty.\n" . 
    "3. Read the README file to go over the packages required by this framework.\n\n" . 
    "Press enter to continue when this checklist has been finished."
);

$continue = trim(fgets(STDIN));

/*
* ----- Config directory path -----
*/
$default_config_directory_path = dirname(__DIR__) . "/framework/core";

config_path:
print("What's the path to the directory where the configuration file will be stored? This must be the same directory as the framework configuration DIST file. ({$default_config_directory_path}): ");

$config_directory_path = trim(fgets(STDIN));

if(empty($config_directory_path)) {
    $config_directory_path = $default_config_directory_path;
}

if(!is_dir($config_directory_path)) {
    print("The directory '{$config_directory_path}' does not exist.\n");
    goto config_path;
}

if(!is_writable($config_directory_path)) {
    print("The directory '{$config_directory_path}' is not writeable. This directory needs to be writeable by PHP for the duration of this install. It can be reverted back to its previous permissions after this is finished.\n");
    goto config_path;
}

/*
* ----- Assets directory path -----
*/
assets_path:
$default_assets_path = dirname(__DIR__) . '/assets';

print("What is the path to the assets directory? ({$default_assets_path}):");

$assets_path = trim(fgets(STDIN));

if(empty($assets_path)) {
    $assets_path = $default_assets_path;
}

if(!is_dir($assets_path)) {
    print("The specified assets path does not exist. Please input the path again.\n");
    goto assets_path;
}

$assets_path = rtrim($assets_path, '/');

assets_directory_writable:
if(!is_writable($assets_path)) {
    print("The specified assets path is not writable. Please make the directory writable and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto assets_directory_writable;
}

assets_subdirectories_writable:
if(!is_writable("{$assets_path}/css")) {
    print("All subdirectories within the specified assets path need to be writable. Please make these subdirectories writable and press enter to check again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto assets_subdirectories_writable;
}

/*
* ----- Cache directory path -----
*/
$default_cache_path = dirname(__DIR__) . "/cache";

cache_path:
print("What's the path to the cache directory? ({$default_cache_path}): ");

$cache_path = trim(fgets(STDIN));

if(empty($cache_path)) {
    $cache_path = $default_cache_path;
}

if(!is_dir($cache_path)) {
    print("The directory '{$cache_path}' does not exist.\n");
    goto cache_path;
}

if(!is_writable($cache_path)) {
    print("The directory '{$cache_path}' is not writeable. Write permissions must be granted to the web server user.\n");
    goto cache_path;
}

$new_configuration_path = "{$config_directory_path}/configuration.ini";

if(is_file($new_configuration_path)) {
    print("The user configuration file '{$new_configuration_path}' for the framework already exists. This site has already been installed. If installation needs to be run again then remove the old configuration.ini, clear the database if it is not empty, and execute this script again.\n");
    
    exit;
}

file_put_contents($new_configuration_path, "cache_base_directory = \"{$cache_path}\"");

/*
* ----- Framework initialization -----
*/
print("Initializing the framework...");

require_once('../framework/core/Framework.class.php');

Framework::runWithoutGUI($new_configuration_path);

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
* ----- Configuration save and reload -----
*/
print("Saving and reloading configuration...");

file_put_contents($new_configuration_path, 
    "\nsite_key = \"{$site_key}\"\n" .
    "password_salt = \"{$password_salt}\"" 
, FILE_APPEND);

config('framework')->load($new_configuration_path);

print("done.\n");

/*
* ----- Database connection configuration -----
*/
print("Now to configure the connection to the PostgreSQL database.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

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
print("The database password: ");

$database_password = trim(fgets(STDIN));

if(empty($database_password)) {
    goto database_password;
}

$database_engine = config('framework')->getParameter('database_engine');

try {
    db('setup')->connect($database_engine, $database_host, $database_name, $database_user, $database_password);
}
catch(Exception $exception) {
    print("Could not connect to the database based on what was given. Please try again.\n");
    goto database_host;
}

print("Database connection was successful.\n- Press enter to continue -");

$continue = trim(fgets(STDIN));

/*
* ----- Database structure creation -----
*/
print("Creating database structure...");

$database_structure_sql = file_get_contents(__DIR__ . "/database.sql");

db('setup')->exec($database_structure_sql);

print("done.\n");

print("Database structure creation was successful. \n- Press enter to continue -");

$continue = trim(fgets(STDIN));

/*
* ----- Configuration save and reload -----
*/
print("Saving and reloading configuration...");

$encrypted_database_password = Encryption::encrypt($database_password, array(
    $database_engine,
    $database_host,
    $database_name,
    $database_user
));

file_put_contents(
    $new_configuration_path, 
    "\ndatabase_host = \"{$database_host}\"\n" . 
    "database_name = \"{$database_name}\"\n" . 
    "database_user = \"{$database_user}\"\n" . 
    "database_password = \"{$encrypted_database_password}\""
, FILE_APPEND);

config('framework')->load($new_configuration_path);

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
print("The user's password: ");

$administrator_password = trim(fgets(STDIN));

if(empty($administrator_password)) {
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

$encrypted_admin_password = Encryption::hash($administrator_password, array($administrator_username, $second_key));

db('setup')->insert('users', array(
    'user_name' => $administrator_username,
    'password' => $encrypted_admin_password,
    'email_address' => $administrator_email,
    'is_site_admin' => true
));

print("done.\n");

print("\nCongratulations! The framework has been setup and the site is ready to be used. Please go to <site_url>?page=AdminLogin in your browser and login as the administrator user you just created to begin managing this install.\n\n");