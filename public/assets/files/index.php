<?php
use \Framework\Core\Modes\File;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/file.class.php");

$framework = new File();

$framework->run();