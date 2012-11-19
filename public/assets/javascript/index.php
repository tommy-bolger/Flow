<?php
use \Framework\Core\Modes\Asset;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/asset.class.php");

$framework = new Asset('javascript');

$framework->run();