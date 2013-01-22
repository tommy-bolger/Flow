<?php
/**
* Handles the processing of a bbcode formatting string into html.
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

namespace Framework\Data;

class BBCode {
    /**
    * @var object The main bbcode parsing configuration.
    */
    private static $main_resource;
    
    /**
    * @var object The parsing configuration of the current bbcode. Defaults to a copy of the main parsing configuration resource.
    */
    private $instance_resource;
    
    /**
    * @var string The encoded string.
    */
    private $unparsed_string;
    
    /**
    * @var string The decoded string.
    */
    private $parsed_string;
    
    /**
     * Initializes a new instance of BBCode.
     *      
     * @param string $unparsed A raw string containing bbcode markup.
     * @return void
     */
    public function __construct($unparsed_string) {
        if(!isset(self::$main_resource)) {
            self::$main_resource = bbcode_create(array());
        }
        
        $this->instance_resource = self::$main_resource;
        
        $this->unparsed_string = $unparsed_string;
    }
    
    /**
     * Sets or alters parser options.
     * 
     * Full documentation for this function can be found at: http://www.php.net/manual/en/function.bbcode-set-flags.php          
     *      
     * @param integer $flags The flag set that must be applied to the bbcode_container options.
     * @param integer $mode (optional) One of the BBCODE_SET_FLAGS_* constant to set, unset a specific flag set or to replace the flag set by flags.
     * @return void
     */
    public function setFlags($flags, $mode = NULL) {
        if(empty($mode)) {
            $mode = BBCODE_SET_FLAGS_SET;
        }
    
        $success = bbcode_set_flags($this->instance_resource, $flags, $mode);
         
        if(!$success) {
            throw new \Exception("Flags could not be set.");
        }
    }
    
    /**
     * Adds a bbcode tag to the parser.
     * 
     * Full documentation for this function can be found at: http://www.php.net/manual/en/function.bbcode-add-element.php
     *      
     * @param string $tag_name The tag to add.
     * @param array $tag_rules The rules for this tag.
     * @return void
     */
    public function addElement($tag_name, $tag_rules) {
        $success = bbcode_add_element($this->instance_resource);
        
        if(!$success) {
            throw new \Exception("Element '{$tag_name}' could not be added.");
        }
    }
    
    /**
     * Adds a smiley to the bbcode parser.
     * 
     * Full documentation for this function can be found at: http://www.php.net/manual/en/function.bbcode-add-smiley.php
     *      
     * @param string $smiley The smiley to add.
     * @param string $replace_with The string to replace the smiley with.
     * @return void
     */
    public function addSmiley($smiley, $replace_with) {
        $success = bbcode_add_smiley($this->instance_resource, $smiley, $replace_with);
        
        if(!$success) {
            throw new \Exception("Smiley '{$smiley}' could not be added.");
        }
    }
    
    /**
     * Parses the encoded string and retrieves the results.
     *      
     * @return string
     */
    public function toHtml() {
        if(empty($this->parsed_string)) {
            $this->parsed_string = bbcode_parse($this->instance_resource, $this->unparsed_string);
        }
    
        return $this->parsed_string;
    }
}