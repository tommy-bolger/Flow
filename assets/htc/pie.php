<?php
header('Content-type: text/x-component');
header("Cache-Control: must-revalidate");
header('Content-Encoding: gzip');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 864000) . " GMT");

require_once("../../framework/core/Framework.class.php");

$output = '';

if(Framework::$enable_cache) {
    Framework::loadCache();
    
    $cache = new Cache();
    
    $output = $cache->get('pie', 'htc');
    
    if(empty($output)) {
        $output = file_get_contents("../../cache/htc/pie.gz");
        
        $cache->set('pie', $output, 'htc');
    }

    echo $output;
}
else {
    echo file_get_contents("../../cache/htc/pie.gz");
}