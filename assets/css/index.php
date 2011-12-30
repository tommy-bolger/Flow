<?php
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
header('Content-Encoding: gzip');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

require_once("../../framework/core/Framework.class.php");

$css_file_name = $_SERVER['QUERY_STRING'];

$output = '';

if(Framework::$enable_cache) {
    Framework::loadCache();
    
    $cache = new Cache();
    
    $output = $cache->get($css_file_name, 'css');
    
    if(empty($output)) {
        $output = file_get_contents("../../cache/css/{$css_file_name}.gz");
        
        $cache->set($css_file_name, $output, 'css');
    }
    
    echo $output;
}
else {
    echo file_get_contents("../../cache/css/{$css_file_name}.gz");
}