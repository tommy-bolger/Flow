<?php
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
header('Content-Encoding: gzip');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

use \Framework\Core\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/framework.class.php");

$framework = new Framework('web_content');

$css_file_name = $_SERVER['QUERY_STRING'];

$css_file_path = "{$installation_path}/cache/css/{$css_file_name}.gz";

$output = '';

if(Framework::$enable_cache) {   
    $cache = cache();
    
    $output = $cache->get($css_file_name, 'css');
    
    if(empty($output)) {
        $output = file_get_contents($css_file_path);
        
        $cache->set($css_file_name, $output, 'css');
    }
    
    echo $output;
}
else {
    echo file_get_contents($css_file_path);
}