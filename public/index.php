<?php  
//Include the framework file
require_once(dirname(__DIR__) . "/framework/core/modes/page/framework.class.php");

//Execute Framework
$web_mode = new \Framework\Core\Modes\Page\Framework();

$web_mode->run();