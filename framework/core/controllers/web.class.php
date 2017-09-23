<?php
/**
* The base class that all web controllers extend from.
* Copyright (c) 2017, Tommy Bolger
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
namespace Framework\Core\Controllers;

class Web 
extends Controller {
    /**
    * @var object An instance of the request.
    */
    protected $request;
    
    /**
     * Initializes a new instance of this controller.
     *
     * @param string $module_name The name of the module this controller is a part of.
     * @return void
     */
    public function __construct($module_name) {
        parent::__construct($module_name);
    
        $this->request = $this->framework->getRequest();
    }

    /**
     * Executes any authorization logic.
     *
     * @return void
     */
    public function authorize() {}
    
    /**
     * Executes any access logic.
     *
     * @return void
     */
    public function access() {}
    
    /**
     * Executes any default validation logic.
     *
     * @return void
     */
    public function validate() {}
    
    /**
     * Executes any validation for GET requests.
     *
     * @return void
     */
    public function validateGet() {}
    
    /**
     * Executes any validation for POST requests.
     *
     * @return void
     */
    public function validatePost() {}
    
    /**
     * Executes any validation for PUT requests.
     *
     * @return void
     */
    public function validatePut() {}
    
    /**
     * Executes any validation for DELETE requests.
     *
     * @return void
     */
    public function validateDelete() {}

    /**
     * Executes a GET action.
     *
     * @return void
     */
    public function actionGet() {}
    
    /**
     * Executes a POST action.
     *
     * @return void
     */
    public function actionPost() {}
    
    /**
     * Executes a DELETE action.
     *
     * @return void
     */
    public function actionDelete() {}
    
    /**
     * Executes a PUT action.
     *
     * @return void
     */
    public function actionPut() {}
}