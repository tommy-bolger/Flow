<?php
use \Framework\Core\Modes\Asset\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/asset/framework.class.php");

$framework = new Framework('css');

$framework->run();