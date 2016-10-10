<?php
/**
* The base error handling class of the framework for PHP 7.0 and greater.
* Copyright (c) 2016, Tommy Bolger
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
namespace Framework\Core;

use \Throwable;

class Error
extends ErrorLegacy {
    /**
     * Sets the framework exception handler.
     *
     * @return void
     */
    protected function setExceptionHandler() {
        set_exception_handler(array($this, 'handleThrowable'));
    }

    /**
     * The exception handler of the framework.
     *
     * @return void
     */
    public function handleThrowable(Throwable $throwable) {    
        $previous_throwable = $throwable->getPrevious();
        
        if(!empty($previous_throwable)) {
            $this->handleError(
                $previous_throwable->getCode(),
                $previous_throwable->getMessage(),
                $previous_throwable->getFile(),
                $previous_throwable->getLine(),
                $previous_throwable->getTraceAsString()
            );
        }
        else {
            $this->handleError(
                $throwable->getCode(),
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine(),
                $throwable->getTraceAsString()
            );
        }
    }
}