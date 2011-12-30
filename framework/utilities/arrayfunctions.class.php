<?php
namespace Framework\Utilities;

class ArrayFunctions {
    public static function extractKeys($values_to_extract, $keys) {
        assert('is_array($values_to_extract) && !empty($values_to_extract)');
        assert('is_array($keys) && !empty($keys)');
        
        $keys = array_flip($keys);
        
        return array_intersect_key($values_to_extract, $keys);
    }
}