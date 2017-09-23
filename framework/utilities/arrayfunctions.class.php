<?php
namespace Framework\Utilities;

class ArrayFunctions {
    public static function extractKeys(array $values_to_extract, array $keys) {        
        $keys = array_flip($keys);
        
        return array_intersect_key($values_to_extract, $keys);
    }
}