<?php
use \Framework\Core\Modes\Image;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/modes/image.class.php");

$framework = new Image();

$framework->display();