<?php
/**
* The login page of the Admin module.
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
namespace Modules\Admin\Controllers\Page;

use \Framework\Utilities\Http;
use \Framework\Utilities\Auth;
use \Framework\Html\Form\Form;
use \Framework\Modules\ModulePage;

class Login
extends Admin {           
    public function init() {
        if(Auth::userLoggedIn()) {
            Http::redirect('/admin');
        }
    }
    
    public function authorize() {}
    
    public function validatePost() {
        $this->request->post->validation()->field('user_name')->required();
        $this->request->post->validation()->field('password')->required();
        
        $this->request->post->validation()->validate();
    }
    
    public function actionPost() {        
        if($this->request->post->validation()->valid()) {
            $login_credentials = $this->request->post->validation()->getValidFieldValues();
            
            if(Auth::userLogin($login_credentials['user_name'], $login_credentials['password'], true)) {
                Http::redirect('/admin');
            }
            else {
                $this->request->post->validation()->addError('The specified username and password are invalid.');
            }
        }
    }
    
    public function setup() {
        $this->page = new ModulePage('admin', 'admin_login');
        
        $this->page->setTitle('Administration Control Panel - Login');
        
        $this->page->setTemplate('login.php');

        //Setup the css style        
        $this->page->addCssFiles(array(
            '/bootstrap/dist/css/bootstrap.css'
        ));
        
        //Setup the javascript        
        $this->page->addJavascriptFiles(array(
            "/jquery/dist/jquery.min.js",
            '/popper.js/dist/umd/popper.min.js',
            '/bootstrap/dist/js/bootstrap.min.js'
        ));
    }
    
    public function action() {
        $login_form = new Form('admin_login_form', '/admin/login', 'post', false);
        
        $login_form->addTextbox('user_name', 'Username');
        
        $login_form->addPassword('password', 'Password');
        
        $login_form->addSubmit('submit', 'Login');
    
        $this->page->body()->addChild($login_form, 'admin_login_form');
    }

    public function actionGet() {    
        
    }
}