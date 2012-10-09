<?php  
//Include the framework file
require_once(dirname(__DIR__) . "/framework/core/modes/page.class.php");

//Execute Framework
$web_mode = new \Framework\Core\Modes\Page();

$web_mode->run();