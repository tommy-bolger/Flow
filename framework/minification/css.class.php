<?php
/**
* Cleans up and compresses css.
* Copyright (c) 2012, Tommy Bolger
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
namespace Framework\Minification;

class Css
extends Minifier {
    /**
    * Strips the unminified css of any unnecessary characters.
    * 
    * @return void
    */
    public function clean() {
        $all_unminified_data = $this->getUnminifiedData();
    
        /*
            The following code was adapted from this page:
            http://www.php.net/manual/en/function.php-strip-whitespace.php#74899
        */
        $replace = array(
            "#/\*.*?\*/#s" => "",  //Strip C style comments.
            "#\s\s+#" => " " //Strip excess whitespace.
        );
        
        $search = array_keys($replace);
        $this->minified_data = preg_replace($search, $replace, $all_unminified_data);
        
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
        $this->minified_data = trim(str_replace($search, $replace, $this->minified_data));
    }
}