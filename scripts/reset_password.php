<?php
use \Framework\Core\Modes\Cli\Framework;
use \Framework\Utilities\Encryption;

require_once(dirname(__DIR__) . '/framework/core/modes/cli/framework.class.php');

function display_help() {
    die(
        "\n================================================================================\n" . 
        "\nThis script reset's a user's password via the command line.\n" . 
        "\nOptions:\n" . 
        "\n-h Outputs this help menu." . 
        "\n================================================================================\n"
    );
}

$framework = new Framework('h');

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

$admin_user_id = db()->getOne("
    SELECT user_id
    FROM cms_users
    WHERE user_name = ?   
", array(
    $administrator_username
));

if(empty($admin_user_id)) {
    print("The username you entered doesn't exist. Please press enter and try again. -");
    
    $continue = trim(fgets(STDIN));
    
    goto administrator_username;
}

administrator_password:

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

$second_key = strlen($administrator_username) * strlen($administrator_password);

$encrypted_admin_password = Encryption::slowHash($administrator_password, array($administrator_username, $second_key));

db()->update('cms_users', array(
    'password' => $encrypted_admin_password
), array(
    'user_id' => $admin_user_id
));

$framework->coutLine("Done!");