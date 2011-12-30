<?php
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
header('Content-Encoding: gzip');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

require_once("../../framework/core/Framework.class.php");

$javascript_file_name = $_SERVER['QUERY_STRING'];

$output = '';

if(Framework::$enable_cache) {
    Framework::loadCache();
    
    $cache = new Cache();
    
    $output = $cache->get($javascript_file_name, 'javascript');
    
    if(empty($output)) {
        $output = file_get_contents("../../cache/javascript/{$javascript_file_name}.gz");
        
        $cache->set($javascript_file_name, $output, 'javascript');
    }

    echo $output;
}
else {
    echo file_get_contents("../../cache/javascript/{$javascript_file_name}.gz");
}