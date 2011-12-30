<?php
/**
* Provides functionality to clean and compress files.
* Copyright (c) 2011, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/
namespace Framework\Utilities;

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
        
        $minified_data = '';
        
        $javascript_minifier = config('framework')->javascript_minifier;
        
        if($javascript_minifier != 'simple') {
            $temp_file = framework()->installation_path . "/cache/{$minify_temp_name}";
            $minified_temp_file = "{$temp_file}_minified";
            
            //Write the unminified data to a temp file
            file_put_contents($temp_file, $all_unminified_data);
        
            switch($javascript_minifier) {
                case 'closure':
                    $compiler_path = escapeshellcmd(config('framework')->closure_compiler_path);

                    if(!empty($compiler_path)) {
                        exec("java -jar {$compiler_path} --js {$temp_file} --js_output_file {$minified_temp_file}");
                    }
                    break;
                case 'uglify-js':
                    exec("uglifyjs -nc -o {$minified_temp_file} {$temp_file}");
                    break;
            }
            
            if(is_file($minified_temp_file)) {
                $minified_data = file_get_contents($minified_temp_file);
                
                unlink($minified_temp_file);
            }
            
            unlink($temp_file);
        }
        
        if(empty($minified_data)) {
            $minified_data = $all_unminified_data;
        }
        
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
        $tidy = tidy_parse_string($unminified_html, array(
            'clean' => 1,
            'bare' => 1,
            'hide-comments' => 1,
            'indent-spaces' => 0,
            'tab-size' => 1,
            'wrap' => 0,
            'preserve-entities' => 1,
            'indent' => 0,
            'break-before-br' => 0
        ), 'utf8');
       
        $minified_html = str_replace(array(
            ">\n<", 
            "EN\"\n  \""
        ), array(
            '><',
            'EN" "'
        ), $tidy);
        
        return gzencode($minified_html, 9);
    }
}