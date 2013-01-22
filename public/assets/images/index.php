<?php
use \Framework\Core\Modes\Image\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/image/framework.class.php");

$framework = new Framework();

$framework->run();