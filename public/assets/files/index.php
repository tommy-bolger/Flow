<?php
use \Framework\Core\Modes\File\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/file/framework.class.php");

$framework = new Framework();

$framework->run();