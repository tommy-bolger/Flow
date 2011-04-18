<?php
/**
* Provides functionality to clean and compress files.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Minify {
    /**
    * Loads and returns all unminified data.
    * 
    * @param array $unminified_data A list of file paths of all files that need to be loaded.
    * @return string The loaded unminified data.
    */
    private static function getUnminifiedData($unminified_data) {
        assert('is_array($unminified_data)');
    
        $all_unminified_data = '';
    
        if(is_array($unminified_data)) {
            foreach($unminified_data as $file) {
                $all_unminified_data .= file_get_contents($file) . "\n";
            }
        }
        else {
            $all_unminified_data = $unminified_data;
        }
        
        return $all_unminified_data;
    }

    /**
    * Minifies javascript files into a single gzipped string.
    * 
    * @param mixed $unminified_data A list of file paths as an array or the entire contents of the unminified data as a string.
    * @param string $minify_temp_name The name to use for any temp files generated.
    * @return string The minified javascript.
    */
    public static function minifyJavascript($unminified_data, $minify_temp_name) {        
        $all_unminified_data = '';
    
        if(is_array($unminified_data)) {
            $all_unminified_data = self::getUnminifiedData($unminified_data);
        }
        else {
            $all_unminified_data = $unminified_data;
        }

        //Javascript special characters that cause issues with php_strip_whitespace()
        $special_characters = array(
            'END_TAG_PLACEHOLDER' => '?' . '>', 
            'POUND_SIGN' => '#', 
            'SINGLE_QUOTE' => "'",
            'DOUBLE_QUOTE' => '"',
            'NEW_LINE' => "\n",
            'SPECIAL_SLASH' => '*/*',
            'URL_DOUBLE_SLASH' => '://'
        );
        
        $special_character_placeholders = array_keys($special_characters);
        
        //Replace javascript special characters with their placeholders
        $all_unminified_data = str_replace($special_characters, $special_character_placeholders, $all_unminified_data);
        
        //Prepend the php opening tag to the data.
        $temp_file_contents = "<?php\n{$all_unminified_data}";
        
        //Write the content to a temp file in the main file cache directory
        $temp_file_path = file_cache()->getCacheDirectoryPath() . $minify_temp_name;
        
        file_put_contents($temp_file_path, $temp_file_contents);
        
        //Load the written temp file back into memory, stripping whitespace and comments
        $minified_data = php_strip_whitespace($temp_file_path);
        
        unlink($temp_file_path);
        
        //Add the opening php tag to the list of special characters so it will be removed
        $special_character_placeholders[] = "<?php\n";
        $special_characters[] = ''; 
        
        //Replace javascript placeholders with their special character
        $special_characters['NEW_LINE'] = '';
        
        $minified_data = trim(str_replace($special_character_placeholders, $special_characters, $minified_data));
        
        //Gzip the minified javascript to its maximum level and return    
        return gzencode($minified_data, 9);
    }
    
    /**
    * Minifies css files into a single gzipped string.
    * 
    * @param mixed $unminified_data A list of file paths as an array or the entire contents of the unminified data as a string.
    * @return string The minified css.
    */
    public static function minifyCss($unminified_data) {
        $all_unminified_data = '';
    
        if(is_array($unminified_data)) {
            $all_unminified_data = self::getUnminifiedData($unminified_data);
        }
        else {
            $all_unminified_data = $unminified_data;
        }
    
        /*
            The following code was adapted from this page:
            http://www.php.net/manual/en/function.php-strip-whitespace.php#74899
        */
        $replace = array(
            "#/\*.*?\*/#s" => "",  //Strip C style comments.
            "#\s\s+#" => " " //Strip excess whitespace.
        );
        
        $search = array_keys($replace);
        $minified_data = preg_replace($search, $replace, $all_unminified_data);
        
        $replace = array(
            ": "  => ":",
            "; "  => ";",
            " {"  => "{",
            " }"  => "}",
            ", "  => ",",
            "{ "  => "{",
            ";}"  => "}", //Strip optional semicolons.
            ",\n" => ",", //Don't wrap multiple selectors.
            "\n}" => "}", //Don't wrap closing braces.
             "\t"  => "", //Put all of the text on the same line
            "\n"  => "", //Put all of the text on the same line
            "\r"  => "" //Put all of the text on the same line
        );
        
        $search = array_keys($replace);
        $minified_data = trim(str_replace($search, $replace, $minified_data));
        
        return gzencode($minified_data, 9);
    }
    
    /**
    * Minifies html into a gzipped string.
    * 
    * @param mixed $unminified_html The html to minify.
    * @return string The minified html.
    */
    public static function minifyHtml($unminified_html) {
        /*
            Regular expression '#\s*<!--[^\[<>].*?(?<!!)-->\s*#s' found at:
            http://www.sitepoint.com/forums/php-34/regex-pattern-strip-html-comments-but-leave-conditonals-696559.html#post4678725
            
            Regular expression '/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/' found at:
            http://www.blog.highub.com/regular-expression/php-regex-regular-expression/php-regex-remove-whitespace-from-html/comment-page-1/#comment-987
        */
        $minified_data = preg_replace(array(
            '#\s*<!--[^\[<>].*?(?<!!)-->\s*#s', 
            '/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)|(?<=\/\>)(\s+)/'
        ), '', $unminified_html);
        
        return gzencode(trim($minified_data), 9);
    }
}