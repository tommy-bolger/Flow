<?php  
//Include the framework file
require_once(dirname(dirname(__DIR__)) . "/framework/core/modes/ajax/framework.class.php");

//Execute Framework
$ajax_mode = new \Framework\Core\Modes\Ajax\Framework();

$ajax_mode->run();