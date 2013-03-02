<?php
/**
* The conductor class for the cli mode of the framework to handle command line processing.
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
namespace Framework\Core\Modes\Cli;

use \Framework\Core\Framework as BaseFramework;

require_once(dirname(dirname(__DIR__)) . '/framework.class.php');

class Framework
extends BaseFramework {
    /**
    * @var array Stores the arguments passed to the current script.
    */
    public $arguments;

    /**
    * @var boolean Indicates if output should be displayed.
    */
    protected $output_enabled = true;

    /**
     * Initializes an instance of the framework in cli mode.
     *
     * @param string $argument_list The argument list in the format used for getopt().
     * @param boolean $output_on_production Indicates if output should be displayed when running in the production environment.     
     * @return void
     */
    public function __construct($argument_list, $output_on_production = true) {
        parent::__construct('cli');
        
        $this->setArguments($argument_list);        
        
        if($this->environment == 'production' && !$output_on_production) {
            $this->output_enabled = false;
        }
    }
    
    /**
     * Retrieves the options passed to this script.
     *
     * @param string $argument_list The argument list in the format used for getopt().     
     * @return void
     */
    protected function setArguments($argument_list) {
        $arguments = getopt($argument_list);
        
        if(empty($arguments)) {
            throw new \Exception("No arguments were found.");
        }
        
        $this->arguments = new \stdClass();
        
        foreach($arguments as $argument_name => $argument_value) {
            $this->arguments->$argument_name = trim($argument_value);
        }
    }
    
    /**
     * Outputs data.
     *
     * @param string $output.  
     * @return void
     */
    public function cout($output) {
        if($this->output_enabled) {
            echo $output;
        }
    }
    
    /**
     * Outputs data ending with a line break.
     *
     * @param string $output.  
     * @return void
     */
    public function coutLine($output) {
        $this->cout("{$output}\n");
    }
}
