<?php
/**
* The login page of the Admin module.
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
class AdminLogin
extends Module {
    protected $name = __CLASS__;

    protected $title = "Administration Control Panel";

    public function __construct() {
        parent::__construct('admin');
        
        if(request()->get->logout == 1) {
            session()->end();
            
            Http::redirect('AdminLogin');
        }
        
        if(Auth::userLoggedIn()) {
            Http::redirect('AdminHome');
        }
        
        $this->constructHeader();
        
        $this->constructContent();
    }
    
    private function constructHeader() {
        $this->setDocType('xhtml_1.1');
        
        $this->addMetaTag('page_robots', 'name', 'robots', 'noindex');
        
        //Setup the css style
        $this->setThemeDirectory("{$this->module_assets_path}/styles/default");
        
        $this->addCssFile("{$this->assets_path}/css/reset.css", false);
        
        $this->addCssFile('main.css');
    }
    
    private function constructContent() {
        $login_box = new Div(array('id' => 'admin-login'));
        
        $login_form = new Form('admin-login-form');
        
        $login_form->addTextbox('user-name', 'Username');
        
        $login_form->addPassword('password', 'Password');
        
        $login_form->addSubmit('submit', 'Login');
        
        $login_form->setRequiredFields(array('user-name', 'password'));
        
        if($login_form->wasSubmitted() && $login_form->isValid()) {
            $login_credentials = $login_form->getData();
            
            if(Auth::adminLogin($login_credentials['user-name'], $login_credentials['password'])) {
                Http::redirect('AdminHome');
            }
            else {
                $login_form->addError('The specified username and password are invalid.');
            }
        }
        
        $login_box->addChild($login_form);
        
        $this->body->addChild($login_box);
    }
}