<?php
header('Content-type: text/x-component');
header("Cache-Control: must-revalidate");
header('Content-Encoding: gzip');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

use \Framework\Core\Framework;

$installation_path = dirname(dirname(dirname(__DIR__)));

require_once("{$installation_path}/framework/core/framework.class.php");

$framework = new Framework('web_content');

$pie_file_path = "{$installation_path}/cache/htc/pie.gz";

$output = '';

if(Framework::$enable_cache) {
    $cache = cache();
    
    $output = $cache->get('pie', 'htc');
    
    if(empty($output)) {
        $output = file_get_contents($pie_file_path);
        
        $cache->set('pie', $output, 'htc');
    }

    echo $output;
}
else {
    echo file_get_contents($pie_file_path);
}