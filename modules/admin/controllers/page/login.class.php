<?php
/**
* The login page of the Admin module.
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
namespace Modules\Admin\Controllers;

use \Framework\Core\Controller;
use \Framework\Utilities\Http;
use \Framework\Utilities\Auth;
use \Framework\Html\Form\LimitedAttemptsForm;
use \Framework\Modules\ModulePage;

class Login
extends Controller {
    public function __construct() {
        parent::__construct();
    
        if(request()->get->logout == 1) {
            session()->end();
            
            Http::redirect(Http::getTopLevelPageUrl('login', array(), 'admin'));
        }
        
        if(Auth::userLoggedIn()) {
            Http::redirect(Http::getTopLevelPageUrl('', array(), 'admin'));
        }
    }
    
    public function setup() {
        $this->page = new ModulePage('admin', 'admin_login');
        
        $this->page->setTitle('Administration Control Panel - Login');
        
        $this->page->setTemplate('login.php');
        
        $this->page->addMetaTag('page_robots', 'name', 'robots', 'noindex');

        $this->page->addCssFiles(array(
            'reset.css',
            'main.css'
        ));
        
        $this->page->body->addChild($this->getAdminLoginForm());
    }
    
    private function getAdminLoginForm() {
        $login_form = new LimitedAttemptsForm('admin_login_form', NULL, 'post', false);                

        $login_form->captchaAtAttemptNumber(3, "Verify that You're Human (Sorry)");
        
        $login_form->addTextbox('user_name', 'Username');
        
        $login_form->addPassword('password', 'Password');
        
        $login_form->addSubmit('submit', 'Login');
        
        $login_form->setRequiredFields(array('user_name', 'password'));
        
        if(!$login_form->isLocked()) {
            if($login_form->wasSubmitted() && $login_form->isValid()) {
                $login_credentials = $login_form->getData();
                
                if(Auth::userLogin($login_credentials['user_name'], $login_credentials['password'], true)) {
                    Http::redirect(Http::getTopLevelPageUrl());
                }
                else {
                    $login_form->addError('The specified username and password are invalid.');
                }
            }
        }
        
        return $login_form;
    }
    
    public function submit() {    
        $login_form = $this->getAdminLoginForm();
    
        return $login_form->toJsonArray();
    }
}