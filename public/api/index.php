<?php  
//Include the framework file
require_once(dirname(dirname(__DIR__)) . "/framework/core/modes/api/framework.class.php");

//Execute Framework
$api_mode = new \Framework\Core\Modes\Api\Framework();

$api_mode->run();