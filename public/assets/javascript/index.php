<?php
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

use \Framework\Core\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/framework.class.php");

$framework = new Framework('web_content');

if($framework->getEnvironment() == 'production') {
    header('Content-Encoding: gzip');

    $javascript_file_name = $_SERVER['QUERY_STRING'];
    
    $javascript_file_path = "{$installation_path}/cache/javascript/{$javascript_file_name}.gz";
    
    $output = '';
    
    if(Framework::$enable_cache) {
        $cache = cache();
        
        $output = $cache->get($javascript_file_name, 'javascript');
        
        if(empty($output)) {
            $output = file_get_contents($javascript_file_path);
            
            $cache->set($javascript_file_name, $output, 'javascript');
        }
    
        echo $output;
    }
    else {
        readfile($javascript_file_path);
    }
}
else {
    readfile(request()->get->file);
}